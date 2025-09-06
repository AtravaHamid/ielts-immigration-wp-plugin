<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_REST_API {
    public function __construct() {
        add_action('rest_api_init', [$this, 'routes']);
    }

    public function routes() : void {
        register_rest_route('examb/v1', '/progress', [
            'methods'  => 'POST',
            'callback' => [$this, 'save_progress'],
            'permission_callback' => function() {
                return is_user_logged_in() && current_user_can('read');
            }
        ]);

        register_rest_route('examb/v1', '/upload-audio', [
            'methods'  => 'POST',
            'callback' => [$this, 'upload_audio'],
            'permission_callback' => function() {
                return is_user_logged_in() && current_user_can('upload_files');
            }
        ]);
    }

    public function save_progress(\WP_REST_Request $req) : \WP_REST_Response {
        $user_id = get_current_user_id();
        $metrics = $req->get_param('metrics');
        if ( ! is_array($metrics) ) {
            return new \WP_REST_Response(['ok'=>false,'error'=>'bad_payload'], 400);
        }
        $logs = get_user_meta($user_id, '_examb_logs', true);
        if ( ! is_array($logs) ) $logs = [];
        $san = [];
        foreach((array)$metrics as $k=>$v){
            $san[$k] = is_scalar($v) ? sanitize_text_field((string)$v) : '';
        }
        $logs[] = [
            'ts' => current_time('mysql', true),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'metrics' => $san,
        ];
        update_user_meta($user_id, '_examb_logs', $logs);
        return new \WP_REST_Response(['ok'=>true]);
    }

    public function upload_audio(\WP_REST_Request $req) : \WP_REST_Response {
        $filename = sanitize_file_name( (string) $req->get_param('filename') ?: 'recording.webm' );
        $mime     = sanitize_mime_type( (string) $req->get_param('mime') ?: 'audio/webm' );
        $data_b64 = (string) $req->get_param('data');

        if ( ! $data_b64 ) return new \WP_REST_Response(['ok'=>false,'error'=>'no_data'], 400);

        $bin = base64_decode($data_b64);
        if ( ! $bin ) return new \WP_REST_Response(['ok'=>false,'error'=>'decode_failed'], 400);

        $tmp = wp_tempnam($filename);
        file_put_contents($tmp, $bin);

        $file = [
            'name'     => $filename,
            'type'     => $mime,
            'tmp_name' => $tmp,
            'error'    => 0,
            'size'     => filesize($tmp),
        ];

        if ( ! function_exists('media_handle_sideload') ) {
            require_once ABSPATH.'wp-admin/includes/file.php';
            require_once ABSPATH.'wp-admin/includes/media.php';
            require_once ABSPATH.'wp-admin/includes/image.php';
        }

        $overrides = ['test_form' => false, 'mimes' => [
            'webm'=>'audio/webm','wav'=>'audio/wav','ogg'=>'audio/ogg','mp3'=>'audio/mpeg'
        ]];

        $sideload = wp_handle_sideload($file, $overrides);
        if ( isset($sideload['error']) ) return new \WP_REST_Response(['ok'=>false,'error'=>$sideload['error']], 400);

        $url  = $sideload['url'];
        $type = $sideload['type'];
        $file_path = $sideload['file'];

        $attachment_id = wp_insert_attachment([
            'post_mime_type' => $type,
            'post_title'     => sanitize_text_field(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ], $file_path);

        require_once ABSPATH.'wp-admin/includes/image.php';
        wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $file_path));

        return new \WP_REST_Response(['ok'=>true, 'id'=>$attachment_id, 'url'=>$url]);
    }
}

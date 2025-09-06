<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'front']);
        add_action('admin_enqueue_scripts', [$this, 'admin']);
    }

    public function admin() {
        wp_register_style(
            'exam-board-admin',
            IELTS_MIGRATION_URL.'public/css/admin.css',
            [],
            IELTS_MIGRATION_VER
        );
    }

    public function front() {
        wp_register_style(
            'exam-board-front',
            IELTS_MIGRATION_URL.'public/css/board.css',
            [],
            IELTS_MIGRATION_VER
        );

        wp_register_script(
            'exam-board-front',
            IELTS_MIGRATION_URL.'public/js/board.js',
            [],
            IELTS_MIGRATION_VER,
            true
        );

        wp_localize_script('exam-board-front', 'ExamBoard', [
            'rest'  => [
                'url'   => esc_url_raw( rest_url('examb/v1/') ),
                'nonce' => wp_create_nonce('wp_rest'),
            ],
            'i18n'  => [
                'recording' => __('Recording...', 'ielts-migration'),
                'unsupported' => __('Not supported in this browser.', 'ielts-migration'),
                'uploading' => __('Uploading...', 'ielts-migration'),
                'saved' => __('Saved', 'ielts-migration'),
            ],
        ]);
    }
}

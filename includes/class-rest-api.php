<?php

require_once __DIR__ . '/class-telemetry.php';

/**
 * Register REST routes for the plugin.
 */
add_action( 'rest_api_init', function() {
    register_rest_route( 'ielts/v1', '/lessons', [
        'methods'  => 'GET',
        'callback' => function( WP_REST_Request $req ) {
            $level    = sanitize_text_field( $req->get_param( 'level' ) );
            $category = sanitize_text_field( $req->get_param( 'category' ) );
            $limit    = absint( $req->get_param( 'limit' ) ) ?: 12;
            $limit    = min( $limit, 50 );
            $args = [
                'post_type'      => 'ielts_lesson',
                'posts_per_page' => $limit,
                'post_status'    => 'publish',
                'meta_query'     => array_filter([
                    $level ? [ 'key' => '_ielts_level', 'value' => $level ] : null,
                    $category ? [ 'key' => '_ielts_category', 'value' => $category ] : null,
                ]),
            ];
            $query = new WP_Query( $args );
            $items = [];
            while ( $query->have_posts() ) {
                $query->the_post();
                $id      = get_the_ID();
                $items[] = [
                    'id'                => $id,
                    'title'             => get_the_title(),
                    'permalink'         => get_permalink(),
                    'excerpt'           => get_the_excerpt(),
                    'level'             => get_post_meta( $id, '_ielts_level', true ),
                    'category'          => get_post_meta( $id, '_ielts_category', true ),
                    'featured_image_url' => ielts_get_featured_image_url( $id ),
                ];
            }
            wp_reset_postdata();
            return rest_ensure_response( [ 'items' => $items ] );
        },
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'ielts/v1', '/session/start', [
        'methods'  => 'POST',
        'callback' => function( WP_REST_Request $req ) {
            $nonce = $req->get_header( 'X-WP-Nonce' );
            if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Invalid nonce', 'ielts-migration' ) ] );
            }
            if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
                return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Unauthorized', 'ielts-migration' ) ] );
            }
            $item_id = absint( $req->get_param( 'id' ) );
            $payload_json = get_post_meta( $item_id, '_payload_json', true );
            if ( empty( $payload_json ) ) {
                return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Invalid item', 'ielts-migration' ) ] );
            }
            $is_paid = get_post_meta( $item_id, '_item_paid', true );
            if ( $is_paid ) {
                $credits = (int) get_user_meta( get_current_user_id(), 'test_credits', true );
                if ( $credits < 1 ) {
                    return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Not enough credits', 'ielts-migration' ) ] );
                }
            }
            $payload = json_decode( $payload_json, true );
            return rest_ensure_response( [ 'ok' => true, 'data' => $payload ] );
        },
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'ielts/v1', '/session/answer', [
        'methods'  => 'POST',
        'callback' => function( WP_REST_Request $req ) {
            $nonce = $req->get_header( 'X-WP-Nonce' );
            if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Invalid nonce', 'ielts-migration' ) ] );
            }
            if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
                return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Unauthorized', 'ielts-migration' ) ] );
            }
            $item_id = absint( $req->get_param( 'id' ) );
            $answer  = sanitize_textarea_field( $req->get_param( 'answer' ) );
            update_user_meta( get_current_user_id(), 'ielts_answer_' . $item_id, $answer );
            return rest_ensure_response( [ 'ok' => true ] );
        },
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'ielts/v1', '/session/finish', [
        'methods'  => 'POST',
        'callback' => function( WP_REST_Request $req ) {
            $nonce = $req->get_header( 'X-WP-Nonce' );
            if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Invalid nonce', 'ielts-migration' ) ] );
            }
            if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
                return rest_ensure_response( [ 'ok' => false, 'error' => __( 'Unauthorized', 'ielts-migration' ) ] );
            }
            $item_id      = absint( $req->get_param( 'id' ) );
            $answer       = sanitize_textarea_field( $req->get_param( 'answer' ) );
            $type         = get_post_meta( $item_id, '_item_type', true );
            $payload_json = get_post_meta( $item_id, '_payload_json', true );
            $payload      = json_decode( $payload_json, true );
            $result       = [];
            if ( 'dictation' === $type && ! empty( $payload['text'] ) ) {
                $ref            = wp_strip_all_tags( $payload['text'] );
                $result['wer']  = ielts_calc_wer( $ref, $answer );
            } elseif ( 'writing' === $type ) {
                $result['word_count'] = str_word_count( $answer );
            }
            IELTS_Telemetry::save_stats( get_current_user_id(), $item_id, $result );
            delete_user_meta( get_current_user_id(), 'ielts_answer_' . $item_id );
            return rest_ensure_response( [ 'ok' => true, 'data' => $result ] );
        },
        'permission_callback' => '__return_true',
    ] );
} );

/**
 * Calculate word error rate.
 *
 * @param string $ref Reference text.
 * @param string $hyp Hypothesis text.
 *
 * @return float
 */
function ielts_calc_wer( string $ref, string $hyp ) : float {
    $r = preg_split( '/\s+/', trim( $ref ) );
    $h = preg_split( '/\s+/', trim( $hyp ) );
    $d = [];
    $rl = count( $r );
    $hl = count( $h );
    for ( $i = 0; $i <= $rl; $i++ ) {
        $d[ $i ][0] = $i;
    }
    for ( $j = 0; $j <= $hl; $j++ ) {
        $d[0][ $j ] = $j;
    }
    for ( $i = 1; $i <= $rl; $i++ ) {
        for ( $j = 1; $j <= $hl; $j++ ) {
            $cost        = ( $r[ $i - 1 ] === $h[ $j - 1 ] ) ? 0 : 1;
            $d[ $i ][ $j ] = min(
                $d[ $i - 1 ][ $j ] + 1,
                $d[ $i ][ $j - 1 ] + 1,
                $d[ $i - 1 ][ $j - 1 ] + $cost
            );
        }
    }
    return $rl ? $d[ $rl ][ $hl ] / $rl : 0.0;
}

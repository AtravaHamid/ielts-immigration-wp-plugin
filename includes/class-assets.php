<?php
/**
 * Manage plugin assets.
 */
class IELTS_Assets {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend' ] );
    }

    /**
     * Enqueue frontend assets when needed.
     */
    public function enqueue_frontend() : void {
        if ( ! is_singular() ) {
            return;
        }

        global $post;
        if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'ielts_lessons' ) ) {
            wp_enqueue_style(
                'ielts-frontend',
                IELTS_MIGRATION_URL . 'public/css/frontend.css',
                [],
                IELTS_MIGRATION_VER
            );
        }
    }
}

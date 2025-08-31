<?php
/**
 * Register custom post types for IELTS plugin.
 */
class IELTS_Custom_Posts {
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_types' ] );
    }

    /**
     * Register plugin post types.
     */
    public function register_post_types() : void {
        register_post_type( 'ielts_lesson', [
            'labels' => [
                'name'          => __( 'Lessons', 'ielts-migration' ),
                'singular_name' => __( 'Lesson', 'ielts-migration' ),
            ],
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'lesson' ],
            'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
            'menu_icon'    => 'dashicons-welcome-learn-more',
        ] );

        register_post_type( 'ielts_kit', [
            'labels' => [
                'name'          => __( 'Kits', 'ielts-migration' ),
                'singular_name' => __( 'Kit', 'ielts-migration' ),
            ],
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'kits' ],
            'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
            'menu_icon'    => 'dashicons-portfolio',
        ] );

        register_post_type( 'ielts_practice', [
            'labels' => [
                'name'          => __( 'Practices', 'ielts-migration' ),
                'singular_name' => __( 'Practice', 'ielts-migration' ),
            ],
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'practice' ],
            'supports'     => [ 'title', 'editor', 'custom-fields' ],
            'menu_icon'    => 'dashicons-edit',
        ] );
    }
}

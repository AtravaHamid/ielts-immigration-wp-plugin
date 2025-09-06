<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_Custom_Posts {
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_types' ] );
    }

    public function register_post_types() : void {
        // Lessons
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
            'show_in_menu' => 'ielts-toolkit', // نمایش زیر منوی IELTS
        ] );

        // Kits
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
            'show_in_menu' => 'ielts-toolkit', // نمایش زیر منوی IELTS
        ] );

        // Practices
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
            'show_in_menu' => 'ielts-toolkit', // نمایش زیر منوی IELTS
        ] );
    }
}

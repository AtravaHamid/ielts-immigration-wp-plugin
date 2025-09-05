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
                'name'          => __( 'Lessons', 'IELTS-IMMIGRATION' ),
                'singular_name' => __( 'Lesson', 'IELTS-IMMIGRATION' ),
            ],
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'lesson' ],
            'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
            'menu_icon'    => 'dashicons-welcome-learn-more',
            // Nest under the IELTS Immigration menu.
            'show_in_menu' => IELTS_Admin_Menu::MENU_SLUG,
        ] );

        register_post_type( 'ielts_kit', [
            'labels' => [
                'name'          => __( 'Kits', 'IELTS-IMMIGRATION' ),
                'singular_name' => __( 'Kit', 'IELTS-IMMIGRATION' ),
            ],
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'kits' ],
            'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
            'menu_icon'    => 'dashicons-portfolio',
            // Nest under the IELTS Immigration menu.
            'show_in_menu' => IELTS_Admin_Menu::MENU_SLUG,
        ] );

        register_post_type( 'ielts_practice', [
            'labels' => [
                'name'          => __( 'Practices', 'IELTS-IMMIGRATION' ),
                'singular_name' => __( 'Practice', 'IELTS-IMMIGRATION' ),
            ],
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'practice' ],
            'supports'     => [ 'title', 'editor', 'custom-fields' ],
            'menu_icon'    => 'dashicons-edit',
            // Nest under the IELTS Immigration menu.
            'show_in_menu' => IELTS_Admin_Menu::MENU_SLUG,
        ] );
    }
}

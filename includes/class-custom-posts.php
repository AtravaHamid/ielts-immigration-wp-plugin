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

        register_post_type( 'ielts_item', [
            'labels' => [
                'name'          => __( 'Items', 'ielts-migration' ),
                'singular_name' => __( 'Item', 'ielts-migration' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_rest' => true,
            'supports'     => [ 'title', 'custom-fields' ],
            'menu_icon'    => 'dashicons-media-text',
            'show_in_menu' => IELTS_Admin_Menu::MENU_SLUG,
        ] );

        register_post_meta( 'ielts_item', '_item_type', [
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'show_in_rest'      => true,
        ] );
        register_post_meta( 'ielts_item', '_item_lang_src', [
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'show_in_rest'      => true,
        ] );
        register_post_meta( 'ielts_item', '_item_lang_tgt', [
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'show_in_rest'      => true,
        ] );
        register_post_meta( 'ielts_item', '_payload_json', [
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'wp_kses_post',
            'show_in_rest'      => true,
        ] );
    }
}

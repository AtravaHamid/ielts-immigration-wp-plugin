<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_Custom_Posts {
    public function __construct() {
        add_action('init', [$this, 'register_post_types']);
    }

    public function register_post_types() : void {
        register_post_type('ielts_lesson', [
            'labels' => [
                'name' => __('Lessons', 'ielts-migration'),
                'singular_name' => __('Lesson', 'ielts-migration'),
                'add_new' => __('Add New Lesson', 'ielts-migration'),
                'add_new_item' => __('Add New Lesson', 'ielts-migration'),
                'edit_item' => __('Edit Lesson', 'ielts-migration'),
                'new_item' => __('New Lesson', 'ielts-migration'),
                'view_item' => __('View Lesson', 'ielts-migration'),
                'search_items' => __('Search Lessons', 'ielts-migration'),
            ],
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'lesson'],
            'supports' => ['title','editor','excerpt','thumbnail','custom-fields'],
            'menu_icon' => 'dashicons-welcome-learn-more',
            'show_in_menu' => IELTS_Admin_Menu::MENU_SLUG,
        ]);

        register_post_type('ielts_kit', [
            'labels' => [
                'name' => __('Kits', 'ielts-migration'),
                'singular_name' => __('Kit', 'ielts-migration'),
                'add_new' => __('Add New Kit', 'ielts-migration'),
                'add_new_item' => __('Add New Kit', 'ielts-migration'),
                'edit_item' => __('Edit Kit', 'ielts-migration'),
                'new_item' => __('New Kit', 'ielts-migration'),
                'view_item' => __('View Kit', 'ielts-migration'),
                'search_items' => __('Search Kits', 'ielts-migration'),
            ],
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'kits'],
            'supports' => ['title','editor','excerpt','thumbnail','custom-fields'],
            'menu_icon' => 'dashicons-portfolio',
            'show_in_menu' => IELTS_Admin_Menu::MENU_SLUG,
        ]);

        register_post_type('ielts_practice', [
            'labels' => [
                'name' => __('Practices', 'ielts-migration'),
                'singular_name' => __('Practice', 'ielts-migration'),
                'add_new' => __('Add New Practice', 'ielts-migration'),
                'add_new_item' => __('Add New Practice', 'ielts-migration'),
                'edit_item' => __('Edit Practice', 'ielts-migration'),
                'new_item' => __('New Practice', 'ielts-migration'),
                'view_item' => __('View Practice', 'ielts-migration'),
                'search_items' => __('Search Practices', 'ielts-migration'),
            ],
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'practice'],
            'supports' => ['title','editor','custom-fields'],
            'menu_icon' => 'dashicons-edit',
            'show_in_menu' => IELTS_Admin_Menu::MENU_SLUG,
        ]);
    }
}

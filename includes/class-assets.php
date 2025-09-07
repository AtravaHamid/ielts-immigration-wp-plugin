<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Assets registration.
 */
class IELTS_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'register_front']);
        add_action('admin_enqueue_scripts', [$this, 'register_admin']);
    }

    public function register_front() : void {
        wp_register_style(
            'examb-board',
            IELTS_MIGRATION_URL.'public/css/board.css',
            [],
            IELTS_MIGRATION_VER
        );
        wp_register_script(
            'examb-board',
            IELTS_MIGRATION_URL.'public/js/board.js',
            [],
            IELTS_MIGRATION_VER,
            true
        );
    }

    public function register_admin() : void {
        // در صورت نیاز CSS/JS ادمین را اینجا register کن
    }
}

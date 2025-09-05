<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'register_menu'));
    }

    public function register_menu() {
        add_menu_page(
            __('IELTS Toolkit', 'ielts-migration'), // عنوان منو
            __('IELTS', 'ielts-migration'),         // متن منو در سایدبار
            'manage_options',                       // دسترسی مورد نیاز
            'ielts-toolkit',                        // slug منو
            array($this, 'render_dashboard'),       // کال‌بک برای نمایش محتوا
            'dashicons-welcome-learn-more',         // آیکون منو
            5                                       // موقعیت منو
        );
    }

    public function render_dashboard() {
        echo '<div class="wrap"><h1>'.__('IELTS Toolkit Dashboard','ielts-migration').'</h1>';
        echo '<p>'.__('Welcome to your IELTS & Migration Toolkit!','ielts-migration').'</p></div>';
    }
}

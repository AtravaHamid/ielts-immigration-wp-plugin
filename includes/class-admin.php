<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_Admin_Menu {
    public const MENU_SLUG = 'exam_board';

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function register_menu() : void {
        add_menu_page(
            __('ExamBoard', 'ielts-migration'),
            __('ExamBoard', 'ielts-migration'),
            'edit_posts',
            self::MENU_SLUG,
            [$this, 'render_dashboard'],
            'dashicons-welcome-learn-more',
            26
        );
    }

    public function enqueue_assets(string $hook) : void {
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
        if ($page !== self::MENU_SLUG) return;

        wp_enqueue_style(
            'exam-board-admin',
            IELTS_MIGRATION_URL.'public/css/admin.css',
            [],
            IELTS_MIGRATION_VER
        );
    }

    public function render_dashboard() : void {
        echo '<div class="wrap">';
        echo '<h1>'.esc_html__('ExamBoard — IELTS & PTE', 'ielts-migration').'</h1>';
        echo '<p>'.esc_html__('Manage lessons, kits, and practices here. Use the submenu items to add or list content.', 'ielts-migration').'</p>';
        echo '<ul style="list-style:disc; margin-top:10px">';
        echo '<li><a href="'.esc_url( admin_url('edit.php?post_type=ielts_lesson') ).'">'.esc_html__('Lessons', 'ielts-migration').'</a></li>';
        echo '<li><a href="'.esc_url( admin_url('edit.php?post_type=ielts_kit') ).'">'.esc_html__('Kits', 'ielts-migration').'</a></li>';
        echo '<li><a href="'.esc_url( admin_url('edit.php?post_type=ielts_practice') ).'">'.esc_html__('Practices', 'ielts-migration').'</a></li>';
        echo '</ul>';
        echo '</div>';
    }
}

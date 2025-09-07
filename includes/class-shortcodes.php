<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Shortcodes for ExamBoard.
 */
class IELTS_Shortcodes {
    public function __construct() {
        add_action('init', [$this, 'register']);
    }

    public function register() : void {
        add_shortcode('exam_board', [$this, 'render_exam_board']);
        // (در صورت نیاز شورتکدهای قبلی را هم اینجا ثبت کن اما خروجی UI قدیمی را حذف کن)
    }

    public function render_exam_board($atts) : string {
        $atts = shortcode_atts([
            'lang' => 'en',
        ], $atts, 'exam_board');

        // حتماً هندل‌های جدید را enqueue کن
        wp_enqueue_style('examb-board');
        wp_enqueue_script('examb-board');

        // متغیرهای فرانت
        wp_localize_script('examb-board', 'ExamBoardVars', [
            'restUrl'   => esc_url_raw( rest_url() ),
            'nonce'     => wp_create_nonce('wp_rest'),
            'pluginUrl' => trailingslashit(IELTS_MIGRATION_URL),
            'lang'      => sanitize_text_field($atts['lang']),
        ]);

        // فقط کانتینر؛ UI را JS می‌سازد
        return '<div id="examb-board" data-examb-board="1"></div>';
    }
}

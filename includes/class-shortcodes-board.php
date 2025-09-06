<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * [ielts_board] — MVP برد لیسنینگ+تایپ
 * Usage:
 *   [ielts_board audio="https://example.com/a.mp3" title="Dictation 1" answer="this is a sample sentence" rewind="5" pause_on_type="1"]
 */
class IELTS_Board_Shortcode {
    public function __construct() {
        add_shortcode( 'ielts_board', [ $this, 'render' ] );
    }

    public function render( $atts = [] ) : string {
        $atts = shortcode_atts( [
            'audio'         => '',
            'title'         => __( 'IELTS Board', 'ielts-migration' ),
            'answer'        => '',        // مرجع اختیاری برای دیکته (برای محاسبه دقت)
            'rewind'        => 5,         // ثانیه برگشت
            'pause_on_type' => '1',       // هنگام تایپ، صوت متوقف شود
            'rtl'           => is_rtl() ? '1' : '0',
        ], $atts, 'ielts_board' );

        // enqueue فقط وقتی شورتکد رندر می‌شود
        wp_enqueue_style(
            'ielts-board',
            IELTS_MIGRATION_URL . 'public/css/board.css',
            [],
            IELTS_MIGRATION_VER
        );
        wp_enqueue_script(
            'ielts-board',
            IELTS_MIGRATION_URL . 'public/js/board.js',
            [],
            IELTS_MIGRATION_VER,
            true
        );

        $audio  = esc_url( $atts['audio'] );
        $title  = esc_html( $atts['title'] );
        $answer = wp_kses_post( $atts['answer'] );
        $rewind = (int) $atts['rewind'];
        $pause  = $atts['pause_on_type'] === '1' ? '1' : '0';
        $rtl    = $atts['rtl'] === '1' ? '1' : '0';

        ob_start();
        ?>
        <div class="ielts-board" data-rewind="<?php echo esc_attr($rewind); ?>"
             data-pause-on-type="<?php echo esc_attr($pause); ?>"
             data-rtl="<?php echo esc_attr($rtl); ?>">

            <div class="ielts-board__header">
                <h3><?php echo $title; ?></h3>
                <div class="ielts-board__timer" aria-live="polite">00:00</div>
            </div>

            <div class="ielts-board__audio">
                <?php if ( $audio ) : ?>
                    <audio class="ielts-board__player" src="<?php echo $audio; ?>" preload="metadata" controls></audio>
                    <button type="button" class="ielts-board__rewind" data-rewind="<?php echo esc_attr($rewind); ?>">
                        &#8634; <?php echo esc_html( sprintf( __('-%ds', 'ielts-migration'), $rewind ) ); ?>
                    </button>
                <?php else: ?>
                    <div class="ielts-board__notice">
                        <?php esc_html_e('No audio provided. Add audio="URL" to shortcode.', 'ielts-migration'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ielts-board__canvas">
                <textarea class="ielts-board__textarea" rows="10" placeholder="<?php esc_attr_e('Start typing here…','ielts-migration'); ?>"></textarea>
            </div>

            <div class="ielts-board__toolbar">
                <div class="ielts-board__stats">
                    <span class="ielts-board__words"><?php esc_html_e('Words','ielts-migration'); ?>: <b>0</b></span>
                    <span class="ielts-board__chars"><?php esc_html_e('Chars','ielts-migration'); ?>: <b>0</b></span>
                </div>
                <div class="ielts-board__actions">
                    <button type="button" class="ielts-board__check"><?php esc_html_e('Check','ielts-migration'); ?></button>
                    <button type="button" class="ielts-board__reset"><?php esc_html_e('Reset','ielts-migration'); ?></button>
                </div>
            </div>

            <div class="ielts-board__result" hidden>
                <div class="ielts-board__score"></div>
                <div class="ielts-board__diff"></div>
            </div>

            <?php if ( $answer !== '' ) : ?>
                <script type="application/json" class="ielts-board__answer">
                    <?php echo wp_json_encode( [ 'answer' => $answer ] ); ?>
                </script>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

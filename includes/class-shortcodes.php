<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_Shortcodes {
    public function __construct() {
        add_shortcode('exam_board', [$this, 'render_board']);
    }

    public function render_board($atts = []) : string {
        $atts = shortcode_atts(['lang' => 'en'], $atts, 'exam_board');

        wp_enqueue_style('exam-board-front');
        wp_enqueue_script('exam-board-front');

        ob_start(); ?>
        <div id="exam-board" class="eb-container" data-lang="<?php echo esc_attr($atts['lang']); ?>">
            <div class="eb-tabs">
                <button class="eb-tab is-active" data-target="typing">Typing</button>
                <button class="eb-tab" data-target="listen-type">Listen & Type</button>
                <button class="eb-tab" data-target="shadowing">Shadowing</button>
                <button class="eb-tab" data-target="describe-image">Describe Image</button>
                <button class="eb-tab" data-target="timer">Timer & Stats</button>
            </div>

            <div class="eb-panel is-active" id="eb-panel-typing">
                <div class="eb-row">
                    <textarea class="eb-target" placeholder="Paste or type target text here..."></textarea>
                    <textarea class="eb-input" placeholder="Start typing here..."></textarea>
                </div>
                <div class="eb-metrics">
                    <span>WPM: <b class="wpm">0</b></span>
                    <span>Accuracy: <b class="acc">100%</b></span>
                    <span>Errors: <b class="errs">0</b></span>
                </div>
                <div class="eb-actions">
                    <button class="eb-btn eb-reset">Reset</button>
                    <button class="eb-btn eb-save">Save Progress</button>
                </div>
            </div>

            <div class="eb-panel" id="eb-panel-listen-type">
                <div class="eb-row">
                    <textarea class="eb-tts-text" placeholder="Enter sentence(s) to play..."></textarea>
                </div>
                <div class="eb-actions">
                    <button class="eb-btn eb-tts-play">Play (TTS)</button>
                    <button class="eb-btn eb-tts-stop">Stop</button>
                </div>
                <div class="eb-row">
                    <textarea class="eb-input" placeholder="Type what you hear..."></textarea>
                </div>
            </div>

            <div class="eb-panel" id="eb-panel-shadowing">
                <div class="eb-row">
                    <textarea class="eb-tts-text" placeholder="Enter sentence(s) to shadow..."></textarea>
                </div>
                <div class="eb-actions">
                    <button class="eb-btn eb-tts-play">Play (TTS)</button>
                    <button class="eb-btn eb-rec-toggle">Record</button>
                    <button class="eb-btn eb-upload" disabled>Upload Recording</button>
                    <audio class="eb-playback" controls style="display:none;"></audio>
                </div>
                <small class="eb-hint">Tip: wear headphones to avoid echo.</small>
            </div>

            <div class="eb-panel" id="eb-panel-describe-image">
                <div class="eb-row eb-image-drop">
                    <input type="file" accept="image/*" class="eb-image-input" />
                    <div class="eb-drop-hint">Drop an image here or click to select</div>
                    <img class="eb-preview" style="display:none;max-width:100%;border-radius:8px;" />
                </div>
                <div class="eb-actions">
                    <button class="eb-btn eb-rec-toggle">Record Description</button>
                    <button class="eb-btn eb-upload" disabled>Upload Recording</button>
                    <audio class="eb-playback" controls style="display:none;"></audio>
                </div>
            </div>

            <div class="eb-panel" id="eb-panel-timer">
                <div class="eb-timer">
                    <input type="number" min="1" max="180" value="25" class="eb-minutes" /> <span>minutes</span>
                    <button class="eb-btn eb-timer-start">Start</button>
                    <button class="eb-btn eb-timer-stop" disabled>Stop</button>
                    <span class="eb-countdown">00:00</span>
                </div>
                <div class="eb-metrics">
                    <span>Total Words: <b class="words">0</b></span>
                    <span>Total Errors: <b class="errors">0</b></span>
                    <span>Listening mins: <b class="listen-mins">0</b></span>
                </div>
                <button class="eb-btn eb-save eb-save-session">Save Session</button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

<?php
/**
 * Shortcodes for IELTS plugin.
 */
class IELTS_Shortcodes {
    public function __construct() {
        add_shortcode( 'ielts_lessons', [ $this, 'render_lessons' ] );
    }

    /**
     * Render lessons grid.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_lessons( $atts ) : string {
        $atts = shortcode_atts(
            [
                'level'    => '',
                'category' => '',
                'limit'    => 12,
            ],
            $atts,
            'ielts_lessons'
        );

        $level    = sanitize_text_field( $atts['level'] );
        $category = sanitize_text_field( $atts['category'] );
        $limit    = (int) $atts['limit'];

        $meta_query = array_filter(
            [
                $level ? [ 'key' => '_ielts_level', 'value' => $level ] : null,
                $category ? [ 'key' => '_ielts_category', 'value' => $category ] : null,
            ]
        );

        $q = new WP_Query(
            [
                'post_type'      => 'ielts_lesson',
                'posts_per_page' => $limit,
                'meta_query'     => array_values( $meta_query ),
            ]
        );

        ob_start();
        echo '<div class="ielts-grid">';
        while ( $q->have_posts() ) {
            $q->the_post();
            ielts_get_template( 'shortcode-lessons.php', [ 'id' => get_the_ID() ] );
        }
        echo '</div>';
        wp_reset_postdata();

        return ob_get_clean();
    }
}

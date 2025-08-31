<?php
// Shortcodes for IELTS plugin
add_action('init', function () {
  add_shortcode('ielts_lessons', function($atts){
    $atts = shortcode_atts([
      'level'    => '',
      'category' => '',
      'limit'    => 12
    ], $atts, 'ielts_lessons');

    ob_start();

    $q = new WP_Query([
      'post_type'      => 'ielts_lesson',
      'posts_per_page' => (int)$atts['limit'],
      'meta_query'     => array_values(array_filter([
        $atts['level'] ? [
          'key'   => '_ielts_level',
          'value' => sanitize_text_field($atts['level'])
        ] : null,
        $atts['category'] ? [
          'key'   => '_ielts_category',
          'value' => sanitize_text_field($atts['category'])
        ] : null,
      ]))
    ]);

    echo '<div class="ielts-grid">';
    while ($q->have_posts()) { $q->the_post();
      ielts_get_template('shortcode-lessons.php', ['id' => get_the_ID()]);
    }
    echo '</div>';
    wp_reset_postdata();

    return ob_get_clean();
  });
});

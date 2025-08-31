<?php
/**
 * Register REST route for lesson queries.
 */
add_action('rest_api_init', function(){
  register_rest_route('ielts/v1', '/lessons', [
    'methods'  => 'GET',
    'callback' => function(WP_REST_Request $req){
      $level    = sanitize_text_field($req->get_param('level'));
      $category = sanitize_text_field($req->get_param('category'));
      $limit    = absint($req->get_param('limit')) ?: 12;
      $limit    = min($limit, 50);
      $args = [
        'post_type'      => 'ielts_lesson',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'meta_query'     => array_filter([
          $level ? ['key' => '_ielts_level', 'value' => $level] : null,
          $category ? ['key' => '_ielts_category', 'value' => $category] : null,
        ])
      ];
      $query = new WP_Query($args);
      $items = [];
      while($query->have_posts()){ $query->the_post();
        $id = get_the_ID();
        $items[] = [
          'id' => $id,
          'title' => get_the_title(),
          'permalink' => get_permalink(),
          'excerpt' => get_the_excerpt(),
          'level' => get_post_meta($id, '_ielts_level', true),
          'category' => get_post_meta($id, '_ielts_category', true),
          'featured_image_url' => ielts_get_featured_image_url($id),
        ];
      }
      wp_reset_postdata();
      return rest_ensure_response(['items' => $items]);
    },
    'permission_callback' => '__return_true'
  ]);
});

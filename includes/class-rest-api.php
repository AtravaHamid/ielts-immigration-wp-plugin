<?php
add_action('rest_api_init', function(){
  register_rest_route('ielts/v1','/lessons', [
    'methods'  => 'GET',
    'callback' => function(WP_REST_Request $req){
      $level = sanitize_text_field($req->get_param('level') ?? '');
      $category = sanitize_text_field($req->get_param('category') ?? '');
      $args = [
        'post_type' => 'ielts_lesson',
        'posts_per_page' => 20,
        'meta_query' => array_filter([
          $level ? ['key'=>'_ielts_level','value'=>$level] : null,
          $category ? ['key'=>'_ielts_category','value'=>$category] : null,
        ])
      ];
      $q = new WP_Query($args);
      $items = [];
      while($q->have_posts()){ $q->the_post();
        $items[] = [
          'id' => get_the_ID(),
          'title' => get_the_title(),
          'permalink' => get_permalink(),
          'excerpt' => get_the_excerpt(),
          'level' => get_post_meta(get_the_ID(),'_ielts_level',true),
          'category' => get_post_meta(get_the_ID(),'_ielts_category',true),
        ];
      }
      wp_reset_postdata();
      return rest_ensure_response(['items'=>$items]);
    },
    'permission_callback' => '__return_true'
  ]);
});

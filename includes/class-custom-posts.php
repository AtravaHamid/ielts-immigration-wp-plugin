<?php
add_action('init', function(){
  register_post_type('ielts_lesson', [
    'label' => __('Lessons','ielts-migration'),
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'lesson'],
    'supports' => ['title','editor','excerpt','thumbnail','custom-fields'],
    'menu_icon' => 'dashicons-welcome-learn-more',
  ]);
  register_post_type('ielts_kit', [
    'label' => __('Kits','ielts-migration'),
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'kits'],
    'supports' => ['title','editor','excerpt','thumbnail','custom-fields'],
    'menu_icon' => 'dashicons-portfolio',
  ]);
  register_post_type('ielts_practice', [
    'label' => __('Practices','ielts-migration'),
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'practice'],
    'supports' => ['title','editor','custom-fields'],
    'menu_icon' => 'dashicons-edit',
  ]);
});

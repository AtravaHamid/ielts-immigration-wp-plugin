<?php
function ielts_log($msg){
  if (WP_DEBUG === true) error_log('[IELTS] '.(is_string($msg)?$msg:print_r($msg,true)));
}
function ielts_get_template($file, $args = []){
  $theme_path  = get_stylesheet_directory().'/ielts/'.$file;
  $plugin_path = IELTS_MIGRATION_DIR.'templates/'.$file;
  $path = file_exists($theme_path) ? $theme_path : $plugin_path;
  if (!empty($args)) extract($args);
  include $path;
}

/**
 * Get the URL of a post's featured image.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return string Image URL or empty string.
 */
function ielts_get_featured_image_url($post_id, $size = 'full'){
  $post_id  = absint($post_id);
  $thumb_id = get_post_thumbnail_id($post_id);
  if(!$thumb_id) return '';
  $img = wp_get_attachment_image_src($thumb_id, $size);
  return $img ? $img[0] : '';
}

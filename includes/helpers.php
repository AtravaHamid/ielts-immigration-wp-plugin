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

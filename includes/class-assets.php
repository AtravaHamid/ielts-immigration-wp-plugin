<?php
add_action('wp_enqueue_scripts', function(){
  // فقط در صفحات لازم بارگذاری شود؛ بعداً شرط بگذار
  wp_register_style('ielts-frontend', IELTS_MIGRATION_URL.'public/css/frontend.css', [], IELTS_MIGRATION_VER);
  wp_register_script('ielts-frontend', IELTS_MIGRATION_URL.'public/js/frontend.js', ['jquery'], IELTS_MIGRATION_VER, true);
});

<?php
add_action('woocommerce_order_status_completed', function($order_id){
  $order = wc_get_order($order_id);
  foreach ($order->get_items() as $item) {
    $product_id = $item->get_product_id();
    $kit_ref = get_post_meta($product_id, '_ielts_kit_ref', true);
    if ($kit_ref) {
      $user_id = $order->get_user_id();
      $granted = get_user_meta($user_id, '_ielts_kits', true) ?: [];
      $granted = array_unique(array_merge($granted, [(int)$kit_ref]));
      update_user_meta($user_id, '_ielts_kits', $granted);
    }
  }
});

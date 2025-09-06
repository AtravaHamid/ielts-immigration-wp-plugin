<?php
/**
 * Plugin Name: ExamBoard (IELTS & PTE)
 * Description: بُرد تمرین و آزمون برای IELTS/PTE: درس‌ها، کیت‌ها، تمرین‌ها، شورتکدها و REST. سازگار با Woo/Elementor/WPBakery/WPML/Polylang.
 * Version: 0.2.0
 * Author: IELTS & Immigration
 * Text Domain: ielts-migration
 */

if ( ! defined('ABSPATH') ) exit;

define('IELTS_MIGRATION_VER', '0.2.0');
define('IELTS_MIGRATION_DIR', plugin_dir_path(__FILE__));
define('IELTS_MIGRATION_URL', plugin_dir_url(__FILE__));

/** Includes — ترتیب مهم است: اول Admin، بعد CPT که به Admin ارجاع می‌دهد */
require_once IELTS_MIGRATION_DIR.'includes/helpers.php';
require_once IELTS_MIGRATION_DIR.'includes/class-assets.php';
require_once IELTS_MIGRATION_DIR.'includes/class-admin.php';
require_once IELTS_MIGRATION_DIR.'includes/class-custom-posts.php';
require_once IELTS_MIGRATION_DIR.'includes/class-shortcodes.php';
require_once IELTS_MIGRATION_DIR.'includes/class-rest-api.php';
require_once IELTS_MIGRATION_DIR.'includes/class-purchases.php';

/** Bootstrap */
new IELTS_Assets();
new IELTS_Admin_Menu();     // ← والد منو
new IELTS_Custom_Posts();   // ← CPTها زیر منوی والد قرار می‌گیرند
new IELTS_Shortcodes();
new IELTS_REST_API();

/** Integrations (اختیاری) */
foreach ([
    'integrations/woocommerce.php',
    'integrations/seo.php',
    'integrations/wpbakery.php',
    'integrations/elementor/plugin.php'
] as $rel) {
    $path = IELTS_MIGRATION_DIR.$rel;
    if ( file_exists($path) ) require_once $path;
}

/** Rewrite flush در فعال/غیرفعال شدن */
register_activation_hook(__FILE__, function() {
    // ثبت CPT ها قبل از flush
    (new IELTS_Custom_Posts())->register_post_types();
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

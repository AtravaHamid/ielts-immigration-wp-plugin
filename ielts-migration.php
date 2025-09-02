<?php
/**
 * Plugin Name: IELTS & Migration Toolkit
 * Description: مدیریت درس‌ها/کیت‌ها/تمرین‌ها + REST + پرداخت/لایسنس (سازگار با Woo، Elementor، WPBakery، WPML/Polylang)
 * Version: 0.1.0
 * Author: IELTS & Immigration
 * Text Domain: ielts-migration
 */

if ( ! defined('ABSPATH') ) exit;

define('IELTS_MIGRATION_VER', '0.1.0');
define('IELTS_MIGRATION_DIR', plugin_dir_path(__FILE__));
define('IELTS_MIGRATION_URL', plugin_dir_url(__FILE__));

require_once IELTS_MIGRATION_DIR.'includes/helpers.php';
require_once IELTS_MIGRATION_DIR.'includes/class-assets.php';
require_once IELTS_MIGRATION_DIR.'includes/class-custom-posts.php';
require_once IELTS_MIGRATION_DIR.'includes/class-shortcodes.php';
require_once IELTS_MIGRATION_DIR.'includes/class-shortcodes-home.php';
require_once IELTS_MIGRATION_DIR.'includes/class-rest-api.php';
require_once IELTS_MIGRATION_DIR.'includes/class-purchases.php';

new IELTS_Assets();
new IELTS_Custom_Posts();
new IELTS_Shortcodes();
new IELTS_Shortcodes_Home();

if ( file_exists(IELTS_MIGRATION_DIR.'integrations/woocommerce.php') ) require_once IELTS_MIGRATION_DIR.'integrations/woocommerce.php';
if ( file_exists(IELTS_MIGRATION_DIR.'integrations/seo.php') )          require_once IELTS_MIGRATION_DIR.'integrations/seo.php';
if ( file_exists(IELTS_MIGRATION_DIR.'integrations/wpbakery.php') )     require_once IELTS_MIGRATION_DIR.'integrations/wpbakery.php';
if ( file_exists(IELTS_MIGRATION_DIR.'integrations/elementor/plugin.php') ) require_once IELTS_MIGRATION_DIR.'integrations/elementor/plugin.php';

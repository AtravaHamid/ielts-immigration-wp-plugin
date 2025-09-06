<?php
/**
 * Plugin Name: IELTS & Migration Toolkit
 * Description: مدیریت درس‌ها/کیت‌ها/تمرین‌ها + REST + پرداخت/لایسنس (سازگار با Woo، Elementor، WPBakery، WPML/Polylang)
 * Version: 0.1.0
 * Author: IELTS & Immigration
 * Text Domain: ielts-migration
 */

if ( ! defined('ABSPATH') ) exit;

// -----------------------------------------------------------------------------
// Constants
// -----------------------------------------------------------------------------
if ( ! defined('IELTS_MIGRATION_VER') ) define('IELTS_MIGRATION_VER', '0.1.0');
if ( ! defined('IELTS_MIGRATION_DIR') ) define('IELTS_MIGRATION_DIR', plugin_dir_path(__FILE__));
if ( ! defined('IELTS_MIGRATION_URL') ) define('IELTS_MIGRATION_URL', plugin_dir_url(__FILE__));

// -----------------------------------------------------------------------------
// Requires (order matters)
// -----------------------------------------------------------------------------
require_once IELTS_MIGRATION_DIR . 'includes/helpers.php';
require_once IELTS_MIGRATION_DIR . 'includes/class-assets.php';
require_once IELTS_MIGRATION_DIR . 'includes/class-custom-posts.php';
require_once IELTS_MIGRATION_DIR . 'includes/class-shortcodes.php';
require_once IELTS_MIGRATION_DIR . 'includes/class-rest-api.php';
require_once IELTS_MIGRATION_DIR . 'includes/class-purchases.php';

// Admin (safe require)
if ( file_exists( IELTS_MIGRATION_DIR . 'includes/class-admin.php' ) ) {
	require_once IELTS_MIGRATION_DIR . 'includes/class-admin.php';
}
new IELTS_Admin();
new IELTS_Custom_Posts();

// Optional integrations
if ( file_exists( IELTS_MIGRATION_DIR . 'integrations/woocommerce.php' ) ) require_once IELTS_MIGRATION_DIR . 'integrations/woocommerce.php';
if ( file_exists( IELTS_MIGRATION_DIR . 'integrations/seo.php' ) )          require_once IELTS_MIGRATION_DIR . 'integrations/seo.php';
if ( file_exists( IELTS_MIGRATION_DIR . 'integrations/wpbakery.php' ) )     require_once IELTS_MIGRATION_DIR . 'integrations/wpbakery.php';
if ( file_exists( IELTS_MIGRATION_DIR . 'integrations/elementor/plugin.php' ) ) require_once IELTS_MIGRATION_DIR . 'integrations/elementor/plugin.php';

// -----------------------------------------------------------------------------
// Bootstrap classes (guard with class_exists to avoid fatals)
// -----------------------------------------------------------------------------
add_action('plugins_loaded', function () {
	if ( class_exists('IELTS_Assets') )        new IELTS_Assets();
	if ( class_exists('IELTS_Custom_Posts') )  new IELTS_Custom_Posts();
	if ( class_exists('IELTS_Shortcodes') )    new IELTS_Shortcodes();
	if ( class_exists('IELTS_Admin') )         new IELTS_Admin();
});

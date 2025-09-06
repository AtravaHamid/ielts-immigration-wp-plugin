<?php
if ( ! defined('ABSPATH') ) exit;

class IELTS_Admin {
	public function __construct() {
		add_action('admin_menu', [$this, 'menu']);
	}

	public function menu() {
		// فقط منوی والد
		add_menu_page(
			__('IELTS & Migration','ielts-migration'),
			__('IELTS','ielts-migration'),
			'edit_posts',
			'ielts-toolkit',
			[$this, 'render'],
			'dashicons-welcome-learn-more',
			26
		);
	}

	public function render() {
		echo '<div class="wrap">';
		echo '<h1>'.esc_html__('IELTS & Migration Toolkit','ielts-migration').'</h1>';
		echo '<p>'.esc_html__('Welcome to the admin panel.','ielts-migration').'</p>';
		echo '</div>';
	}
}

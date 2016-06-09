<?php
// Settings page under Company Post Type

class CompanySettingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_company_files' ) );
	}

	function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=company',
			'Company Settings',
			'Settings',
			'manage_options',
			'company-settings',
			array(
				$this,
				'company_settings_do_page'
			)
		);
	}
	
	function enqueue_company_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . '/css/company-settings.css' );
	}

	function  company_settings_do_page() {
		echo 'This is the page content';
	}
}

new CompanySettingsPage;
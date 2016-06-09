<?php
// Settings page under Company Post Type

/*add_action( 'admin_init', 'company_options_init' );
add_action( 'admin_menu', 'company_options_add_page' );

function company_options_init() {
	register_setting( 'company_settings', 'company_page_options');
}

function company_options_add_page() {
	$company_page = add_submenu_page(
        'edit.php?post_type=company',
        __( 'Settings', 'tbb_company' ),
        __( 'Settings', 'texttbb_companydomain' ),
        'manage_options',
        'company-settings',
        'company_page_options_do_page'
    );
	add_action( 'load-' . $landing_page, 'load_company_files' );
}

function load_company_files() {
	add_action( 'admin_enqueue_scripts', 'enqueue_company_files' );
}

function enqueue_landing_files() {
	wp_enqueue_style( 'landing-css', plugin_dir_url( __FILE__ ) . 'css/company.css' );
	//wp_enqueue_media();
	//wp_enqueue_script( 'landing-js', plugin_dir_url( __FILE__ ) . 'js/company-page.js', array('jquery'), 1.0, true );
}

function company_page_options_do_page() {
	
}*/


class CompanySettingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_company_files' ) );
	}

	function admin_menu() {
		add_options_page(
			'Company Settings',
			'Settings',
			'manage_options',
			'company-settings',
			array(
				$this,
				'company_settings_page'
			)
		);
	}
	
	function enqueue_company_files() {
		wp_enqueue_style( 'company-css', TBB_FUNCTIONS_DIR . '/css/company-settings.css' );
	}

	function  company_settings_page() {
		echo 'This is the page content';
	}
}

new CompanySettingsPage;
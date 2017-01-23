<?php

if( ! defined( 'ABSPATH' ) )
	exit;

if( defined( 'RETS_Office' ) )
	return;

class RETS_Office {
	
	const SLUG = 'rets-offices';
	
	public $filters = array();

	public $errors = array();
	
	public function __construct(){
		add_action( 'plugins_loaded', array( $this, '_init' ) );
	}
	
	public function _init() {
		// Enable Basic Authentication for Dev Site
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2);
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		
		add_action( 'admin_menu', array( $this, 'admin_menu_settings_page' ) );
		
	}
	
	function get_setting( $setting = false, $default = null ){
		$settings = get_option( '_' . self::SLUG . '-settings' );
		if( ! $setting )
			return $settings;

		if( ! isset( $settings[ $setting ] ) )
			return $default !== null ? $default : false;

		return $settings[ $setting ];
	}
	
	public function admin_assets(){
		wp_register_script( 'rets-admin', TBB_FUNCTIONS_URL . '/assets/js/rets-admin.js', array( 'jquery' ), TBB_VERSION );
		wp_register_style( 'rets-admin', TBB_FUNCTIONS_URL . '/assets/css/rets-admin.css', array(), TBB_VERSION );

		wp_localize_script( 'rets-sync-admin', 'rets_sync_vars', array(
			'ajax_url' => admin_url() . 'admin-ajax.php'
		));

		$screen = get_current_screen();

		if( $screen->post_type == 'office' || $screen->id == 'toplevel_page_rets-office' ){
			wp_enqueue_script( 'rets-admin');
			wp_enqueue_style( 'rets-admin');
		}
	}
	
	public function admin_menu_settings_page(){
		$hook = add_menu_page(
			__( 'RETS Offices', 'rets-offices' ),		// page_title
			__( 'RETS Offices', 'rets-offices' ),		// menu_title
			'manage_options',						// capabilities
			self::SLUG,								// menu_slug
			array( $this, 'plugin_page' ),			// function
			'dashicons-cloud',						// icon_url
			37										// position
		);

		//add_action( "load-$hook", array( $this, 'screen_option' ) );
	}
	
	public function plugin_page(){
		$this->save_settings();

		$view_vars = array(
			'plugin_base_url' => admin_url( 'admin.php?page=' . self::SLUG ),

			'nonce' => wp_nonce_field( self::SLUG . '-settings', '_wpnonce', true, false ),
			'key' => '_' . self::SLUG . '-settings',
		);

		echo $this->load_view( 'rets-offices-settings', $view_vars );
	}
	
	public function load_view( $_file, $_vars = array() ){
		// it must end in .php
		if( substr( $_file, -4 ) !== '.php' )
			$_file .= '.php';

		// force views into the "views/" folder
		if( substr( $_file, 0, 6 ) !== 'views/' )
			$_file = 'views/' . $_file;

		$_view = RETSSYNC_PATH  . $_file;
		if( ! file_exists( $_view ) )
			return false;

		if( is_array( $_vars ) )
			extract( $_vars );

		ob_start();
		include( $_view );
		return ob_get_clean();
	}
	
	function save_settings(){
		$key = '_' . self::SLUG . '-settings';

		if ( ! isset( $_POST[ $key ] ) || ! isset( $_POST['_wpnonce'] ) )
			return;

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], self::SLUG . '-settings' ) )
			return;

		$new_settings = $_POST[ $key ];

		update_option( $key, $new_settings );

	}
	
	function add_filter( $key, $value, $operator = '=', $prefix = 'field_' ){
		$this->filters[ $prefix . $key ] = array( $value, $operator );
	}

	function clear_filters(){
		$this->filters = array();
	}

	function get_filters(){
		$filters = '';
		foreach( $this->filters as $key => $params ){
			$filter = $filters ? ' AND `%s` ' . $params[1] . ' %s' : '`%s` ' . $params[1] . ' %s';
			$filter = sprintf( $filter, $key, '%s' );
			$filters .= $this->db->prepare( $filter, $params[0] );
		}
		return $filters;
	}
	
}
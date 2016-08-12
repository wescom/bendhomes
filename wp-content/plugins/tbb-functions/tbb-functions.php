<?php
/*
Plugin Name: Wescom Custom Functions
Plugin URI: mailto:jculley@bendbulletin.com
Description: Custom functions for Bend Homes.  Includes "Companies" custom post type and Settings page to create companies based on Agents brokerage office fields. Also includes additional shortcodes and cleans up the wordpress admin area.
Version: 1.0
Author: Jarel Culley
Author URI: http://www.bendbulletin.com
*/


/**
 * Upon activation of the plugin, see if we are running the required version and deploy theme in defined.
 */
function tbb_custom_functions_activation() {
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( __FILE__  );
        wp_die( __('WordPress 3.0 and higher required. The plugin has now disabled itself. On a side note why are you running an old version :( Upgrade!','index') );
    }
}

/* Constants */
define('TBB_FUNCTIONS_URL', plugin_dir_url(__FILE__));
define('TBB_FUNCTIONS_DIR', plugin_dir_path(__FILE__));


/***** 
 * Include necessary files
*****/
require_once('admin/dashboard_widget.php');
require_once('admin/functions.php');
require_once('admin/pages-metabox.php');
require_once('post-types/post-type-company.php');
require_once('admin/settings-company.php');
require_once('admin/settings-agents.php');
require_once('tbb-shortcodes.php');


// Enqueue Additional Files
add_action( 'wp_enqueue_scripts', 'tbb_enqueue_additional_files');
function tbb_enqueue_additional_files() {
	if (!is_admin() && $GLOBALS['pagenow'] != 'wp-login.php') {
		wp_deregister_script('jquery');
		wp_deregister_script('jquery-ui-core');
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', false, '1.11.3');
		wp_register_script('jquery-ui-core', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js', array('jquery'), '1.11.4', true);
		//wp_register_script('jquery-cookie', TBB_FUNCTIONS_URL .'js/jquery.cookie.min.js', array('jquery'), '', true);
        wp_enqueue_script('jquery');	
		wp_enqueue_script('jquery-ui-core');
		//wp_enqueue_script('jquery-cookie');	
	}
	//wp_enqueue_script("mobile-check", TBB_FUNCTIONS_URL . "/js/mobile-check.js", array("jquery"));
}


// Disable stupid emojicons scripts wordpress adds by default into the header.
add_action( 'init', 'disable_wp_emojicons' );
function disable_wp_emojicons() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) { return array_diff( $plugins, array( 'wpemoji' ) ); } else { return array(); }
}


// Filter to only search Agents by name, ie post_title.
add_filter( 'posts_search', 'tbb_search_by_title_only', 500, 2 );
function tbb_search_by_title_only( $search, &$wp_query ) {

	$type = '';
	if( isset( $_GET['post_type'] ) ) $type = $_GET['post_type'];
	
    if( $type == 'agent' ) {

		if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
			global $wpdb;
	
			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';
	
			$search = array();
	
			foreach ( ( array ) $q['search_terms'] as $term )
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
	
			if ( !is_user_logged_in() )
				$search[] = "$wpdb->posts.post_password = ''";
	
			$search = ' AND ' . implode( ' AND ', $search );
		}
	}
	
	return $search;
}


// Only show Mailchimp newsletter popup if user is not logged in or on the login page.
add_filter( 'popmake_popup_is_loadable', 'tbb_popup_not_logged_in', 10, 2 );
function tbb_popup_not_logged_in( $is_loadable, $popup_id ) {
	if( $popup_id == 292579 ) {	
		if( !is_user_logged_in() || !is_page( 'login-or-register' ) ) return;	
		//return ! is_user_logged_in();
		//return ! is_page( 'login-or-register' );
	}
	return $is_loadable;
}
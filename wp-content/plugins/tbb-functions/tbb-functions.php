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
		wp_register_script('jquery-cookie', TBB_FUNCTIONS_URL .'js/jquery.cookie.min.js', array('jquery'), '', true);
        wp_enqueue_script('jquery');	
		wp_enqueue_script('jquery-ui-core');
	}
	//wp_enqueue_script("mobile-check", TBB_FUNCTIONS_URL . "/js/mobile-check.js", array("jquery"));
}


// Remove admin color scheme picker from user profile
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
// Remove wordpress Welcome panel
remove_action('welcome_panel', 'wp_welcome_panel');


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


// Remove unneccesary menus from Admins (not Super Admins)
add_action( 'admin_init', 'tbb_remove_admin_menus', 999 );
function tbb_remove_admin_menus() {
	if(!current_user_can( 'manage_network' )) {
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'users.php' );
		remove_menu_page( 'aiowpsec' );
		remove_menu_page( 'gadash_settings' );
		remove_menu_page( 'wpseo_dashboard' );
		remove_menu_page( 'edit.php?post_type=acf' );
		remove_submenu_page( 'options-general.php', 'options-writing.php' );
		remove_submenu_page( 'options-general.php', 'options-reading.php' );
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		remove_submenu_page( 'options-general.php', 'options-media.php' );
		remove_submenu_page( 'options-general.php', 'options-permalink.php' );
		remove_submenu_page( 'themes.php', 'optionsframework' );
		remove_submenu_page( 'themes.php', 'switch_themes' );
		remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2F' );
		remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2Findex.php' );
		remove_submenu_page( 'themes.php', 'customize.php' );
		remove_submenu_page( 'themes.php', 'multiple_sidebars' );
	}
	//remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2F' );
	//remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2Findex.php' );
	//remove_submenu_page( 'themes.php', 'customize.php' );
}


add_action( 'after_setup_theme','tbb_remove_header_and_background_menus', 100 );
function tbb_remove_header_and_background_menus() {
	remove_theme_support( 'custom-background' );
	remove_theme_support( 'custom-header' );
}


// Clean up admin bar 
add_action( 'wp_before_admin_bar_render', 'tbb_remove_adminbar_links', 100 ); 
function tbb_remove_adminbar_links() {  
    global $wp_admin_bar;  
    $wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_node('customize');
	$wp_admin_bar->remove_menu('themes');
	$wp_admin_bar->remove_menu('updates');
	$wp_admin_bar->remove_menu('new-content');
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('wpseo-menu');
}


// Custom login styles on /wp-admin page in case anyone accesses it
add_action('login_head',  'tbb_custom_login_logo');
function tbb_custom_login_logo() {
	echo '
	<style  type="text/css"> 
		body.login { background: #e8e8e8; }
		#login { width: 420px; }
		.login h1 { display: none; }
		body.login form { margin-top: 0; border-radius: 0; background: #423144; }
		body.login #login form p { color: #fff; }
		body.login > div > p { margin: 0 !important; background: #fff; padding: 1em 1.25em !important; font-size: 15px !important; }
		body.login .submit .button.button-primary { font-size: 15px; display: block; width: 100%; height: auto; padding: .75em 0; line-height: 150%; background: #2FCC41; border-color: #2FCC41; }
		body.login .submit .button.button-primary:hover, body.login .submit .button.button-primary:active { background: #3276B1; border-color: #3276B1; }
		body.login form .forgetmenot { margin: 0 0 10px !important; }
		body.login label { color: #fff; font-size: 16px; letter-spacing: .05em; font-weight: 100; position: relative; }
		body.login label[for=user_login]:before { font-family: dashicons; content: "\f110"; color: #9E9E9C; font-size: 22px; position: absolute; top: 31px; z-index: 2; left: 10px; }
		body.login label[for=user_pass]:before { font-family: dashicons; content: "\f112"; color: #9E9E9C; font-size: 22px; position: absolute; top: 31px; z-index: 2; left: 10px; }
		.login form .input, .login input[type=text] { padding: 4px 15px 4px 40px; }
		div.aiowps-captcha-equation { color: #fff; }
		input#aiowps-captcha-answer { width: 70px; margin-left: 10px; padding: 2px 10px; }
	</style>
	';
}


// Admin footer modification
add_filter('admin_footer_text', 'tbb_custom_footer_admin');
function tbb_custom_footer_admin () {
    echo '<span id="footer-thankyou">Site Powered by <a href="http://www.bendbulletin.com" target="_blank">The Bend Bulletin</a></span>';
}


// Remove the Register link from the wp-login.php script
add_filter('option_users_can_register', function($value) {
    $script = basename(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));
    if ($script == 'wp-login.php') {
        $value = false;
    }
    return $value;
});


// Add Product URL column to Manufacturers admin list
add_filter('manage_edit-agent_columns','create_agent_admin_column');
function create_agent_admin_column($columns) {
	$new_column = array(
		'brokerage' => __( 'Brokerage' )
	);
	return array_merge($columns, $new_column);
}

// Populate Brokerage column with agent office name meta value
add_action('manage_agent_posts_custom_column', 'populate_agent_admin_column', 10, 2);
function populate_agent_admin_column($column_name, $term_id) {
	if($column_name == 'brokerage') {
		$brokerage = get_post_meta($term_id, 'brk_office_name', true);
		echo $brokerage;
	}
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
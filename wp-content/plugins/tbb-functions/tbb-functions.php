<?php // test
/*
Plugin Name: Wescom Custom Functions
Plugin URI: mailto:jculley@bendbulletin.com
Description: Custom functions for Bend Homes.  Includes "Companies" custom post type and Settings page to create companies based on Agents brokerage office fields. Also includes additional shortcodes and cleans up the wordpress admin area.
Version: 1.0
Author: Jarel Culley
Author URI: http://www.bendbulletin.com
*/

if( ! defined( 'ABSPATH' ) )
	exit;


/**
 * Upon activation of the plugin, see if we are running the required version and deploy theme in defined.
 */
function tbb_custom_functions_activation() {
    if ( version_compare( get_bloginfo( 'version' ), '4.0', '<' ) ) {
        deactivate_plugins( __FILE__  );
        wp_die( __('WordPress 4.0 and higher required. The plugin has now disabled itself. On a side note why are you running an old version :( Upgrade!','index') );
    }
}

/* Constants */
define( 'TBB_VERSION', '1.0.1' );
define('TBB_FUNCTIONS_URL', plugin_dir_url(__FILE__));
define('TBB_FUNCTIONS_DIR', plugin_dir_path(__FILE__));


/***** 
 * Include necessary files
*****/
require_once( TBB_FUNCTIONS_DIR . 'admin/dashboard_widget.php' );
require_once( TBB_FUNCTIONS_DIR . 'admin/pages-metabox.php' );

require_once( TBB_FUNCTIONS_DIR . 'admin/functions.php' );

require_once( TBB_FUNCTIONS_DIR . 'rets-connect.class.php' );

require_once( TBB_FUNCTIONS_DIR . 'admin/offices.php' );
require_once( TBB_FUNCTIONS_DIR . 'admin/agents.php' );

//require_once( TBB_FUNCTIONS_DIR . 'post-types/post-type-company.php' );
//require_once( TBB_FUNCTIONS_DIR . 'admin/settings-company.php' );
//require_once( TBB_FUNCTIONS_DIR . 'admin/settings-agents.php' );

require_once( TBB_FUNCTIONS_DIR . 'tbb-shortcodes.php' );
require_once( TBB_FUNCTIONS_DIR . 'rets-shortcodes.php' );


// Enqueue Additional Files
/*add_action( 'wp_enqueue_scripts', 'tbb_enqueue_additional_files');
function tbb_enqueue_additional_files() {
	if (!is_admin() && $GLOBALS['pagenow'] != 'wp-login.php') {
		wp_deregister_script('jquery');
		wp_deregister_script('jquery-ui-core');
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', false, '1.11.3', true);
		wp_register_script('jquery-ui-core', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js', array('jquery'), '1.11.4', true);
		//wp_register_script('jquery-cookie', TBB_FUNCTIONS_URL .'js/jquery.cookie.min.js', array('jquery'), '', true);
        wp_enqueue_script('jquery');	
		wp_enqueue_script('jquery-ui-core');
		//wp_enqueue_script('jquery-cookie');	
	}
	//wp_enqueue_script("mobile-check", TBB_FUNCTIONS_URL . "/js/mobile-check.js", array("jquery"));
}*/


function string_sanitize($s) {
    $result = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($s, ENT_QUOTES));
    return $result;
}


// Enable shortcodes in text widgets
add_filter('widget_text','do_shortcode');


// Disable stupid emojicons scripts wordpress adds by default into the header.
add_action( 'init', 'disable_wp_emojicons' );
function disable_wp_emojicons() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  add_filter( 'emoji_svg_url', '__return_false' );
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



add_action('wp_head', 'tbb_custom_analytics_scripts', '999');
function tbb_custom_analytics_scripts() {
	$analytics = "";
	
	// Link IDX Broker to BendHomes in analytics
	if( is_page( array('577379', '577465') ) ) {
		$analytics .= "
			<!-- Cross Script GA: idxbroker -> bendhomes -->
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			  ga('create', 'UA-1815236-10', 'auto', {'allowLinker': true});
			  ga('require', 'linker');
  			  ga('linker:autoLink', ['bendhomes.com'] );
			  ga('send', 'pageview');

			</script>
		";
		
	// Vice versa: Link BendHomes to IDX Broker in analytics
	} else {
		$analytics .= "
			<!-- Cross Script GA: bendhomes -> idxbroker -->
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			  ga('create', 'UA-1815236-10', 'auto', {'allowLinker': true});
			  ga('require', 'linker');
  			  ga('linker:autoLink', ['bendhomes.idxbroker.com'] );
			  ga('send', 'pageview');

			</script>
		";
	}
	
	echo $analytics;
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
//add_filter( 'popmake_popup_is_loadable', 'tbb_popup_not_logged_in', 10, 2 );
function tbb_popup_not_logged_in( $is_loadable, $popup_id ) {
	//if( $popup_id == 292579 ) {	// Devsite
	if( $popup_id == 353717 ) { 		// Livesite
		return ! is_user_logged_in();
	}
	return $is_loadable;
}


// Add Mortgage Calculator Modal to Footer
add_action('wp_footer', 'tbb_add_modal_to_footer');
function tbb_add_modal_to_footer() {
	ob_start(); 

	if( is_page( array('577379', '577465') ) ) {
	?>
	<!-- Mortgage Calculator Modal -->
	<div id="paymentmodal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		</div>
		<div class="modal-body">
			<?php echo do_shortcode('[MORT_CALC_FORM]'); ?>
		</div>
	</div>
	<?php
	}
	
	echo ob_get_clean();
}


// Javascript to get url parameters on single property idx page to display Open House info
add_action('wp_footer', 'tbb_add_openhouses');
function tbb_add_openhouses() {
	ob_start();
	
	if( is_page( array('577379', '577465') ) ) {
	?>
	<script type="text/javascript">
	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');                        
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	var description = document.getElementById('IDX-description'),
		time = [],
		url_vars = getUrlVars();
	
	if(url_vars['dt0'] && url_vars['dt0'].length) {
		for(var i in url_vars) {
			if(i !== '_ga' && Object.keys(obj).some(function(k){ return ~k.indexOf("dt"+i) })) {
				//alert(i + " == " + url_vars[i]);
				textNode = decodeURI(url_vars[i]);
				time[time.length] = '<div class="time time-'+ i +'">'+ textNode.replace('+', ' ') +'</div>';
			}
		}		
		//console.log(time.join(""));
		
		description.insertAdjacentHTML('beforebegin', '<div id="OpenHouse" class="clearfix"><h3>Open House Times</h3>'+ time.join("") +'</div>');
	}
	</script>
	<?php
	}
	
	echo ob_get_clean();
}


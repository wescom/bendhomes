<?php
/*-----------------------------------------------------------------------------------*/
/*	Enqueue Styles in Child Theme
/*-----------------------------------------------------------------------------------*/
if (!function_exists('inspiry_enqueue_child_styles')) {
    function inspiry_enqueue_child_styles(){
        if ( !is_admin() ) {
            // dequeue and deregister parent default css
            wp_dequeue_style( 'parent-default' );
            wp_deregister_style( 'parent-default' );

            // dequeue parent custom css
            wp_dequeue_style( 'parent-custom' );

            // parent default css
            wp_enqueue_style( 'parent-default', get_template_directory_uri().'/style.css' );

            // parent custom css
            wp_enqueue_style( 'parent-custom' );

            // child default css
            wp_enqueue_style('child-default', get_stylesheet_uri(), array('parent-default'), '1.0', 'all' );

            // child custom css
            //wp_enqueue_style('child-custom',  get_stylesheet_directory_uri() . '/child-custom.css', array('child-default'), '1.0', 'all' );
			
			//wp_enqueue_script( 'superfish', get_stylesheet_directory_uri().'/js/superfish.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'touchwipe', get_stylesheet_directory_uri().'/js/jquery.touchwipe.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'sidr', get_stylesheet_directory_uri().'/js/jquery.sidr.min.js', array( 'jquery' ), '', true );
			//wp_enqueue_script( 'menufit', get_stylesheet_directory_uri().'/js/jquery.menufit.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script('custom');
        }
    }
}
add_action( 'wp_enqueue_scripts', 'inspiry_enqueue_child_styles', PHP_INT_MAX );


if ( !function_exists( 'inspiry_load_translation_from_child' ) ) {
    /**
     * Load translation files from child theme
     */
    function inspiry_load_translation_from_child() {
        load_child_theme_textdomain ( 'framework', get_stylesheet_directory () . '/languages' );
    }
    add_action ( 'after_setup_theme', 'inspiry_load_translation_from_child' );
}


// Add scripts to wp_head()
add_action( 'wp_head', 'child_theme_head_script' );
function child_theme_head_script() { ?>
  <script type='text/javascript'>
    <?php 
	/* Unminified JS code. Minified code added below.
	
	// DFP init
	var googletag = googletag || {};
	googletag.cmd = googletag.cmd || [];
	(function() {
	  var gads = document.createElement('script');
	  gads.async = true;
	  gads.type = 'text/javascript';
	  var useSSL = 'https:' == document.location.protocol;
	  gads.src = (useSSL ? 'https:' : 'http:') +
		'//www.googletagservices.com/tag/js/gpt.js';
	  var node = document.getElementsByTagName('script')[0];
	  node.parentNode.insertBefore(gads, node);
	})();
	// DFP slot definitions

	googletag.cmd.push(function () {
			// set up var ahead of time
			var width = document.documentElement.clientWidth;

	var sizetopleaderboard
	var sizemidleaderboard
	var sizebottomleaderboard
	var sizerail
	var sizePartners

			if (width >= 320 && width < 768) { 			//320--767
				sizetopleaderboard = [320, 50];
	  sizemidleaderboard = [320, 50];
				sizebottomleaderboard = [320, 50];
				sizerail = [[180, 150], [160, 600]];
	  sizePartners = [200, 200]
			} else if(width >= 768 && width < 992) {	//768--991
				sizetopleaderboard = [320, 50];
	  sizemidleaderboard = [320, 50];
	  sizebottomleaderboard = [320, 50];
				sizerail = [160, 600];
	  sizePartners = [200, 200]
			} else if(width >= 992) {					//992+
				sizetopleaderboard = [[970, 90], [728,90]];
				sizemidleaderboard = [728, 90];
	  sizebottomleaderboard = [[970, 90], [728,90]];
				sizerail = [160, 600];
	  sizePartners = [200, 200]
			} else { // fallback
	  sizetopleaderboard = [728, 90];
	  sizemidleaderboard = [728, 90];
	  sizebottomleaderboard = [728, 90];
	  sizerail = [160, 600];
	  sizePartners = [200, 200]
			}

			var gadsgenerateId = 1459980402618;
			var gadssectionkey = "Home";

	var slot01 = googletag.defineSlot('/38749147/BendHomes-topLeaderboard', sizetopleaderboard, 'div-gpt-ad-' + gadsgenerateId + '-0').addService(googletag.pubads());
			var slot02 = googletag.defineSlot('/38749147/BendHomes-middleLeaderboard', sizemidleaderboard, 'div-gpt-ad-' + gadsgenerateId + '-1').addService(googletag.pubads());
			var slot03 = googletag.defineSlot('/38749147/BendHomes-Rectangle', sizerail, 'div-gpt-ad-' + gadsgenerateId + '-2').addService(googletag.pubads());
			var slot04 = googletag.defineSlot('/38749147/BendHomes-bottomLeaderboard', sizebottomleaderboard, 'div-gpt-ad-' + gadsgenerateId + '-3').addService(googletag.pubads());
	var slot05 = googletag.defineSlot('/38749147/BendHomes-wideskyscraper', sizerail, 'div-gpt-ad-' + gadsgenerateId + '-4').addService(googletag.pubads());
	

	slot01.setTargeting("section", [gadssectionkey]);
			//*** slot02.setTargeting("section", [gadssectionkey]);
			slot03.setTargeting("section", [gadssectionkey]);
			slot04.setTargeting("section", [gadssectionkey]);

	googletag.pubads().collapseEmptyDivs();
			googletag.enableServices();

			$(window).resize(function () {
				googletag.pubads().refresh([slot01, slot02, slot03, slot04, slot05]);
			});
  });

  function refreshAd(slotName) {
			googletag.pubads().refresh();
		}*/ ?>
var googletag=googletag||{};googletag.cmd=googletag.cmd||[],function(){var t=document.createElement("script");t.async=!0,t.type="text/javascript";var e="https:"==document.location.protocol;t.src=(e?"https:":"http:")+"//www.googletagservices.com/tag/js/gpt.js";var o=document.getElementsByTagName("script")[0];o.parentNode.insertBefore(t,o)}();function refreshAd(e){googletag.pubads().refresh()}googletag.cmd.push(function(){var e,g,d,o,a,t=document.documentElement.clientWidth;t>=320&&768>t?(e=[320,50],g=[320,50],d=[320,50],o=[[180,150],[160,600]],a=[200,200]):t>=768&&992>t?(e=[320,50],g=[320,50],d=[320,50],o=[160,600],a=[200,200]):t>=992?(e=[[970,90],[728,90]],g=[728,90],d=[[970,90],[728,90]],o=[160,600],a=[200,200]):(e=[728,90],g=[728,90],d=[728,90],o=[160,600],a=[200,200]);var i=1459980402618,n="Home",s=googletag.defineSlot("/38749147/BendHomes-topLeaderboard",e,"div-gpt-ad-"+i+"-0").addService(googletag.pubads()),l=googletag.defineSlot("/38749147/BendHomes-middleLeaderboard",g,"div-gpt-ad-"+i+"-1").addService(googletag.pubads()),r=googletag.defineSlot("/38749147/BendHomes-Rectangle",o,"div-gpt-ad-"+i+"-2").addService(googletag.pubads()),p=googletag.defineSlot("/38749147/BendHomes-bottomLeaderboard",d,"div-gpt-ad-"+i+"-3").addService(googletag.pubads()),c=googletag.defineSlot("/38749147/BendHomes-wideskyscraper",o,"div-gpt-ad-"+i+"-4").addService(googletag.pubads());p1=googletag.defineSlot("/38749147/BendHomes-Partners1",a,"div-gpt-ad-"+i+"-5").addService(googletag.pubads()),p2=googletag.defineSlot("/38749147/BendHomes-Partners2",a,"div-gpt-ad-"+i+"-6").addService(googletag.pubads()),p3=googletag.defineSlot("/38749147/BendHomes-Partners3",a,"div-gpt-ad-"+i+"-7").addService(googletag.pubads()),p4=googletag.defineSlot("/38749147/BendHomes-Partners4",a,"div-gpt-ad-"+i+"-8").addService(googletag.pubads()),s.setTargeting("section",[n]),r.setTargeting("section",[n]),p.setTargeting("section",[n]),googletag.pubads().collapseEmptyDivs(),googletag.enableServices(),$(window).resize(function(){googletag.pubads().refresh([s,l,r,p,c])})});
</script>
  
  <?php
}


// Google DFP postion rener
add_action('dfp_ad_spot', 'dfp_ad_render', 10, 1);
if ( ! function_exists( 'dfp_ad_render' ) ) {
  function dfp_ad_render($position) {
    echo '<!-- '.$position.' -->';

    // // positions
    // leadheader
    // leadmid
    // leadfooter
    // siderail

    $posid = array(
      'leadheader' => 0,
      'leadmid' => 1,
      'leadfooter' => 3,
      'siderail' => 4,
      'partners1' => 5,
      'partners2' => 6,
      'partners3' => 7,
      'partners4' => 8,
    );

    $dispid = 'div-gpt-ad-1459980402618-'.$posid[$position];

    ?>
    <div class="dfp-ad">
      <div id='<?php echo $dispid; ?>'>
        <script type='text/javascript'>
        googletag.cmd.push(function() { googletag.display('<?php echo $dispid; ?>'); });
        </script>
      </div>
    </div>
    <?php

  }
}


// Displays property image "basename" underneath the Homepage Slider metabox on Propery
// edit screen, to make searching for property images easier.
add_action('admin_head','tbb_admin_load_property_script');
function tbb_admin_load_property_script() {
    global $pagenow, $typenow;
    if (empty($typenow) && !empty($_GET['post'])) {
        $post = get_post($_GET['post']);
        $typenow = $post->post_type;
    }
    if ($pagenow=='post-new.php' || $pagenow=='post.php' && $typenow=='property') {
        global $post;
		$property = get_post_meta( $post->ID );
		//print_r($property);
		
		// Get the Homepage Slider metadata value
		$slider_image_id = $property['REAL_HOMES_slider_image'][0];
		
		// Use the Homepage Slider image value if it exists, otherwise get the Featured Image value.
		$image_id = $slider_image_id ? $property['REAL_HOMES_slider_image'][0] : get_post_thumbnail_id( $post->ID );
		
		// Break down $image_id value to get the "basename" only.
		$image_src = wp_get_attachment_image_src( $image_id );
		$file_part = str_replace( 'property-', '', basename( $image_src[0], ".jpg" ) );
		$file_base = substr($file_part, 0, strpos($file_part, "-"));
				
		echo '
        <script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#REAL_HOMES_slider_image_description").before("<p>Image Base Name: '. $file_base .'</p>");
			});
		</script>
		';
		
    }
}


function tbb_current_url( $params ) {
    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$query = parse_url($url, PHP_URL_QUERY);
	// Returns a string if the URL has parameters or NULL if not
	if ($query) {
		$url .= '&'.$params;
	} else {
		$url .= '?'.$params;
	}
	return $url;
}


add_action('custom_footer_scripts', 'load_maps_script_in_footer');
function load_maps_script_in_footer() {
	if( is_singular('property') ) {
		global $post;
		
		$display_google_map = get_option('theme_display_google_map');
		$property_location = get_post_meta($post->ID,'REAL_HOMES_property_location',true);
		// set a trap if we get zeroes for map lat long from MLS | 1777 JTG
		if($property_location == '0.000000,0.000000') {
		  unset($property_location);
		}
		$property_address = get_post_meta($post->ID,'REAL_HOMES_property_address',true);
		$property_map = get_post_meta($post->ID,'REAL_HOMES_property_map',true);
	
		if( $property_address && !empty($property_location) && $display_google_map == 'true' && ( $property_map != 1 ) ) {
			$property_marker = array();
			$lat_lng = explode(',',$property_location);
			$property_marker['lat'] = $lat_lng[0];
			$property_marker['lang'] = $lat_lng[1];
	
			/* Property Map Icon Based on Property Type */
			$property_type_slug = 'single-family-home'; // Default Icon Slug
	
			$type_terms = get_the_terms( $post->ID,"property-type" );
			if( !empty( $type_terms ) ) {
				foreach($type_terms as $typ_trm){
					$property_type_slug = $typ_trm->slug;
					break;
				}
			}
	
			if( file_exists( get_template_directory().'/images/map/'.$property_type_slug.'-map-icon.png' ) ){
				$property_marker['icon'] = get_template_directory_uri().'/images/map/'.$property_type_slug.'-map-icon.png';
				// retina icon
				if( file_exists( get_template_directory().'/images/map/'.$property_type_slug.'-map-icon@2x.png' ) ) {
					$property_marker['retinaIcon'] = get_template_directory_uri().'/images/map/'.$property_type_slug.'-map-icon@2x.png';
				}
			}else{
				$property_marker['icon'] = get_template_directory_uri().'/images/map/single-family-home-map-icon.png';// default icon
				$property_marker['retinaIcon'] = get_template_directory_uri().'/images/map/single-family-home-map-icon@2x.png';  // default retina icon
			}
			
			$output = '<script type="application/javascript">
			function initialize_property_map(){var e='. json_encode( $property_marker ) .',o=e.icon,n=new google.maps.Size(42,57);window.devicePixelRatio>1.5&&e.retinaIcon&&(o=e.retinaIcon,n=new google.maps.Size(83,113));var a={url:o,size:n,scaledSize:new google.maps.Size(42,57),origin:new google.maps.Point(0,0),anchor:new google.maps.Point(21,56)},i=new google.maps.LatLng(e.lat,e.lang),p={center:i,zoom:15,mapTypeId:google.maps.MapTypeId.ROADMAP,scrollwheel:!1},g=new google.maps.Map(document.getElementById("property_map"),p);new google.maps.Marker({position:i,map:g,icon:a})}window.onload=initialize_property_map();
			</script>';
			
			echo $output;
			
		}	
	}
}


// Settings page under Properties Post Type
class PropertySettingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_action_property_settings', array( $this, 'property_settings_admin_action' ) );
	}

	function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=property',
			'Settings',
			'Property Settings',
			'edit_posts',
			'property-settings',
			array(
				$this,
				'property_settings_do_page'
			)
		);
	}
	
	function property_settings_admin_action() {
		wp_redirect( $_SERVER['HTTP_REFERER'] .'&companies-created=true' );
		//print_r($_POST);
		exit();
	}

	function property_settings_do_page() {
    
    	//must check that the user has the required capability 
        if (!current_user_can('edit_posts')) {
              wp_die( __('You do not have sufficient permissions to access this page.') ); }
        
            // variables for the field and option names 
            $opt_name = 'property_settings';
            $hidden_field_name = 'property_settings_hidden';
            $data_field_name = 'property_settings';
        
            // Read in existing option value from database
            $opt_val = get_option( $opt_name );
        
            // See if the user has posted us some information
            // If they did, this hidden field will be set to 'Y'
            if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
                // Read their posted value
                $opt_val = $_POST[ $data_field_name ];
        
                // Save the posted value in the database
                update_option( $opt_name, $opt_val );
        
                // Put a settings updated message on the screen
        
        		?>
            	<div class="updated"><p><strong>Settings saved.</strong></p></div>
            	<?php
            
            }
            
                // Now display the settings editing screen
            
                echo '<div class="wrap">';
            
                // header
            
                echo "<h1>Property Settings</h1>";
                            
                // settings form
                
                ?>
            
                <form name="form1" method="post" action="">
                
                	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
                    
                	<table class="widefat">
			
                        <tr valign="top">
                            <th class="row-title" colspan="2"><h3 style="margin-bottom: 0;">Homepage Banner</h3></th>
                        </tr>
                    
                        <tr valign="top" style="background: #fff;">
                            <th scope="row" width="25%"><label>MLS Number(s) for Banner:</label></th>
                            <td>
                                <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" class="large-text"> 
                            </td>
                        </tr>
                	</table>
                    
                    <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="Save Settings" />
                    </p>
                </form>
            </div>
	
    <?php }
	
}

if(is_admin()) new PropertySettingsPage;
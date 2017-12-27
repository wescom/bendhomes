<?php
/*-----------------------------------------------------------------------------------*/
/*	Enqueue Styles in Child Theme
/*-----------------------------------------------------------------------------------*/
if (!function_exists('inspiry_enqueue_child_styles')) {
    function inspiry_enqueue_child_styles(){
        if ( !is_admin() ) {
            wp_dequeue_style( 'parent-default' );
            wp_deregister_style( 'parent-default' );
            wp_dequeue_style( 'parent-custom' );
			wp_dequeue_style( 'bootstrap-css' );
			wp_dequeue_style( 'flexslider' );
			wp_dequeue_style( 'pretty-photo-css' );
			wp_dequeue_style( 'swipebox' );
			wp_dequeue_style( 'main-css' );
			wp_dequeue_style( 'font-awesome' );
			wp_deregister_style( 'font-awesome' );
			
			wp_register_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', array(), '', 'all' );
			wp_enqueue_style( 'font-awesome' );
            // child  style.css
            wp_enqueue_style('child-default', get_stylesheet_uri(), '', '', 'all' );
			
			// Deregister all scripts here to combine into one script below
			wp_dequeue_script( 'flexslider' );
            wp_dequeue_script( 'easing' );
            wp_dequeue_script( 'elastislide' );
            wp_dequeue_script( 'pretty-photo' );
            wp_dequeue_script( 'swipebox' );
            wp_dequeue_script( 'isotope' );
            wp_dequeue_script( 'jcarousel' );
            wp_dequeue_script( 'jqvalidate' );
            wp_dequeue_script( 'jqform' );
            wp_dequeue_script( 'selectbox' );
            wp_dequeue_script( 'jqtransit' );
            wp_dequeue_script( 'bootstrap' );
			// Combined scripts into one file
			wp_register_script( 'scripts-one', get_stylesheet_directory_uri() .'/js/scripts.one.js', array('jquery'), '', true );
			wp_enqueue_script( 'scripts-one' );
			
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
// Remove srcset attribute added to post thumbnails.
add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
// Add scripts to wp_head()
add_action( 'wp_head', 'child_theme_head_script' );
function child_theme_head_script() { 
    $sectionKey = "";
 
   	$theCat = get_the_category( $id = false );
   	if (sizeof($theCat) > 0) {
   		$sectionKey = $theCat[0]->slug;
   	} else {
	   	$urlLink = $_SERVER['REQUEST_URI'];
	   	$link_array = explode('/',$urlLink);
	    $sectionKey = $link_array[count($link_array) - 2];  
	}
   	//echo "sectionKey6 = ".$sectionKey;
   		
   ?>
  <script type='text/javascript'>
  	var getKey = <?php echo json_encode($sectionKey); ?>;
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
		var sizeSquared
		var sizeSmallSquare
		if (width >= 320 && width < 768) { 			//320--767
			sizetopleaderboard = [320, 50];
			sizemidleaderboard = [320, 50];
			sizebottomleaderboard = [320, 50];
			sizerail = [[180, 150], [160, 600]];
			sizePartners = [180, 180];
			sizeSquared = [[300, 250],[300, 600]];
			sizeSmallSquare = [200, 200];
		} else if(width >= 768 && width < 992) {	//768--991
			sizetopleaderboard = [320, 50];
			sizemidleaderboard = [320, 50];
			sizebottomleaderboard = [320, 50];
			sizerail = [160, 600];
			sizePartners = [180, 180];
			sizeSquared = [[300, 250],[300, 600]];
			sizeSmallSquare = [200, 200];
		} else if(width >= 992) {					//992+
			sizetopleaderboard = [[970, 90], [728,90]];
			sizemidleaderboard = [728, 90];
			sizebottomleaderboard = [[970, 90], [728,90]];
			sizerail = [160, 600];
			sizePartners = [180, 180];
			sizeSquared = [[300, 250],[300, 600]];
			sizeSmallSquare = [200, 200];
		} else { // fallback
			sizetopleaderboard = [728, 90];
			sizemidleaderboard = [728, 90];
			sizebottomleaderboard = [728, 90];
			sizerail = [160, 600];
			sizePartners = [180, 180];
			sizeSquared = [[300, 250],[300, 600]];
			sizeSmallSquare = [200, 200];
		}
		var gadsgenerateId = 1459980402618;
		var gadssectionkey = getKey;
		
		var slot01 = googletag.defineSlot('/38749147/BendHomes-topLeaderboard', sizetopleaderboard, 'div-gpt-ad-' + gadsgenerateId + '-1').addService(googletag.pubads());
		
		var slot02 = googletag.defineSlot('/38749147/BendHomes-middleLeaderboard', sizemidleaderboard, 'div-gpt-ad-' + gadsgenerateId + '-2').addService(googletag.pubads());
		
		var slot03 = googletag.defineSlot('/38749147/BendHomes-Rectangle', sizerail, 'div-gpt-ad-' + gadsgenerateId + '-3').addService(googletag.pubads());
		
		var slot04 = googletag.defineSlot('/38749147/BendHomes-bottomLeaderboard', sizebottomleaderboard, 'div-gpt-ad-' + gadsgenerateId + '-4').addService(googletag.pubads());
		
		// Single property square ads
		var slot05 = googletag.defineSlot('/38749147/BendHomes-Rectangle', sizeSquared, 'div-gpt-ad-' + gadsgenerateId + '-5').addService(googletag.pubads());
		var slot06 = googletag.defineSlot('/38749147/BendHomes-Rectangle2', sizeSquared, 'div-gpt-ad-' + gadsgenerateId + '-6').addService(googletag.pubads());
		var slot07 = googletag.defineSlot('/38749147/BendHomes-Partners1', sizePartners, 'div-gpt-ad-' + gadsgenerateId + '-7').addService(googletag.pubads());
		var slot08 = googletag.defineSlot('/38749147/BendHomes-Partners2', sizePartners, 'div-gpt-ad-' + gadsgenerateId + '-8').addService(googletag.pubads());
		var slot09 = googletag.defineSlot('/38749147/BendHomes-Partners3', sizePartners, 'div-gpt-ad-' + gadsgenerateId + '-9').addService(googletag.pubads());
		var slot10 = googletag.defineSlot('/38749147/BendHomes-Partners4', sizePartners, 'div-gpt-ad-' + gadsgenerateId + '-10').addService(googletag.pubads());
		var slot11 = googletag.defineSlot('/38749147/BendHomes-Partners5', sizePartners, 'div-gpt-ad-' + gadsgenerateId + '-11').addService(googletag.pubads());
		var slot12 = googletag.defineSlot('/38749147/BendHomes-MortCalc', sizeSmallSquare, 'div-gpt-ad-' + gadsgenerateId + '-12').addService(googletag.pubads());
		var slot13 = googletag.defineSlot('/38749147/BendHomes-Rectangle3', sizeSquared, 'div-gpt-ad-' + gadsgenerateId + '-13').addService(googletag.pubads());
		var slot14 = googletag.defineSlot('/38749147/BendHomes-Rectangle4', sizeSquared, 'div-gpt-ad-' + gadsgenerateId + '-14').addService(googletag.pubads());
		
		// Mortgage Calculator ad
		slot01.setTargeting("section", [gadssectionkey]);
		slot02.setTargeting("section", [gadssectionkey]);
		slot03.setTargeting("section", [gadssectionkey]);
		slot04.setTargeting("section", [gadssectionkey]);
		slot05.setTargeting("section", [gadssectionkey]);
		slot06.setTargeting("section", [gadssectionkey]);
		slot07.setTargeting("section", [gadssectionkey]);
		slot08.setTargeting("section", [gadssectionkey]);
		slot09.setTargeting("section", [gadssectionkey]);
		slot10.setTargeting("section", [gadssectionkey]);
		slot11.setTargeting("section", [gadssectionkey]);
		slot12.setTargeting("section", [gadssectionkey]);
		slot13.setTargeting("section", [gadssectionkey]);
		slot14.setTargeting("section", [gadssectionkey]);
		
		googletag.pubads().collapseEmptyDivs();
		googletag.enableServices();
		$(window).resize(function () {
			googletag.pubads().refresh([slot01, slot02, slot03, slot04]);
		});
  });
  function refreshAd(slotName) {
	googletag.pubads().refresh();
  }*/ 
	
	  
// This javasript below is the minified version of everything commented out above. 
// Make all edits to the script above, then use https://javascript-minifier.com/ to replace everything below.
// This will help keep things more readable but still have a minified version of the code for production.
?>
function refreshAd(e){googletag.pubads().refresh()}var googletag=googletag||{};googletag.cmd=googletag.cmd||[],function(){var e=document.createElement("script");e.async=!0,e.type="text/javascript";var g="https:"==document.location.protocol;e.src=(g?"https:":"http:")+"//www.googletagservices.com/tag/js/gpt.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)}(),googletag.cmd.push(function(){var e,g,t,o,a,d,n,i=document.documentElement.clientWidth;i>=320&&768>i?(e=[320,50],g=[320,50],t=[320,50],o=[[180,150],[160,600]],a=[180,180],d=[[300,250],[300,600]],n=[200,200]):i>=768&&992>i?(e=[320,50],g=[320,50],t=[320,50],o=[160,600],a=[180,180],d=[[300,250],[300,600]],n=[200,200]):i>=992?(e=[[970,90],[728,90]],g=[728,90],t=[[970,90],[728,90]],o=[160,600],a=[180,180],d=[[300,250],[300,600]],n=[200,200]):(e=[728,90],g=[728,90],t=[728,90],o=[160,600],a=[180,180],d=[[300,250],[300,600]],n=[200,200]);var s=1459980402618,r=getKey,l=googletag.defineSlot("/38749147/BendHomes-topLeaderboard",e,"div-gpt-ad-"+s+"-1").addService(googletag.pubads()),c=googletag.defineSlot("/38749147/BendHomes-middleLeaderboard",g,"div-gpt-ad-"+s+"-2").addService(googletag.pubads()),p=googletag.defineSlot("/38749147/BendHomes-Rectangle",o,"div-gpt-ad-"+s+"-3").addService(googletag.pubads()),v=googletag.defineSlot("/38749147/BendHomes-bottomLeaderboard",t,"div-gpt-ad-"+s+"-4").addService(googletag.pubads()),m=googletag.defineSlot("/38749147/BendHomes-Rectangle",d,"div-gpt-ad-"+s+"-5").addService(googletag.pubads()),S=googletag.defineSlot("/38749147/BendHomes-Rectangle2",d,"div-gpt-ad-"+s+"-6").addService(googletag.pubads()),u=googletag.defineSlot("/38749147/BendHomes-Partners1",a,"div-gpt-ad-"+s+"-7").addService(googletag.pubads()),b=googletag.defineSlot("/38749147/BendHomes-Partners2",a,"div-gpt-ad-"+s+"-8").addService(googletag.pubads()),f=googletag.defineSlot("/38749147/BendHomes-Partners3",a,"div-gpt-ad-"+s+"-9").addService(googletag.pubads()),B=googletag.defineSlot("/38749147/BendHomes-Partners4",a,"div-gpt-ad-"+s+"-10").addService(googletag.pubads()),T=googletag.defineSlot("/38749147/BendHomes-Partners5",a,"div-gpt-ad-"+s+"-11").addService(googletag.pubads()),H=googletag.defineSlot("/38749147/BendHomes-MortCalc",n,"div-gpt-ad-"+s+"-12").addService(googletag.pubads()),h=googletag.defineSlot("/38749147/BendHomes-Rectangle3",d,"div-gpt-ad-"+s+"-13").addService(googletag.pubads()),w=googletag.defineSlot("/38749147/BendHomes-Rectangle4",d,"div-gpt-ad-"+s+"-14").addService(googletag.pubads());l.setTargeting("section",[r]),c.setTargeting("section",[r]),p.setTargeting("section",[r]),v.setTargeting("section",[r]),m.setTargeting("section",[r]),S.setTargeting("section",[r]),u.setTargeting("section",[r]),b.setTargeting("section",[r]),f.setTargeting("section",[r]),B.setTargeting("section",[r]),T.setTargeting("section",[r]),H.setTargeting("section",[r]),h.setTargeting("section",[r]),w.setTargeting("section",[r]),googletag.pubads().collapseEmptyDivs(),googletag.enableServices(),$(window).resize(function(){googletag.pubads().refresh([l,c,p,v])})});
</script>
  
  <?php
}


// Google DFP postion render
add_action('dfp_ad_spot', 'dfp_ad_render', 10, 1);
if ( ! function_exists( 'dfp_ad_render' ) ) {
  function dfp_ad_render($position) {
    echo '<!-- '.$position.' -->';
    // // positions
    // leadheader
    // leadmid
    // leadfooter
    // siderail
	$code1 = '1459980402618-';
    $posid = array(
      'leadheader' => $code1 .'1',
      'leadmid' => $code1 .'2',
      'leadfooter' => $code1 .'4',
      'siderail' => $code1 .'3',
      'partners1' => $code1 .'7',
      'partners2' => $code1 .'8',
      'partners3' => $code1 .'9',
      'partners4' => $code1 .'10',
	  'partners5' => $code1 .'11',
	  //'siderail2' => $code1 .'10',
		
	  'rectangle1' => $code1 .'5',
	  'rectangle2' => $code1 .'6',
	  'mortcalc'   => $code1 .'12',
	  'rectangle3' => $code1 .'13',
	  'rectangle4' => $code1 .'14'
    );
    $dispid = 'div-gpt-ad-'.$posid[$position];
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


// Google DFP position shortcode render
add_shortcode('dfp_ad', 'dfp_ad_shortcode');
function dfp_ad_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'type' => '',
		'class' => '',
	), $atts );
	
	$type = sanitize_text_field( $atts['type'] );
	$class = sanitize_text_field( $atts['class'] );
	
	$code1 = '1459980402618-';
    $posid = array(
		'leadheader' => $code1 .'1',
		'leadmid' => $code1 .'2',
		'leadfooter' => $code1 .'4',
		'siderail' => $code1 .'3',
		'partners1' => $code1 .'7',
		'partners2' => $code1 .'8',
		'partners3' => $code1 .'9',
		'partners4' => $code1 .'10',
		'partners5' => $code1 .'11',
		'rectangle1' => $code1 .'5',
		'rectangle2' => $code1 .'6',
		'mortcalc'   => $code1 .'12',
		'rectangle3' => $code1 .'13',
		'rectangle4' => $code1 .'14'
    );
    $dispid = 'div-gpt-ad-'.$posid[$type];
	
	$html = '';
	$html .= sprintf('<!-- %s -->', $type );
	$html .= sprintf( '<div class="dfp-ad %s"><div id="%s"><script type="text/javascript">', $class, $dispid );
		$html .= sprintf( 'googletag.cmd.push(function() { googletag.display("%s"); });', $dispid );
	$html .= '</script></div></div>';
	
	return $html;
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
			$(\'#prop-tabs a[href="#tab-map"]\').on("shown",function(e){initialize_property_map();})
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
		//print_r($_POST);
		exit();
	}
	function property_settings_do_page() {
    
    	//must check that the user has the required capability 
        if (!current_user_can('edit_posts')) {
              wp_die( __('You do not have sufficient permissions to access this page.') ); }
        
            // variables for the field and option names 
            $banner_mls_nums = 'banner_mls_numbers';
			
            $hidden_field_name = 'property_settings_hidden';
            $data_field_name = 'property_settings';
        
            // Read in existing option value from database
            $banner_mls_val = get_option( $banner_mls_nums );
        
            // See if the user has posted us some information
            // If they did, this hidden field will be set to 'Y'
            if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
                // Read their posted value
                $banner_mls_val = $_POST[ $data_field_name ];
        
                // Save the posted value in the database
                update_option( $banner_mls_nums, $banner_mls_val );
        
                // Put a settings updated message on the screen
        		?>
            	<div class="updated"><p><strong>Settings saved.</strong></p></div>
            	<?php
            
            }
			?>
            
            <style type="text/css">
			.wrap h1 { margin-bottom: 30px; }
			.wrap.property-settings .dashicons-building:before { line-height: 30px; color: #0073AA; }
			.widefat.white tr { background: #fff !important; }
			</style>
            
			<div class="wrap property-settings">
                <h1><i class="dashicons-before dashicons-building"></i> Property Settings</h1>
            
                <form name="form1" method="post" action="">
                
                	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
                    
                	<table class="widefat white">
			
                        <tr valign="top">
                            <th class="row-title" colspan="2"><h3 style="margin-bottom: 0;">Homepage Banner</h3></th>
                        </tr>
                    
                        <tr valign="top" style="background: #fff;">
                            <th scope="row" width="25%"><label>MLS Number(s) for Banner:</label></th>
                            <td>
                                <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $banner_mls_val; ?>" class="large-text"> 
                                <div>(Separate each MLS# with a comma)</div>
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
//if(is_admin()) new PropertySettingsPage;
// Temporary fix to reditect non admin users so we can work on the live site.
// Uncomment this action out to get logged in, then reactivate it again.
//add_action( 'init', 'bh_redirect_non_admin_user' );
function bh_redirect_non_admin_user(){
    if ( !defined( 'DOING_AJAX' ) && !current_user_can('administrator') && !in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ) {
		wp_redirect( 'http://adhosting.wescompapers.com/bendhomes-com/' );  
		exit;
    } 
}
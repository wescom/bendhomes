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

    <?php /* Unminified JS code. Minified code added below.
	
			googletag.cmd.push(function () {
    				// set up var ahead of time
    				var width = document.documentElement.clientWidth;

            var sizetopleaderboard
            var sizemidleaderboard
            var sizebottomleaderboard
            var sizerail

    				if (width >= 320 && width < 768) { 			//320--767
    					sizetopleaderboard = [320, 50];
              sizemidleaderboard = [320, 50];
    					sizebottomleaderboard = [320, 50];
    					sizerail = [[180, 150], [160, 600]];
    				} else if(width >= 768 && width < 992) {	//768--991
    					sizetopleaderboard = [320, 50];
              sizemidleaderboard = [320, 50];
              sizebottomleaderboard = [320, 50];
    					sizerail = [160, 600];
    				} else if(width >= 992) {					//992+
    					sizetopleaderboard = [[970, 90], [728,90]];
    					sizemidleaderboard = [728, 90];
              sizebottomleaderboard = [[970, 90], [728,90]];
    					sizerail = [160, 600];
    				} else { // fallback
              sizetopleaderboard = [728, 90];
              sizemidleaderboard = [728, 90];
              sizebottomleaderboard = [728, 90];
              sizerail = [160, 600];
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
				
		function refreshAd(e){googletag.pubads().refresh()}googletag.cmd.push(function(){var e,g,o,d,a=document.documentElement.clientWidth;a>=320&&768>a?(e=[320,50],g=[320,50],o=[320,50],d=[[180,150],[160,600]]):a>=768&&992>a?(e=[320,50],g=[320,50],o=[320,50],d=[160,600]):a>=992?(e=[[970,90],[728,90]],g=[728,90],o=[[970,90],[728,90]],d=[160,600]):(e=[728,90],g=[728,90],o=[728,90],d=[160,600]);var t=1459980402618,i="Home",n=googletag.defineSlot("/38749147/BendHomes-topLeaderboard",e,"div-gpt-ad-"+t+"-0").addService(googletag.pubads()),s=googletag.defineSlot("/38749147/BendHomes-middleLeaderboard",g,"div-gpt-ad-"+t+"-1").addService(googletag.pubads()),l=googletag.defineSlot("/38749147/BendHomes-Rectangle",d,"div-gpt-ad-"+t+"-2").addService(googletag.pubads()),r=googletag.defineSlot("/38749147/BendHomes-bottomLeaderboard",o,"div-gpt-ad-"+t+"-3").addService(googletag.pubads()),c=googletag.defineSlot("/38749147/BendHomes-wideskyscraper",d,"div-gpt-ad-"+t+"-4").addService(googletag.pubads());n.setTargeting("section",[i]),l.setTargeting("section",[i]),r.setTargeting("section",[i]),googletag.pubads().collapseEmptyDivs(),googletag.enableServices(),$(window).resize(function(){googletag.pubads().refresh([n,s,l,r,c])})});
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
      'siderail' => 4
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
		print_r($property);
		
		$image_id = $property['REAL_HOMES_slider_image'][0];
		$image_src = wp_get_attachment_image_src( $image_id );
		$file_part = str_replace( 'property-', '', basename( $image_src[0], ".jpg" ) );
		$file_base = substr($file_part, 0, strpos($file_part, "-"));
		
		echo '
        <script type="text/javascript">
			<!-- Test Jarel 194197 -->
			<!-- ID: '. $image_id .' -->
			<!-- URL: '. $image_src[0] .' -->
			<!-- PART: '. $file_part .' -->
			<!-- BASE: '. $file_base .' -->
			jQuery(document).ready(function(){
				/*frame = wp.media();
				frame.on("open",function() {
					console.log("Yes I opened it!");
					jQuery("#media-search-input").val("'. $file_base .'");
				});*/
				jQuery("#REAL_HOMES_slider_image_description").before("<p>Image Base Name: '. $file_base .'</p>");
			});
		</script>
		';
		
    }
}

<?php
// Shortcodes


// Filters shortcode content to format html tags
add_filter('shortcode_content', 'shortcode_content');
function shortcode_content($content) {
	// p and br tag fix for wordpress autop
	$bogus_br_start_end = "^<br \/>\s*|<br \/>\s*$";
	$br_between_scs = "(\])<br \/>\s*?(\[)";
	$bogus_p_start_end = "^<\/p>\s*|\s*<p>$";
	$p_with_sc = "<p>(\[.*\]|\[[^\]]*\][^\[]*\[[^\]]*\])<\/p>|<p>(\[)|(\])<\/p>"; // Detect them brackets!

	$content = preg_replace("/{$bogus_br_start_end}|{$br_between_scs}/", "\$1\$2", $content);
	$content = preg_replace("/{$bogus_p_start_end}|{$p_with_sc}/", "\$1\$2\$3", $content);

	// render internal shortcodes... cause wordpress doesn't
	$content = do_shortcode($content);

	return $content;
}


// Creates address with schema.org tags
add_shortcode('SCHEMA_ADDRESS', 'tbb_schema_address');
function tbb_schema_address( $atts ) {
	$atts = shortcode_atts( array(
		'name' => '',
		'address' => '',
		'city' => '',
		'state' => '',
		'zip' => '',
		'phone' => '',
		'fax' => '',
		'link' => '',
		'image_id' => '',
		'show_image' => '',
		'latitude' => '',
		'longitude' => '',
		'google_plus' => '',
		'google_map' => ''
	), $atts );
	
	$name = sanitize_text_field( $atts['name'] );
	$address = sanitize_text_field( $atts['address'] );
	$city = sanitize_text_field( $atts['city'] );
	$state = sanitize_text_field( $atts['state'] );
	$zip = sanitize_text_field( $atts['zip'] );
	$phone = sanitize_text_field( $atts['phone'] );
	$phone_url = preg_replace('/[^0-9]/', '', $phone);
	$fax = sanitize_text_field( $atts['fax'] );
	$link = sanitize_text_field( $atts['link'] );
	$image_id = sanitize_text_field( $atts['image_id'] );
	
	$logo = wp_get_attachment_image_src( $image_id, 'full', true);
	
	$latitude = sanitize_text_field( $atts['latitude'] );
	$longitude = sanitize_text_field( $atts['longitude'] );
	$google_plus = sanitize_text_field( $atts['google_plus'] );
	$google_map = sanitize_text_field( $atts['google_map'] );
	
	ob_start(); ?>
    
    <div itemid="LocalBusiness" itemprop="location" itemscope="" itemtype="http://schema.org/LocalBusiness" style="line-height:125%;">
    	<?php if(!empty($image_id)) { ?>
        <p<?php if( $atts['show_image'] == 'no' ) echo ' style="display:none;"'; ?>><img itemprop="logo" src="<?php echo $logo[0]; ?>" alt="<?php echo $name; ?> Logo" width="<?php echo $logo[1]; ?>" height="<?php echo $logo[2]; ?>" /></p>
        <?php } ?>
    	
        <div itemprop="name"><?php echo $name; ?></div>
        
        <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
            <p><span itemprop="streetAddress"><?php echo $address; ?></span> <br>
            
            <span itemprop="addressLocality"><?php echo $city; ?></span>, <span itemprop="addressRegion"><?php echo $state; ?></span> <span itemprop="postalCode"><?php echo $zip; ?></span><br>
        </div>
        
        <?php if(!empty($phone)) { ?>
        <strong>Phone:</strong> <a href="tel:<?php echo $phone_url; ?>" data-track-event="set">
        <span itemprop="telephone"><?php echo $phone; ?></span></a> <br>
        <?php } ?>
        
        <?php if(!empty($fax)) { ?>
        <strong>Fax:</strong> <span itemprop="faxNumber"><?php echo $fax; ?></span></p>
        <?php } ?>
        
        <?php if(!empty($link)) { ?><link itemprop="url" href="<?php echo $link; ?>"><?php } ?>
        <?php if(!empty($google_plus)) { ?><link itemprop="sameAs" href="https://plus.google.com/<?php echo $google_plus; ?>"><?php } ?>
        <?php if(!empty($google_map)) { ?><link itemprop="hasMap" href="<?php echo $google_map; ?>"><?php } ?>
        
        <?php if(!empty($latitude) && !empty($longitude)) { ?>
        <span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
            <meta itemprop="latitude" content="<?php echo $latitude; ?>" />
            <meta itemprop="longitude" content="<?php echo $longitude; ?>" />
        </span>
        <?php } ?>
    </div>
    
    <?php
	return ob_get_clean();
}



// Creates PlanIt events
add_shortcode( 'PLANIT', 'tbb_render_planit' );
function tbb_render_planit( $atts ) {
	$atts = shortcode_atts( array(
		'class' => 'planit-wrapper'
	), $atts );
	
	$class = sanitize_text_field( $atts['class'] );
	
	ob_start(); ?>
    
    <div class="<?php echo $class; ?>">
    <script type="text/javascript" src="//portal.CitySpark.com/PortalScripts/BendBulletin"></script>
    </div>
    
    <?php
	return ob_get_clean();
}


// Adds the Mortgage Calculator iframe
add_shortcode( 'MORTGAGE_CALCULATOR', 'tbb_render_mortgage_calculator' );
function tbb_render_mortgage_calculator() {
	ob_start(); ?>
    
    <div id="acww-widgetwrapper" style="min-width:250px;width:100%;"><div id="acww-widget" style="position:relative;padding-top:0;height:0;overflow:hidden;padding-bottom:840px;"><iframe id="acww-widget-iframe" frameborder="0" scrolling="no" width="800px" height="280px" src="http://usmortgagecalculator.org/widget/2.0/widget.html" style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe></div></div>
    
    <?php
	return ob_get_clean();	
}


// Adds Evergreen Home Loans info
add_shortcode( 'EVERGREEN_LOANS', 'tbb_evergreen_home_loads' );
function tbb_evergreen_home_loads() {
	ob_start(); ?>
    
    <div style="text-align: center;">
        <a href="https://www.evergreenhomeloans.com/bend/?ref=bh" target="_blank" onclick="trackOutboundLink('https://www.evergreenhomeloans.com/bend/?ref=bh', 'Evergreen 1'); return false;">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/evergreen-home-loans-logo2.png" width="200" height="117" alt="Evergreen Home Loans" />
        </a>
		<div style="margin-bottom: 10px;">Helping people buy homes</div>
        <div class="address"><i class="fa fa-map-marker"></i> 685 SE 3rd St., Bend OR, 97702<br><a href="tel:5413185500" onclick="trackOutboundLink('tel:5413185500', 'Evergreen Ph'); return false;"><i class="fa fa-mobile-phone"></i> (541) 318-5500</a></div>
    </div>
    
    <?php
	return ob_get_clean();	
}


// Creates map link to open native maps app on mobile devices.
add_shortcode('MAP_LINK', 'tbb_map_link');
function tbb_map_link($atts, $content = null) {
	
	wp_enqueue_script("mobile-check", TBB_FUNCTIONS_URL . "/js/mobile-check.js", array("jquery"));

	static $count = 0;

	$opts = shortcode_atts( array(
		'id' => '',
		'address' => '',
		'cid' => '',
		'class' => ''
	), $atts );

	$content = apply_filters('shortcode_content', $content);

	if(!$opts['id']) $opts['id'] = 0;

	$link = 'https://maps.google.com/maps?cid='. $opts['cid'] . '&amp;q='. urlencode($opts['address']);

	/* Unminified JS
	(function() {
		if(window.tbb_mobile_check && tbb_mobile_check()) {
			var _running = document.getElementById("map-link-controller-'. $count .'"),
				_link = _running.previousElementSibling;
			if(tbbMobileOSCheck.iOS() || tbbMobileOSCheck.Windows()) {
				_link.href = _link.href.replace("https:", "maps:");
			}
			else if(tbbMobileOSCheck.Android()) {
				_link.href = _link.href.replace("https:", "geo:");
			}
		}
	})();
	*/

	$mobile_script = '<script id="map-link-controller-'. $count .'">(function() { if(window.tbb_mobile_check && tbb_mobile_check()) { var _running = document.getElementById("map-link-controller-'. $count .'"), _link = _running.previousElementSibling; if(tbbMobileOSCheck.iOS() || tbbMobileOSCheck.Windows()) { _link.href = _link.href.replace("https:", "maps:"); } else if(tbbMobileOSCheck.Android()) { _link.href = _link.href.replace("https:", "geo:"); } } })();</script>';

	$count++;

	$classes = explode(' ', $opts['class']);
	$classes[] = 'google-map-link';

	return '<a href="'. $link .'" target="_blank" class="'. implode(' ', $classes) .'">'. $content .'</a>' . $mobile_script;
}


// Adds autofocus script to page.  Default input is Main Form "Name" field.
add_shortcode('AUTOFOCUS', 'add_autofocus_shortcode');
function add_autofocus_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'id' => '',
	), $atts );
	
	$id = sanitize_text_field( $atts['id'] );
	
	ob_start(); ?>
	
	<script type="text/javascript">
		var w = window.innerWidth
			|| document.documentElement.clientWidth
			|| document.body.clientWidth;
		window.onload = function() {
			if(w > 800) {
		  		document.getElementById("<?php echo $id; ?>").focus();
			}
		};
	</script>
	
	<?php
	return ob_get_clean();
}


// Mailchimp email sign up form
add_shortcode('MAILCHIMP_FORM', 'tbb_mailchimp_signup_form');
function tbb_mailchimp_signup_form( $atts ) {
	$atts = shortcode_atts( array(
		'classes' => '',
		'button_text' => 'Subscribe'
	), $atts );
	
	$classes = sanitize_text_field( $atts['classes'] );
	$button_text = sanitize_text_field( $atts['button_text'] );
	
	ob_start(); ?>
    
<!-- Begin MailChimp Signup Form -->
<!--link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css"-->
<div id="mc_embed_signup" class="<?php echo $classes; ?>">
<form action="//bendbulletin.us1.list-manage.com/subscribe/post?u=a5d36976165603b3ce7485798&amp;id=747292e3e3" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	
    <div class="mc-field-group mc-email">
        <label for="mce-EMAIL">Email Address </label>
        <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
    </div>
    <div class="mc-field-group mc-first">
        <label for="mce-FNAME">First Name </label>
        <input type="text" value="" name="FNAME" class="" id="mce-FNAME">
    </div>
    <div class="mc-field-group mc-last">
        <label for="mce-LNAME">Last Name </label>
        <input type="text" value="" name="LNAME" class="" id="mce-LNAME">
    </div>
	<div id="mce-responses" style="clear:both">
		<div class="response" id="mce-error-response" style="display:none"></div>
		<div class="response" id="mce-success-response" style="display:none"></div>
	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a5d36976165603b3ce7485798_2b8e3c71e4" tabindex="-1" value=""></div>
    <div class="clear mc-button" style="clear:both"><input type="submit" value="<?php echo $button_text; ?>" name="subscribe" id="mc-embedded-subscribe" class="real-btn btn"></div>
    </div>
</form>
</div>
<!--End mc_embed_signup-->
    
    <?php
	
	$output = ob_get_clean();
	return $output;	
}


// Display any post type in a 1-6 column grid
add_shortcode('BH_CUSTOM_POSTS', 'tbb_custom_posts');
function tbb_custom_posts( $defaults ) {
	$defaults = shortcode_atts( array(
		'type' => 'post',
		'ids' => '',
		'limit' => '12',
		'offset' => '',
		'category_type' => '',
		'categories' => '',
		'featured_image' => '',
		'excerpt_length' => '12',
		'show_excerpt' => '',
		'meta_key' => '',
		'meta_value' => '',
		'meta_value_type' => 'CHAR',
		'meta_compare' => '=',
		'classes' => '',
		'columns' => '2',
		'order' => 'ASC',
		'orderby' => 'name',
		'show_search' => '',
		'show_pagination' => '',
		'skip_words' => ''
	), $defaults );
	
	$classes = sanitize_text_field( $defaults['classes'] );
	$order = sanitize_text_field( $defaults['order'] );
	$orderby = sanitize_text_field( $defaults['orderby'] );
	
	switch( $defaults['columns'] ) {
		case "6":
			$cols_per_row = 6;
			$cols = "six";
			$image_size = 'property-thumb-image';
			break;
		case "5":
			$cols_per_row = 5;
			$cols = "five";
			$image_size = 'property-thumb-image';
			break;
		case "4":
			$cols_per_row = 4;
			$cols = "four";
			$image_size = 'property-thumb-image';
			break;
		case "3":
			$cols_per_row = 3;
			$cols = "three";
			$image_size = 'gallery-two-column-image';
			break;
		case "2":
			$cols_per_row = 2;
			$cols = "two";
			$image_size = 'gallery-two-column-image';
			break;
		case "1":
			$cols_per_row = 1;
			$cols = "one";
			$image_size = 'property-detail-slider-image-two';
			break;
	}
	
	// Transform ids to array
	if( $defaults['ids'] ) {
		$post_ids = explode( ',', $defaults['ids'] );
	} else {
		$post_ids = array();	
	}
	
	// Transform categories to array
	if ( $defaults['category_type'] && $defaults['categories'] ) {
		//$cat_slugs = preg_replace( '/\s+/', '', $defaults['categories'] );
		$cat_slugs = explode( ',', $defaults['categories'] );
	} else {
		$cat_slugs = array();
	}
	
	// Enable order A-Z & Z-A select field if url contains ?sort= param
	$url_sort = '';
	$url_sort = $_GET['sort'];
	$sort_order = ($url_sort == 'a-z' || $url_sort == 'z-a') ? 'name' : $order;
	if( $url_sort == 'a-z' ) {
		$sort_orderby = 'ASC';
	} elseif( $url_sort == 'z-a' ) {
		$sort_orderby = 'DESC';
	} else {
		$sort_orderby = $orderby;
	}
	
	// Initialize the query array
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type' 	=> $defaults['type'],
		'posts_per_page' => $defaults['limit'],
		'paged' 	=> $paged,
		'has_password' => false,
		'order' => $sort_order,
		'orderby' => $sort_orderby
	);
	
	// Adds list of ids to query
	if ( !empty( $post_ids ) ) {
		$args['post__in'] = $post_ids;
	}
	
	// Skip posts with specific word
	if ( $defaults['skip_words'] ) {
		$args['s'] = $defaults['skip_words'];
	}
	
	// Adds offset to query
	if ( $defaults['offset'] ) {
		$args['offset'] =  $defaults['offset'];
	}
	
	// Adds categories to query
	if ( !empty( $cat_slugs ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' 	=> $defaults['category_type'],
				'field' 	=> 'slug',
				'terms' 	=> $cat_slugs
			)
		);
	}
	
	// Adds meta key and value pair to query with optional comparision value
	if ( !empty( $defaults['meta_key'] ) && !empty( $defaults['meta_value'] ) ) {
		$args['meta_query'] = array(
			array(
				'key' => $defaults['meta_key'],
				'value' => $defaults['meta_value'],
				'compare' => $defaults['meta_compare'],
				'type' => $defaults['meta_value_type']
			)
		);
	}

	$custom_posts = new WP_Query( $args );
	
	if ( $custom_posts->have_posts() ) :
	
	$output = '<div class="custom-posts-wrapper post-'. $defaults['type'] .'"><div class="custom-posts-container clearfix">';
	
		if( !empty( $defaults['show_search'] ) ) {
			
			$find_text = $defaults['type'] == 'agent' ? 'Find an '. $defaults['type'] : 'Find a '. $defaults['type'];
			
			$output .= '<div class="custom-search-wrap">';
				$output .= '
					<form role="search" action="'. site_url('/') .'" method="get" id="searchform">
						<input type="text" class="search-field" name="s" placeholder="'. $find_text .'"/>
						<input type="hidden" name="post_type" value="'. $defaults['type'] .'" />
						<input type="submit" class="btn real-btn" alt="Search" value="Search" />
					</form>
				';
			$output .= '</div>';
			
		}
	
		if( $defaults['type'] == 'property' ) {
			
			$total_properties = $custom_posts->found_posts;
			
			$output .= sprintf('<div class="found-properties"><h4>Total Properties: %s</div>', $total_properties);
			
		}
	
		if( $defaults['type'] == 'agent' ) {
			
			$current_url = home_url() .'/agents/';
			$output .= '<div class="order-box option-bar small clearfix">';
				$output .= '<span class="selectwrap"><select id="sort-order" class="sort-order search-select">';

					$option_values = '';
					if( $url_sort == 'a-z' ) {
						$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
						$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
						$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
					} elseif( $url_sort == 'z-a' ) {
						$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
						$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
						$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
					} else {
						$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
						$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
						$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
					}
					$output .= $option_values;

				$output .= '</select></span>';
			$output .= '</div>';
			$output .= '<script>
						document.getElementById("sort-order").onchange = function() { if (this.selectedIndex!==0) { window.location.href = this.value; } };
						</script>';
			
		}
	
		$count = 1;
		// Loop through returned posts
		// Setup the inner HTML for each elements
		while ( $custom_posts->have_posts() ) : $custom_posts->the_post();
		
			$id = get_the_ID();
			$permalink = get_permalink();
			$title = get_the_title();
			
			// Show additional meta fields based on post type chosen
			$property_price = ''; $additional_meta = ''; $category_classes = ''; $broker = '';
			
			switch( $defaults['type'] ) {
				
				case "property" :
					$temp_dir = get_template_directory();
					$bed_icon = file_get_contents( $temp_dir .'/images/icon-bed.svg' );
					$bath_icon = file_get_contents( $temp_dir .'/images/icon-bath.svg' );
					$sqft_icon = file_get_contents( $temp_dir .'/images/icon-size.svg' );
					$property_agents = get_post_meta( $id, 'REAL_HOMES_agents' );
					$property_agents = array_filter( $property_agents, function($v){
					  return ( $v > 0 );
					});
					$property_agents = array_unique( $property_agents );
					$brokerage = get_post_meta( $property_agents[0], 'brk_office_name',true );
					
					// Number of days on market. $onsite.
					$strNow = current_time( 'mysql' );
					$strListingDate = get_field( 'REAL_HOMES_property_listing_date' );
					$dteStart = new DateTime($strNow); 
					$dteEnd   = new DateTime($strListingDate);
					$dteDiff  = $dteStart->diff($dteEnd)->days;
					$onsite = $dteDiff .' Days on Market'; 

					if( $onsite == '0 Days on Market' ) {
						$onsite = 'New Today';
					} 
					if( $onsite == '1 Days on Market' ) {
						$onsite = 'New Yesterday';
					}
					
					$newness = '';
					if( !empty( $strListingDate ) ) {
						$newness = sprintf( '<div class="newness">On Site: <strong>%s</strong></div>' , $onsite );
					}
					
					$property_status = inspiry_get_figure_caption( $id );
					$property_price = sprintf( '<h5 class="property-price">%s%s%s</h5>', get_property_price(), inspiry_get_property_types( $id ), $property_status );
					$bedrooms = floatval( get_post_meta( $id, 'REAL_HOMES_property_bedrooms', true ) );
					$bathrooms = floatval( get_post_meta( $id, 'REAL_HOMES_property_bathrooms', true ) );
					$square_feet = intval( get_post_meta( $id, 'REAL_HOMES_property_size', true ) );
					
						//if( $bedrooms != 0 && $bathrooms != 0 ) { $spacer = ' / '; } else { $spacer = ''; }
						$bedrooms = $bedrooms != 0 ? sprintf( '<span class="bd"><span>%s Bd</span></span>', $bedrooms ) : '';
						$bathrooms = $bathrooms != 0 ? sprintf( '<span class="ba"><span>%s Ba</span></span>', $bathrooms ) : '';
						$square_feet = sprintf('<span class="sqft">%s SqFt</span>', $square_feet );
					
					$additional_meta = sprintf( '%s<div class="extra-meta property-meta"><span class="bdba">%s%s</span>%s</div>', 
											$newness, $bedrooms, $bathrooms, $square_feet );
											
					$broker = sprintf( '<div class="brokerage-label bl-small"><p>%s</p><img src="%s/images/idx-small.gif" width="45" height="35" alt="Broker Reciprocity"></div>', 
									$brokerage, get_template_directory_uri() );
					break;
					
				case "agent" :
					$image_size = 'agent-image';
					$brokerage = get_field( 'brk_office_name' );
					$category_classes = sanitize_title( strip_tags( get_the_term_list( $id, 'agent_types', '', ' ', '' ) ) );
					$address = get_field( 'brk_office_address' );
					$phone = get_field( 'brk_office_phone' );
					//$agent_types = wp_get_post_terms( $id, 'agent_types', array("fields" => "all"));
					//$agent_type = $agent_types[0]->slug;	
					if( $phone )
						$phone = sprintf( '<div class="phone"><i class="fa fa-mobile"></i> <a href="tel:%s">%s</a></div>', preg_replace("/[^0-9]/", "", $phone), $phone );
					
					$additional_meta = sprintf( '
						<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
							$brokerage, $address, $phone );
					
					break;
					
				case "company" :
					$image_size = 'agent-image';
					$phone = get_field( 'company_office_phone' );
					$fax = get_field( 'company_office_fax' );
					$address = get_field( 'company_office_address' );
					$company_featured = get_field( 'company_featured_company' );
					if( $address )
						$address = sprintf( '<p class="address">%s</p>', $address );
					if( $phone )
						if( $company_featured == 1 ) {
							$phone = sprintf( '<div class="phone"><i class="fa fa-mobile"></i> <a href="tel:%s">%s</a></div>', preg_replace("/[^0-9]/", "", $phone), $phone );
						} else {
							$phone = sprintf( '<div class="phone"><i class="fa fa-mobile"></i> %s</div>', $phone );
						}
					if( $fax )
						$fax = sprintf( '<div class="fax"><i class="fa fa-print"></i> %s</div>', $fax );
					$additional_meta = sprintf( '
						<div class="extra-meta company-meta">%s<div>%s%s</div></div>', 
							$address, $phone, $fax );
					break;
					
			}
			
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $image_size, true);
			$image_parts = pathinfo( $image[0] );
			if( $image_parts['filename'] == 'default' ) $image = '';
			
			$has_image_class = !empty( $image ) ? 'with-image' : 'without-image';
			$image_src = !empty( $image ) ? $image[0] : '';
			$image_width = !empty( $image ) ? $image[1] : '';
			$image_height = !empty( $image ) ? $image[2] : '';
	
			// If its a post with no image use a placeholder image
			if( $defaults['type'] == 'post' && empty( $image ) ) {
				$image = get_stylesheet_directory_uri() .'/images/bh-placeholder.jpg';
				$has_image_class = 'with-image';
				$image_src = $image;
				$image_width = '244';
				$image_height = '163';
			}
			
			// Begin item output
			$output .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $classes, $has_image_class, $category_classes );
			
				if( empty( $defaults['featured_image'] ) && !empty( $image ) ) {
					$output .= sprintf( '<figure class="custom-post-image image-%s %s"><a href="%s"><img src="%s" width="%s" height="%s" /></a></figure>', 
									$count, $image_size, $permalink, $image_src, $image_width, $image_height );
				}
				
				if( $defaults['type'] == 'property' ) $output .= $property_price;
				
				$output .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', 
								$permalink, $title );
				
				if( empty( $defaults['show_excerpt'] ) ) {
					if( $defaults['type'] == 'property' || $defaults['type'] == 'post' && !empty(get_the_content()) ) {
						$output .= sprintf( '<p class="custom-post-excerpt">%s</p>', 
										get_framework_excerpt( $defaults['excerpt_length'] ) );
					}
				}
				
				$output .= $additional_meta;
				
				if( $defaults['type'] != 'property' ) {
					$output .= sprintf( '<a class="more-details" href="%s">View More <i class="fa fa-caret-right"></i></a>', 
									$permalink );
				}
								
				$output .= $broker;
			
			$output .= '</div></div>';
			// End item ouput
			
			
			$clearfix_test = $count / $cols_per_row;
			if( is_int( $clearfix_test ) ) {
				$output .= '<div class="clearfix"></div>';
			}
			
			$count++;
			
		endwhile;
	
	if(empty( $defaults['show_pagination'] )) {
		$output .= sprintf( '</div>%s</div>', get_theme_ajax_pagination( $custom_posts->max_num_pages) );
	} else {
		$output .= '</div></div>';
	}
	
	endif;
			
	return $output;
	
	wp_reset_query();
}


// Shortcode to display featured agents by featured-company and featured-agent
add_shortcode('BH_AGENTS', 'tbb_display_agents');
function tbb_display_agents( $defaults ) {
	$defaults = shortcode_atts( array(
		'limit' => '12',
		'offset' => '',
		'classes' => '',
		'columns' => '2',
		'order' => 'ASC',
		'orderby' => 'name',
		'show_search' => '',
		'filter' => ''
	), $defaults );
	
	$classes = sanitize_text_field( $defaults['classes'] );
	$order = sanitize_text_field( $defaults['order'] );
	$orderby = sanitize_text_field( $defaults['orderby'] );
	
	switch( $defaults['columns'] ) {
		case "6":
			$cols_per_row = 6;
			$cols = "six";
			break;
		case "5":
			$cols_per_row = 5;
			$cols = "five";
			break;
		case "4":
			$cols_per_row = 4;
			$cols = "four";
			break;
		case "3":
			$cols_per_row = 3;
			$cols = "three";
			break;
		case "2":
			$cols_per_row = 2;
			$cols = "two";
			break;
		case "1":
			$cols_per_row = 1;
			$cols = "one";
			break;
	}
	
	// Enable order A-Z & Z-A select field if url contains ?sort= param
	$url_sort = '';
	$url_sort = $_GET['sort'];
	
	if( $url_sort == 'a-z' ) {
		$sort_orderby = 'name';
		$sort_order = 'ASC';
	} elseif( $url_sort == 'z-a' ) {
		$sort_orderby = 'name';
		$sort_order = 'DESC';
	} else {
		$sort_orderby = $orderby;
		$sort_order = $order;
	}
	
	// Initialize the query array
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type' 	=> 'agent',
		'posts_per_page' => $defaults['limit'],
		'paged' 	=> $paged,
		'has_password' => false,
		'orderby' => $sort_orderby,
		'order' => $sort_order // ASC or DESC
	);
	
	// Adds offset to query
	if ( $defaults['offset'] ) {
		$args['offset'] =  $defaults['offset'];
	}
	
	switch( $defaults['filter'] ) {
		
		case "standard-agent" :
		
			// Display only standard agents in "agent_type"
			$args['tax_query'] = array(
				array(
					'taxonomy' 	=> 'agent_types',
					'field' 	=> 'slug',
					'terms' 	=> 'standard-agent'
				)
			);
			
			break;
		case "featured-agent" :	
		
			// Display only featured agents in "agent_type"
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'agent_types',
					'field' 	=> 'slug',
					'terms' 	=> 'featured-agent'
				)
			);
			
			break;
		case "company" :
		
			// Display only agents whose company is featured
			$args['meta_query'] = array(
				array(
					'key' => 'brk_office_is_featured',
					'value' => '1',
					'compare' => '=',
				)
			);
			
			break;
		case "all-featured" :
		
			// Display all agents whose company or agent_type is featured
			$args['meta_query'] = array(
				array(
					'key' => 'agent_is_featured',
					'value' => '1',
					'compare' => '=',
				)
			);
			
			break;
			
	} // end switch
	
	$featured_agents = new WP_Query( $args );
				
	if ( $featured_agents->have_posts() ) :
	
	$output = '<div class="custom-posts-wrapper post-agent"><div class="custom-posts-container clearfix">';
	
		if( !empty( $defaults['show_search'] ) ) {
			
			$output .= '<div class="custom-search-wrap">';
				$output .= '
					<form role="search" action="'. site_url('/') .'" method="get" id="searchform">
						<input type="text" class="search-field" name="s" placeholder="Find an agent"/>
						<input type="hidden" name="post_type" value="agent" />
						<input type="submit" class="btn real-btn" alt="Search" value="Search" />
					</form>
				';
			$output .= '</div>';
			
		}
	
		$current_url = home_url() .'/agents/';
		$output .= '<div class="order-box option-bar small clearfix">';
			$output .= '<span class="selectwrap"><select id="sort-order" class="sort-order search-select">';
	
				$option_values = '';
				if( $url_sort == 'a-z' ) {
					$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
					$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
					$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
				} elseif( $url_sort == 'z-a' ) {
					$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
					$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
					$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
				} else {
					$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
					$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
					$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
				}
				$output .= $option_values;
	
			$output .= '</select></span>';
		$output .= '</div>';
		$output .= '<script>
					document.getElementById("sort-order").onchange = function() { if (this.selectedIndex!==0) { window.location.href = this.value; } };
					</script>';
	
		$count = 1;
		// Loop through returned agents
		// Setup the inner HTML for each elements
		while ( $featured_agents->have_posts() ) : $featured_agents->the_post();
		
			$id = get_the_ID();
			$permalink = get_permalink();
			$title = get_the_title();
			$brokerage = get_field( 'brk_office_name' );
			$category_classes = sanitize_title( strip_tags( get_the_term_list( $id, 'agent_types', '', ' ', '' ) ) );
			$address = get_field( 'brk_office_address' );
			
			$phone = get_field( 'brk_office_phone' );
			if( $phone ) $phone = sprintf( '<div class="phone"><i class="fa fa-mobile"></i> <a href="tel:%s">%s</a></div>', 
									preg_replace("/[^0-9]/", "", $phone), $phone );
									
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'agent-image', true);
			$image_parts = pathinfo( $image[0] );
			if( $image_parts['filename'] == 'default' ) $image = '';
			
			if( !empty( $image ) ) {
				$has_image_class = 'with-image';
				$image_url = $image[0];
				$image_width = $image[1];
				$image_height = $image[2];	
			} else {
				$has_image_class = 'without-image';
				$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
				$image_width = '210';
				$image_height = '210';		
			}
			
			
			// Begin item output
			$output .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $classes, $has_image_class, $category_classes );
			
				$output .= sprintf( '<figure class="custom-post-image image-agent-image %s"><a href="%s"><img src="%s" width="%s" height="%s" alt="%s, for %s" /></a></figure>', 
								$count, $permalink, $image_url, $image_width, $image_height, $title, str_replace( array('\'', '\"'), '', $brokerage) );
								
				$output .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', 
								$permalink, $title );
				
				$output .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
									$brokerage, $address, $phone );
				
				$output .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', 
								$permalink );
			
			$output .= '</div></div>';
			// End item ouput
			
			
			$clearfix_test = $count / $cols_per_row;
			if( is_int( $clearfix_test ) ) {
				$output .= '<div class="clearfix"></div>';
			}
			
			$count++;
		
		endwhile;
	
	$output .= sprintf( '</div>%s</div>', get_theme_ajax_pagination( $featured_agents->max_num_pages) );
	
	endif;
			
	return $output;
	
	wp_reset_query();
}


// Creates mortgage calculator form with prepopulated form data from property
// References:
// http://www.loansanddebts.com/view.php?file=calculator_code.php
// http://ravingroo.com/decoded/download-simple-mortgage-payment-calculator.php
add_shortcode('MORT_CALC_FORM', 'tbb_mortgage_calc_form');
function tbb_mortgage_calc_form( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'id' => '',
		'class' => ''
	), $atts );
	
	$id = sanitize_text_field( $atts['id'] );
	$class = sanitize_text_field( $atts['class'] );
	$content = apply_filters('shortcode_content', $content);
	$span_check = !empty($content) ? 'span8' : 'span12';
	
	$sale_price              = intval( get_post_meta( $id, 'REAL_HOMES_property_price', true ) );
    $annual_interest_percent = 0; // percent
    $year_term               = 30;  // years
    $down_percent            = 20;  // percent
	$tax_insurance			 = 1.3;
	
	function get_interest_factor($year_term, $monthly_interest_rate) {
        global $base_rate;
        
        $factor      = 0;
        $base_rate   = 1 + $monthly_interest_rate;
        $denominator = $base_rate;
        for ($i=0; $i < ($year_term * 12); $i++) {
            $factor += (1 / $denominator);
            $denominator *= $base_rate;
        }
        return $factor;
    }
	
	$down_payment            = $sale_price * ($down_percent / 100);
	$financing_price         = $sale_price - $down_payment;
	$month_term              = $year_term * 12;
	$annual_interest_rate    = $annual_interest_percent / 100;
	$monthly_interest_rate   = $annual_interest_rate / 12;
	$monthly_factor          = get_interest_factor($year_term, $monthly_interest_rate);
	$tax_insurance_rate		 = $tax_insurance / 100;
	$tax_ins_per_month			 = ($sale_price / 12) * $tax_insurance_rate;
	
	// Monthly payment calculated with php including estimated tax + insurance
	$monthly_payment         = ($financing_price / $monthly_factor) + $tax_ins_per_month;
	
	ob_start(); ?>
	
	<div class="mort-calc-form-wrap clearfix <?php echo $class; if(!empty($content)) echo ' has-content'; ?>">
		<div class="mort-calc clearfix">
			
			<div class="row-fluid">
				<div class="<?php echo $span_check; ?>">
			
					<h3 class="text-center">Monthly Payment Estimator</h3>
					<h2 id="monthly-payment" class="text-center">
						<?php 
							if (is_nan($monthly_payment)) {
								echo "Enter interest rate below.";
							} else {
								echo "$".number_format($monthly_payment). "per month.";
							}
						?>
					</h2>
					<div class="text-center">
						<small>Loan Amount: <strong id="loan-amt">$<?php echo number_format($financing_price); ?></strong></small>
					</div>

					<div class="form-wrap">
						<form name="mortgagecalc" method="POST">

							<div class="row-fluid">
								<div class="form-item span12 price-item dollar"><label for="price" class="text-center">Listing Price</label>
									<input id="mort-price-value" type="text" onkeypress="return validNumber(event)" onChange="findpercentdown(); findloanamount(); myPayment();" onkeyup="this.onchange();" name="price" value="<?php echo $sale_price; ?>"> 
									<div class="smpc-error" id="priceError"></div>
								</div>
							</div>
							<div class="row-fluid">
								<div class="form-item span6 interest-item percent"><label for="rate">Interest Rate</label>
									<input id="mort-interest-value" type="text" onkeypress="return validNumber(event)" onChange="myPayment();" onkeyup="this.onchange();" name="rate" autocomplete="off" value="<?php echo $annual_interest_percent; ?>"> 
									<div class="smpc-error" id="rateError"></div>
								</div>
								<div class="form-item span6 term-item time"><label for="years">Loan Type</label>
									<span class="selectwrap">
										<select id="mort-term-value" class="search-select" onChange="myPayment();" onkeyup="this.onchange();" name="years">
											<option value="5">5 Years</option>
											<option value="10">10 Years</option>
											<option value="15">15 Years</option>
											<option value="20">20 Years</option>
											<option value="25">25 Years</option>
											<option value="<?php echo $year_term; ?>" selected="selected">30 Years</option>
											<option value="35">35 Years</option>
										</select>
									</span>
									<div class="smpc-error" id="yearsError"></div>
								</div>
							</div>
							<div class="row-fluid">
								<div class="form-item span6 down-item dollar"><label for="down">Down Payment</label>
									<div class="down">
										<input id="mort-down-value" type="text" onkeypress="return validNumber(event)" onChange="findpercentdown(); findloanamount(); myPayment();" onkeyup="this.onchange();" name="down" value="<?php echo $down_payment; ?>"> 
										<div id="down-percent">(<?php echo $down_percent; ?>%)</div>
									</div>
									<div class="smpc-error" id="downError"></div>
								</div>
								<div class="form-item span6 taxes-item percent"><label for="taxes">Est. Tax &amp; Insurance</label>
									<div class="taxes">
										<input id="mort-taxes-value" type="text" onkeypress="return validNumber(event)" onChange="findtaxpermonth(); myPayment();" onkeyup="this.onchange();" name="taxes" value="<?php echo $tax_insurance; ?>"> 
										<div id="taxes-per">($<?php echo round($tax_ins_per_month); ?>/mo.)</div>
									</div>
									<div class="smpc-error" id="taxesError"></div>
								</div>
							</div>

							<?php //<!--input type=button onClick="return myPayment()" value=Calculate--> ?>
						</form>
					</div>
				</div>
			
				<?php
				if( !empty( $content ) )
					echo sprintf('<div class="span4"><div class="mort-content-wrap"><div class="mort-content">%s</div></div></div>', $content );
				?>
			
			</div>
			
		</div><!-- end class mort-calc -->
	</div><!-- end class mort-calc-form-wrap -->
	
	<script type="text/javascript">
	function validNumber(fieldinput){ var unicode=fieldinput.charCode? fieldinput.charCode : fieldinput.keyCode;if ((unicode!=8) && (unicode!=46)) { if (unicode<48||unicode>57) return false; } }
		
	function addCommas(nStr){nStr+='';x=nStr.split('.');x1=x[0];x2=x.length>1?'.'+x[1]:'';var rgx=/(\d+)(\d{3})/;while(rgx.test(x1)){x1=x1.replace(rgx,'$1'+','+'$2');}return x1+x2;}
		
	function findpercentdown(){var price=document.mortgagecalc.price.value;var downpayment=document.mortgagecalc.down.value;var percentdown=(downpayment/price)*100;document.getElementById('down-percent').innerHTML = '('+percentdown.toFixed(0)+'%)';}
		
	function findtaxpermonth(){var price=document.mortgagecalc.price.value;var taxpercent=document.mortgagecalc.taxes.value;var taxpermonth=(price/12)*(taxpercent/100);document.getElementById('taxes-per').innerHTML = '($'+taxpermonth.toFixed(0)+'/mo)';}
		
	function findloanamount(){var price=document.mortgagecalc.price.value;var downpayment=document.mortgagecalc.down.value;var loanamount=price-downpayment;document.getElementById('loan-amt').innerHTML = '$'+addCommas(loanamount);}
		
	// Get initial property price from listing and calculate all variables for monthly payment and fill form
	var initprice = document.getElementById('IDX-detailsMainInfo').getElementsByClassName('IDX-text')[1].innerHTML.replace(/\D/g,'');
	var initdown = (initprice * ( 20 / 100 )).toFixed(0);
	var initloanprincipal = (initprice - initdown).toFixed(0);
	var initmonths = document.mortgagecalc.years.value * 12;
	var initinterest = document.mortgagecalc.rate.value / 1200;
	var inittaxpermonth = (initprice / 12) * (document.mortgagecalc.taxes.value / 100);
	// Calculate  initial mortgage payment and display result
	var initmonthlypayment = (initloanprincipal * initinterest / (1 - (Math.pow(1/(1 + initinterest), initmonths))) + inittaxpermonth).toFixed(0);
	
	document.getElementById('mort-price-value').value = initprice;
	document.getElementById('mort-down-value').value = initdown;
	if ((initmonthlypayment == 0) || (isNaN(initmonthlypayment))) {
		document.getElementById('monthly-payment').innerHTML = 'Enter interest rate below.';
	} else {
		document.getElementById('monthly-payment').innerHTML = '$' + addCommas(initmonthlypayment) + ' per month';	
	}
	document.getElementById('loan-amt').innerHTML = '$' + addCommas(initloanprincipal);
	document.getElementById('taxes-per').innerHTML = '$' + Math.round(inittaxpermonth) + '/mo.';
		
	//var estimatedpayment = '<div id="est-payment"><a href="#paymentmodal" data-toggle="modal"><i class="fa fa-calculator"></i> $' + addCommas(initmonthlypayment) + '/mo.</a></div>';
	var estimatedpayment = '<div id="est-payment"><a href="#paymentmodal" data-toggle="modal"><i class="fa fa-calculator"></i> Estimate $/mo.</a></div>';
	document.getElementById('IDX-detailsMainInfo').getElementsByClassName('IDX-field-listingPrice')[0].insertAdjacentHTML('beforeend', estimatedpayment);

	// Set up dynamic form
	function myPayment(){
	document.getElementById('priceError').innerHTML = ''; document.getElementById('downError').innerHTML = ''; document.getElementById('yearsError').innerHTML = ''; document.getElementById('rateError').innerHTML = '';

	// Form validation checking
	if ((document.mortgagecalc.price.value === null) || (document.mortgagecalc.price.value.length === 0) || (isNaN(document.mortgagecalc.price.value) === true)){
	} else if ((document.mortgagecalc.down.value === null) || (document.mortgagecalc.down.value.length === 0) || (isNaN(document.mortgagecalc.down.value) === true)){
	} else if ((document.mortgagecalc.years.value === null) || (document.mortgagecalc.years.value.length === 0) || (isNaN(document.mortgagecalc.years.value) === true)){
	} else if ((document.mortgagecalc.rate.value === null) || (document.mortgagecalc.rate.value.length === 0) || (isNaN(document.mortgagecalc.rate.value) === true)){
	} else if ((document.mortgagecalc.taxes.value === null) || (document.mortgagecalc.taxes.value.length === 0) || (isNaN(document.mortgagecalc.taxes.value) === true)){
	} else{
	// Set variables from form data
	var price = document.mortgagecalc.price.value;
	var downpayment = document.mortgagecalc.down.value;
	var loanprincipal = price - downpayment;
	var months = document.mortgagecalc.years.value * 12;
	var interest = document.mortgagecalc.rate.value / 1200;
	var taxpermonth = (price / 12) * (document.mortgagecalc.taxes.value / 100);
	// Calculate mortgage payment and display result
	var monthlypayment = '$' + (loanprincipal * interest / (1 - (Math.pow(1/(1 + interest), months))) + taxpermonth).toFixed(0)+' per month';
	document.getElementById('monthly-payment').innerHTML = addCommas(monthlypayment);
	}
	}
	</script>
	
	<?php
	return ob_get_clean();
}


// Creates the Share Bar on single properties
if ( ! function_exists( 'tbb_is_added_to_favorite' ) ) {
	/**
	 * Check if a property is already added to favorites of a given user
	 *
	 * @param $user_id
	 * @param $property_id
	 * @return bool
	 */
	function tbb_is_added_to_favorite( $user_id, $property_id ) {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM $wpdb->usermeta WHERE meta_key='favorite_properties' AND meta_value=" . $property_id . " AND user_id=" . $user_id );
		if ( isset( $results[ 0 ]->meta_value ) && ( $results[ 0 ]->meta_value == $property_id ) ) {
			return true;
		} else {
			return false;
		}
	}
}

add_shortcode('SHARE_BAR', 'tbb_share_bar');
function tbb_share_bar( $atts ) {
	$atts = shortcode_atts( array(
		'class' => ''
	), $atts );
	
	$class = sanitize_text_field( $atts['class'] );
	
	$fav_button = get_option('theme_enable_fav_button');
	$property_id = get_the_ID();
	$current_url = home_url().''.$_SERVER['REQUEST_URI'];
	
	ob_start(); ?>
	
	<div class="share-bar-wrap clearfix <?php echo $class; ?>">
		<!-- Share Dropdown -->
		<span class="actions">
			<div class="dropdown">
				<a id="share-bar-share" class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-share-square-o"></i> Share</a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					<li role="presentation"><a href="#share-bar-modal" data-toggle="modal"><i class="fa fa-envelope"></i> Email this listing</a></li>
					<li role="presentation"><a href="javascript:var w = window.open('http://www.facebook.com/sharer.php?u=<?php echo urlencode($current_url); ?>', 'sharer', 'toolbar=0,status=0,scrollbars=1,width=660,height=400'); w.focus();" title="Add to Facebook"><i class="fa fa-facebook"></i> Share on Facebook</a></li>
					<li role="presentation"><a href="javascript:var w = window.open('https://plusone.google.com/share?url=<?php echo urlencode($current_url); ?>', 'gplusshare', 'toolbar=0,status=0,scrollbars=1,width=600,height=450'); w.focus();" title="Share on Google+"><i class="fa fa-google-plus"></i> Share on Google+</a></li>
					<li role="presentation"><a href="javascript:var w = window.open('http://twitter.com/home?status=Check+out+this+real+estate+listing%3A+<?php echo urlencode($current_url); ?>', 'twittersharer', 'toolbar=0,status=0,scrollbars=1,width=400,height=325'); w.focus();" title="Share on Twitter"><i class="fa fa-twitter"></i> Share on Twitter</a></li>
					<li role="presentation"><a href="javascript:var w = window.open('http://pinterest.com/pin/create/button/?url=<?php echo urlencode($current_url); ?>&media=<?php echo the_post_thumbnail_url('full'); ?>&description=<?php echo urlencode(the_title()); ?>', 'pinterestshare', 'toolbar=0,status=0,scrollbars=1,width=748,height=450'); w.focus();" title="Share on Pinterest"><i class="fa fa-pinterest"></i> Share on Pinterest</a></li>
				</ul>
			</div>
		</span>
		
		<!-- Add to favorite -->
		<span class="add-to-fav actions">
			<?php
			if( is_user_logged_in() ){
				$user_id = get_current_user_id();
				if ( tbb_is_added_to_favorite( $user_id, $property_id ) ) {
					?>
					<div id="fav_output" class="show fav-btn"><i class="fa fa-heart"></i> <span id="fav_target">Favorited</span></div>
					<?php
				} else {
					?>
					<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" id="add-to-favorite-form">
						<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
						<input type="hidden" name="property_id" value="<?php echo $property_id; ?>" />
						<input type="hidden" name="action" value="add_to_favorite" />
					</form>
					<div id="fav_output"><i class="fa fa-heart-o"></i> <span id="fav_target" class="dim"></span></div>
					<a id="add-to-favorite" class="fav-btn" href="#"><i class="fa fa-heart-o"></i> Favorite</a>
				<?php
				}
			} else {
				?><a href="#login-modal" class="fav-btn" data-toggle="modal"><i class="fa fa-heart-o"></i> Favorite</a><?php
			}
			?>
		</span>
		
		<!-- Print -->
		<span class="actions">
			<a id="share-bar-print" href="javascript:window.print()"><i class="fa fa-print"></i> Print</a>
		</span>
	</div>
	
	<?php
	
	add_action('custom_footer_scripts', 'tbb_share_modal');
	function tbb_share_modal() {
		global $current_user;
		get_currentuserinfo();
		$current_url = home_url().''.$_SERVER['REQUEST_URI'];
		$current_user = wp_get_current_user();
		$current_user_email = is_user_logged_in() ? $current_user->user_email : '';
		
		ob_start(); ?>
		
		<div id="share-bar-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Share This" aria-hidden="true">
			<div class="modal-scrollable">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body"> 
					<h2 class="text-center">Email This Listing<small><?php echo the_title(); ?></small></h2>
					
					<div class="row-fluid share-boxes">
						<div id="email-form">
							<form action="" method="post" id="share-with-friend" >
								<h4 class="muted text-center">FROM</h4>
								<div class="row-fluid">
									<div class="form-item span6">
										<label for="yourname">Your Name:</label>
										<input type="text" name="yourname" id="yourname" class="required" />
									</div>
									<div class="form-item span6">
										<label for="youremail">Your Email:</label>
										<input type="text" name="youremail" id="youremail" class="required" value="<?php echo $current_user_email; ?>" />
									</div>
								</div>
								<h4 class="muted text-center">TO</h4>
								<div class="row-fluid">
									<div class="form-item span12">
										<label for="friendemail">Recipient's Email:</label>
										<input type="text" name="friendemail" id="friendemail" class="required" />
									</div>
								</div>
								<div class="row-fluid">
									<div class="form-item span12">
										<label for="message">Message:</label>
										<textarea name="message" id="message">Take a look at this property I found on BendHomes.com: <?php echo $current_url; ?></textarea>
									</div>
								</div>
								<div class="text-center">
									<input type="hidden" name="listingtitle" value="<?php echo the_title(); ?>" />
									<input type="button" value="Send Email" id="submit" class="btn real-btn btn-large" />
									<span id="success" style="color:#02888f;"></span>
								</div>
							</form>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
		$(document).ready(function(){$("#share-with-friend").validate({rules:{yourname:"required",youremail:{required:!0,email:!0},friendemail:{required:!0,email:!0}},errorPlacement:function(e,i){e.insertAfter($(i))}}),$("#submit").click(function(){return $("#submit").attr("disabled","disabled"),$("#share-with-friend").valid()?($("#share-with-friend").submit(),$.post("<?php echo plugins_url().'/tbb-functions/post.php'; ?>",$("#share-with-friend").serialize(),function(e){$("#success").html(e).fadeIn("slow"),setTimeout(function(){$("#share-bar-modal").modal("hide")},1500)}),!1):($("#submit").removeAttr("disabled"),!1)})});
		</script>
		
		<?php echo ob_get_clean();
	} // end wp_footer function
	
	return ob_get_clean();
	
	/* Unminified script used above for form validation
	<script type="text/javascript">
	$(document).ready(function(){
		$('#share-with-friend').validate({
			rules: {
				"yourname": "required",
				"youremail": {
					required: true,
					email: true
				},
				"friendemail": {
					required: true,
					email: true
				}
			},
			errorPlacement: function (error, element) {
				error.insertAfter($(element));
			}
		});


		$('#submit').click(function(){
			$('#submit').attr("disabled","disabled");
			if ( $('#share-with-friend').valid() ) {
				$('#share-with-friend').submit();
				$.post("<?php echo plugins_url().'/tbb-functions/post.php'; ?>", $("#share-with-friend").serialize(),  function(response) {   
					$('#success').html(response).fadeIn('slow');
					setTimeout(function() { $('#share-bar-modal').modal('hide'); }, 1500);
				}); return false;
			} else { $('#submit').removeAttr("disabled"); return false; }

		});
	});
	</script>*/
	
} // end SHARE_BAR shortcode


// Chruches directory pulled from google spreadsheet.
// Shortcode: [tbb_churches]
class TBB_Churches_List {
	
	public static $args;
	
    public function __construct() {
        add_shortcode( 'tbb_churches', array($this, 'render') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue') );
    }
	
	public function enqueue() {
		if( is_page( '888' ) ) {
			wp_enqueue_script( 'google-map-api', '//maps.googleapis.com/maps/api/js?key=AIzaSyBzmtlh7yHJ_EuPTJ3XsFF-YsVp-Hn-qtA', false );
			wp_enqueue_script( 'google-map-info-box', TBB_FUNCTIONS_URL .'js/infobox.min.js', array('google-map-api'), '', false );
			wp_enqueue_script( 'markerclusterer', TBB_FUNCTIONS_URL .'js/markerclusterer.js', array('google-map-api'), '', false );
			wp_enqueue_script( 'datatables', 'https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js', array('jquery'), '', false );
		}
	}
	
	// Render the shortcode
	public function render( $args ) {
				
		$html = '';
		
		$defaults = shortcode_atts(
			array(
				'class' => 'churches'
			), $args
		);

		extract( $defaults );
		
		//$google_key = '14ok04FVOzKjd_MzNNlI1-vQJ_4WTDSH3mDPRoWMRp_g';
		$google_key = '1UJ94-Y3lldgxCQMaqHfdx4Lla424t1CuAffIVa-fNxg';
		
		$url = 'https://spreadsheets.google.com/feeds/list/'. $google_key .'/1/public/basic?alt=json';
		
		$file = file_get_contents( $url );
		
		$json = json_decode($file);
		
		$rows = $json->{'feed'}->{'entry'};
		
		//print_r( $json ); // test to see the data returned from google sheet
		
		$current_url = home_url() .''. strtok($_SERVER['REQUEST_URI'], '?'); 
		
		// Add custom css from function below
		$html .= $this->css();
		
		// Get location url parameter if it exists
		$location_param = isset($_GET['location']) ? $_GET['location'] : '';
		
		// Hold json array as variable
		$json_array = $this->json_map_data( $rows, $location_param );
		
		// Get total count of churches
		$total_array = json_decode( $json_array, true );
		
		// Add json array to map script and print whole script here
		$html .= sprintf( '%s', $this->map_script( $json_array ) );
		
		$html .= '<div class="church-filters clearfix row-fluid">';
		
			$html .= '<div class="option-bar small"><span class="label-text">Filter Map by Area: </span><span class="selectwrap"><select name="church-filter" class="search-select" onchange="location=this.value;"><option value=""> - Select City - </option>';
		
			$locations = array();
			foreach( $rows as $location ) {
				$location_content = $location->{'content'}->{'$t'};
				$location_array = explode( ',', $location_content );
				$item = str_replace( 'location: ', '', $location_array[0] );
				if( in_array( $item, $locations ) )
					continue;
				
				$html .= sprintf( '<option value="%s?location=%s">%s</option>', $current_url, $item, $item );
				$locations[] = $item;
			}
		
			$html .= '</select></span></div>';
		
			if( !empty( $location_param ) ) {
				$html .= sprintf( ' <a href="%s" class="full-list"><i class="fa fa-angle-left"></i> View Full List</a>', $current_url );
				$html .= sprintf( '<div class="viewing">%s Churches in %s</div>', count( $total_array ), $location_param );
			} else {
				$html .= sprintf( '<div class="viewing">%s Total Churches</div>', count( $total_array ) );
			}
		
		$html .= '</div>'; // End church filter
		
		$html .= '<div id="church-map" class="map-wrap clearfix row-fluid">';
			$html .= '<div id="map-container"><div id="map"></div></div>';
		$html .= '</div>'; // End Map
		
		$html .= sprintf( '<div id="church-wrapper" class="row-fluid clearfix %s">', $class );
		
		$html .= '<table id="sortable-table" class="table table-bordered table-striped" width="100%" cellspacing="0">';
		
		$html .= '<thead><tr>
					<th class="name">Name</th>
					<th class="denom">Denomination</th>
					<th class="address">Address</th>
					<th class="city">City</th>
					<th class="contact" style="min-width:100px;">Contact</th>
				  </tr></thead><tbody>';
				
			foreach($rows as $row) {

				$name = $row->{'title'}->{'$t'};
				$content = $row->{'content'}->{'$t'};
				$content_array = explode( ',', $content );
				$location = str_replace( 'location: ', '', $content_array[0] );
				$denomination = str_replace( 'denomination: ', '', $content_array[1] );
				$address = str_replace( 'address: ', '', $content_array[2] );
				$city = str_replace( 'city: ', '', $content_array[3] );
				$state = str_replace( 'state: ', '', $content_array[4] );
				$zip = str_replace( 'zip: ', '', $content_array[5] );
				$phone = str_replace( 'phone: ', '', $content_array[6] );
				$url = str_replace( 'url: ', '', $content_array[7] );
				
				if( !empty( $location_param ) ) {
					// Filter by location if url param exists
					if( $location_param == $location ) {
						$html .= $this->church_item( $name, $denomination, $address, $city, $state, $zip, $phone, $url );
					}
				} else {
					// Otherwise just show everything
					$html .= $this->church_item( $name, $denomination, $address, $city, $state, $zip, $phone, $url );
				}

			}
		
		$html .= '</tbody></table>';
		
		$html .= '</div>'; // end church-wrapper
		
		// Outputs the entire shortcode here
		return $html;
		
	}
	
	// Single church item content inside this function so we don't have to duplicate it above
	private function church_item( $n, $d, $a, $c, $s, $z, $p, $u ) {
		$url = esc_url( str_replace( ' ', '', $u ) );
		$url = $url == 'http://none' ? '' : $url;
		$url = filter_var( $url, FILTER_VALIDATE_URL) ? '<a href="'.$url.'" target="_blank" class="btn btn-sm"><i class="fa fa-globe"></i> Website</a>' : '';
		
		$map_part = $n .' '. $c;
		$map_url = sprintf( 'https://www.google.com/maps/place/%s', urlencode( $map_part ) );
		
		$phone = $p == ' none' ? '' : '<a href="tel:'. preg_replace( '/\D/', '', $p ) .'">'. $p .'</a>';
		
		$output = '';
		$output .= '<tr class="church-item">';
				
			$output .= sprintf( '<td class="name"><div><strong>%s</strong></div>%s</td>', $n, $url );
		
			$output .= sprintf( '<td class="denomination">%s</td>', $d );

			$output .= '<td class="address">';
				$output .= sprintf( '<div>%s</div>', $a );
				if( !empty( $c ) ) $output .= sprintf( '<div>%s, %s %s<div><a href="%s" target="_blank">Get Directions</a></div></div>', 
													  $c, $s, $z, $map_url );
			$output .= '</td>';
			
			$output .= sprintf( '<td class="city">%s</td>', $c );
		
			$output .= sprintf( '<td class="contact"><p>%s</p></td>', $phone );

		$output .= '</tr>';
		
		return $output;
	}
	
	// Create array of json map data for google map
	private function json_map_data( $data, $param ) {
		$map_data = array();
		
		foreach( $data as $item ) {
			$map_item = array();
			// Map item title
			$map_item['title'] = $item->{'title'}->{'$t'};
			
			$map_content = $item->{'content'}->{'$t'};
			$map_content_array = explode( ',', $map_content );
			
			$city = str_replace( 'city: ', '', $map_content_array[3] );
			$state = str_replace( 'state: ', '', $map_content_array[4] );
			$zip = str_replace( 'zip: ', '', $map_content_array[5] );
			
			$map_item['address1'] = str_replace( 'address: ', '', $map_content_array[2] );
			$map_item['address2'] = $city .', '. $state .' '. $zip;
			
			// Get last 2 items in array, which is Latitude & Longitude
			$lat_long_array = array_slice( $map_content_array, -2 );
			$map_item['lat'] = str_replace( 'latitude: ', '', $lat_long_array[0] );
			$map_item['lng'] = str_replace( 'longitude: ', '', $lat_long_array[1] );
			
			// Create link to google maps using church title and city
			$url_part = $map_item['title'] .' '. $city;
			$map_item['url'] = sprintf( 'https://www.google.com/maps/place/%s/@%s,%s', 
									   urlencode( $url_part ), $map_item['lat'], $map_item['lng'] );
			
			$location = str_replace( 'location: ', '', $map_content_array[0] );
			
			if( !empty( $param ) ) {
				// If the location matches the location param add item to array
				if( $location == $param ) {
					$map_data[] = $map_item;
				}
			} else {
				// Otherwise add all map_item to map_data array to display all churches
				$map_data[] = $map_item;
			}
		}
		
		// Return the json array to be used in render function
		return json_encode( $map_data );
	}
	
	// Create all javascript using google-maps-api, google-map-info-box, & markerclusterer.
	// Add json_map_data function above as variable in myData.
	private function map_script( $json_array ) {
		ob_start(); 

		/********** Unminified code displayed here for editing.
		/********** Minified code below used for production.
		/********** Make edits to the UNMINIFIED code only, then reminify and replace code below
		/********** Remember the mapData array is using the php $json_array variable.
		
		/*function initChurchesMap() {

			// Properties Array
			var mapData = <?php //echo $json_array; ?>

			var location_center = new google.maps.LatLng(mapData[0].lat,mapData[0].lng);

			var mapOptions = {
				zoom: 15,
				maxZoom: 18,
				scrollwheel: false
			}

			var map = new google.maps.Map(document.getElementById("map"), mapOptions);

			var bounds = new google.maps.LatLngBounds();

			// Loop to generate marker and infowindow based on mapData array
			var markers = new Array();
			var info_windows = new Array();

			for (var i=0; i < mapData.length; i++) {

				markers[i] = new google.maps.Marker({
					position: new google.maps.LatLng(mapData[i].lat,mapData[i].lng),
					map: map,
					title: mapData[i].title,
					animation: google.maps.Animation.DROP,
					visible: true
				});

				bounds.extend(markers[i].getPosition());

				var boxText = document.createElement("div");
				boxText.className = 'map-info-window';

				var innerHTML = "";
				innerHTML += '<div class="prop-title">' + mapData[i].title + '</div>';
				innerHTML += '<div class="prop-address">' + mapData[i].address1 + '<br>'+ mapData[i].address2 +'</div>';
				innerHTML += '<div class="prop-link"><a href="' + mapData[i].url + '" target="_blank">Get Directions</a></div>';
				innerHTML += '<div class="arrow-down"></div>';

				boxText.innerHTML = innerHTML;

				var myOptions = {
					content: boxText,
					disableAutoPan: true,
					maxWidth: 0,
					alignBottom: true,
					pixelOffset: new google.maps.Size( -122, -48 ),
					zIndex: 10,
					closeBoxMargin: "0",
					closeBoxURL: "<?php echo get_template_directory_uri() . '/images/map/close.png'; ?>",
					infoBoxClearance: new google.maps.Size( 1, 1 ),
					isHidden: false,
					pane: "floatPane",
					enableEventPropagation: false
				};

				var ib = new InfoBox( myOptions );

				attachInfoBoxToMarker( map, markers[i], ib );
			}

			map.fitBounds(bounds);

			// Marker Clusters
			var markerClustererOptions = {
				ignoreHidden: true,
				maxZoom: 16,
				styles: [{
					textColor: '#ffffff',
					url: "<?php echo get_template_directory_uri() . '/images/map/cluster-icon.png'; ?>",
					height: 48,
					width: 48
				}]
			};

			var markerClusterer = new MarkerClusterer( map, markers, markerClustererOptions );

			function attachInfoBoxToMarker( map, marker, infoBox ){
				google.maps.event.addListener( marker, 'click', function(){
					var scale = Math.pow( 2, map.getZoom() );
					var offsety = ( (100/scale) || 0 );
					var projection = map.getProjection();
					var markerPosition = marker.getPosition();
					var markerScreenPosition = projection.fromLatLngToPoint( markerPosition );
					var pointHalfScreenAbove = new google.maps.Point( markerScreenPosition.x, markerScreenPosition.y - offsety );
					var aboveMarkerLatLng = projection.fromPointToLatLng( pointHalfScreenAbove );
					map.setCenter( aboveMarkerLatLng );
					infoBox.open( map, marker );
				});
			}

		}
		google.maps.event.addDomListener(window,"load",initChurchesMap);*/
			
		//********* Production minified code taken from above.
		//********* Only edit the js above then reminify and replace this minified code
		?>
		<script async defer type="text/javascript">
		$(document).ready(function() {
			$('#sortable-table').DataTable({
				"pageLength": 25,
				"lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "All"] ]
			});
			/*function paginateScroll() {
				$('html, body').animate({ scrollTop: $(".dataTables_wrapper").offset().top - 100 }, 500);
				$(".paginate_button").unbind('click', paginateScroll); $(".paginate_button").bind('click', paginateScroll);
			}paginateScroll();*/
		} );
		function initChurchesMap(){function e(e,o,n){google.maps.event.addListener(o,"click",function(){var t=Math.pow(2,e.getZoom()),a=100/t||0,i=e.getProjection(),r=o.getPosition(),l=i.fromLatLngToPoint(r),s=new google.maps.Point(l.x,l.y-a),g=i.fromPointToLatLng(s);e.setCenter(g),n.open(e,o)})}for(var o=<?php echo $json_array; ?>,n=(new google.maps.LatLng(o[0].lat,o[0].lng),{zoom:15,maxZoom:18,scrollwheel:!1}),t=new google.maps.Map(document.getElementById("map"),n),a=new google.maps.LatLngBounds,i=new Array,r=(new Array,0);r<o.length;r++){i[r]=new google.maps.Marker({position:new google.maps.LatLng(o[r].lat,o[r].lng),map:t,title:o[r].title,animation:google.maps.Animation.DROP,visible:!0}),a.extend(i[r].getPosition());var l=document.createElement("div");l.className="map-info-window";var s="";s+='<div class="prop-title">'+o[r].title+"</div>",s+='<div class="prop-address">'+o[r].address1+"<br>"+o[r].address2+"</div>",s+='<div class="prop-link"><a href="'+o[r].url+'" target="_blank">Get Directions</a></div>',s+='<div class="arrow-down"></div>',l.innerHTML=s;var g={content:l,disableAutoPan:!0,maxWidth:0,alignBottom:!0,pixelOffset:new google.maps.Size(-122,-48),zIndex:10,closeBoxMargin:"0",closeBoxURL:"<?php echo get_template_directory_uri() . '/images/map/close.png'; ?>",infoBoxClearance:new google.maps.Size(1,1),isHidden:!1,pane:"floatPane",enableEventPropagation:!1},d=new InfoBox(g);e(t,i[r],d)}t.fitBounds(a);var p={ignoreHidden:!0,maxZoom:16,styles:[{textColor:"#ffffff",url:"<?php echo get_template_directory_uri() . '/images/map/cluster-icon.png'; ?>",height:48,width:48}]};new MarkerClusterer(t,i,p)}google.maps.event.addDomListener(window,"load",initChurchesMap);
		</script>
		<?php
		
		return ob_get_clean();
	}
	
	// Shortcode CSS
	private function css() {
		ob_start(); ?>
		<style type="text/css">
			.church-filters .option-bar.small { float: right; }
			.church-filters .full-list { color: #999; font-size: 14px; }
			.church-filters .selectwrap { display:inline-block; }
			.selectbox-wrapper { border-bottom: 2px solid #dedede; }
			.selectbox-wrapper ul { margin: 0 !important; }
			.selectbox-wrapper ul li { padding: 4px 8px; }
			.selectbox-wrapper ul li:first-child { display: none; }
			.selectwrap input { margin-bottom: 0; }
			#church-map { padding-bottom: 30px; }
			#church-map #map { width: 100%; height: 400px; }
			.church-filters { padding-top: 20px; padding-bottom: 20px; border-top: 1px solid #ddd; margin-top: 30px; }
			.church-filters .viewing { padding-top: 2px; }
			.table-bordered { border-collapse: collapse; }
			.church-item .website { text-align: center; }
			.church-item .btn-sm { padding: 1px 7px; margin-top: 8px; letter-spacing: .05em; font-size: 12px; font-weight: 400; color: #02888f; }
			.church-item a { color: #02888f; }
			.denomination small, .church-filters .viewing, .church-filters span.label-text { color: #999; }
			.church-item .phone a, .church-item .phone a:hover, .church-item .phone a:active { color: #555; }
			.church-item .address a { font-size: 14px; }
			#map-container { background: url("<?php echo TBB_FUNCTIONS_URL; ?>images/loader.gif") no-repeat center center #f4f4f4; }
			#map .map-info-window { position: relative; background: #fff; width: 244px; border-bottom: 3px #4dc7ec solid; margin-bottom: 17px; box-shadow: 2px 5px 10px rgba(0,0,0,.25); }
			#map .map-info-window .prop-title { margin: 0; padding: 10px; text-align: center; font-size: 14px; }
			#map .map-info-window .prop-address, #map .map-info-window .prop-link { text-align: center; padding-bottom: 10px; font-size: 12px; }
			#map .map-info-window .arrow-down { width: 0; height: 0; border-style: solid; border-width: 10px 10px 0; border-color: #4dc7ec transparent transparent; position: absolute; bottom: -13px; left: 112px; }
			#map .infoBox img { z-index: 10; }
			.dataTables_length select { width: auto; margin: 0 5px; }
			table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc, table.dataTable thead .sorting_asc_disabled, table.dataTable thead .sorting_desc_disabled { background-repeat: no-repeat; background-position: center right; }
			table.dataTable thead .sorting_asc { background-image: url("<?php echo TBB_FUNCTIONS_URL; ?>images/sort_asc.png"); }
			table.dataTable thead .sorting_desc { background-image: url("<?php echo TBB_FUNCTIONS_URL; ?>images/sort_desc.png") }
			table.dataTable thead .sorting { background-image: url("<?php echo TBB_FUNCTIONS_URL; ?>images/sort_both.png"); }
			table.dataTable thead .sorting.address, table.dataTable thead .sorting.contact { background-image: none; }
			table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc { cursor: pointer; }
			.dataTables_wrapper .dataTables_length { float: left; margin-bottom: 7px; }
			.dataTables_wrapper .dataTables_filter { float: right; margin-bottom: 7px; }
			.dataTables_wrapper .dataTables_filter input { margin-bottom: 0; margin-left: 5px; }
			.dataTables_wrapper .dataTables_info { clear: both; float: left; font-size: 14px; }
			.dataTables_wrapper .dataTables_paginate { float: right;text-align: right; font-size: 14px; }
			.dataTables_wrapper .dataTables_paginate .paginate_button { color: #555; box-sizing: border-box; display: inline-block; padding: 1px 10px; margin-left: 5px; text-align: center; text-decoration: none !important; cursor: pointer; border: 1px solid transparent; border-radius: 2px; }
			.dataTables_wrapper .dataTables_paginate .paginate_button.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active { cursor: default; border: 1px solid transparent; background: transparent; box-shadow: none; }
			.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #e8e8e8; border: 1px solid #ddd; }
			.dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { color: #fff !important; border: 1px solid #02888f; background-color: #02888f; }
		</style>
		<?php
		$css = ob_get_clean();
		echo $css;
	}
	
}
new TBB_Churches_List();

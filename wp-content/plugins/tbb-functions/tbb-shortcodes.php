<?php
// Shortcodes


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
    
    <div style="text-align: center; margin-bottom: 1.5em;">
        <a href="https://www.evergreenhomeloans.com/bend/?ref=bh" target="_blank" onclick="trackOutboundLink('https://www.evergreenhomeloans.com/bend/?ref=bh', 'Evergreen 1'); return false;">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/evergreen-home-loans-logo.jpg" width="325" height="103" alt="Mortage Calculator Sponsored by Evergreen Home Loans" />
        </a>
        <div class="modal-address"><i class="fa fa-map-marker"></i> 685 SE 3rd St., Bend OR, 97702<br><a href="tel:5413185500" onclick="trackOutboundLink('tel:5413185500', 'Evergreen Ph'); return false;"><i class="fa fa-mobile-phone"></i> (541) 318-5500</a></div>
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
<form action="//bendbulletin.us1.list-manage.com/subscribe/post?u=a5d36976165603b3ce7485798&amp;id=5e8299d4c6" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
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
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a5d36976165603b3ce7485798_5e8299d4c6" tabindex="-1" value=""></div>
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
		'meta_key' => '',
		'meta_value' => '',
		'meta_value_type' => 'CHAR',
		'meta_compare' => '=',
		'classes' => '',
		'columns' => '2',
		'order' => 'ASC',
		'orderby' => 'name',
		'show_search' => ''
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
					
					$property_status = inspiry_get_figure_caption( $id );
					$property_price = sprintf( '<h5 class="property-price">%s%s%s</h5>', get_property_price(), inspiry_get_property_types( $id ), $property_status );
					$bedrooms = floatval( get_post_meta( $id, 'REAL_HOMES_property_bedrooms', true ) );
					$bathrooms = floatval( get_post_meta( $id, 'REAL_HOMES_property_bathrooms', true ) );
					$square_feet = intval( get_post_meta( $id, 'REAL_HOMES_property_size', true ) );
					
						//if( $bedrooms != 0 && $bathrooms != 0 ) { $spacer = ' / '; } else { $spacer = ''; }
						$bedrooms = $bedrooms != 0 ? sprintf( '<span class="bd">%s <span>%s Bd</span></span>', $bed_icon, $bedrooms ) : '';
						$bathrooms = $bathrooms != 0 ? sprintf( '<span class="ba">%s <span>%s Ba</span></span>', $bath_icon, $bathrooms ) : '';
						$square_feet = sprintf('<span class="sqft">%s %s SqFt</span>', $sqft_icon, $square_feet );
					
					$additional_meta = sprintf( '<div class="extra-meta property-meta"><span class="bdba">%s%s</span>%s</div>', 
											$bedrooms, $bathrooms, $square_feet );
											
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
			
			
			// Begin item output
			$output .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $classes, $has_image_class, $category_classes );
			
				if( empty( $defaults['featured_image'] ) && !empty( $image ) ) {
					$output .= sprintf( '<figure class="custom-post-image image-%s %s"><a href="%s"><img src="%s" width="%s" height="%s" /></a></figure>', 
									$count, $image_size, $permalink, $image[0], $image[1], $image[2] );
				}
				
				if( $defaults['type'] == 'property' ) $output .= $property_price;
				
				$output .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', 
								$permalink, $title );
				
				if( $defaults['type'] == 'property' && $defaults['excerpt_length'] != 0 && !empty(get_the_content()) ) {
					$output .= sprintf( '<p class="custom-post-excerpt">%s</p>', 
									get_framework_excerpt( $defaults['excerpt_length'] ) );
				}
				
				$output .= $additional_meta;
				
				if( $defaults['type'] != 'property' ) {
					$output .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', 
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
	
	$output .= sprintf( '</div>%s</div>', get_theme_ajax_pagination( $custom_posts->max_num_pages) );
	
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


// Creates mortgage calculator form with prepopulated form data from property in PHP
add_shortcode('MORT_CALC_FORM_PHP', 'tbb_mortgage_calc_form_php');
function tbb_mortgage_calc_form_php( $atts ) {
	$atts = shortcode_atts( array(
		'id' => '',
		'class' => ''
	), $atts );
	
	$id = sanitize_text_field( $atts['id'] );
	$class = sanitize_text_field( $atts['class'] );
	
	/* --------------------------------------------------- *
     * Set Form DEFAULT values
     * --------------------------------------------------- */
    $default_sale_price              = intval( get_post_meta( $id, 'REAL_HOMES_property_price', true ) );;
    $default_annual_interest_percent = 3.5;
    $default_year_term               = 30;
    $default_down_percent            = 20;
    $default_show_progress           = TRUE;
	
	/* --------------------------------------------------- *
     * Initialize Variables
     * --------------------------------------------------- */
    $sale_price                      = 0;
    $annual_interest_percent         = 0;
    $year_term                       = 0;
    $down_percent                    = 0;
    $this_year_interest_paid         = 0;
    $this_year_principal_paid        = 0;
    $form_complete                   = false;
    $show_progress                   = false;
    $monthly_payment                 = false;
    $show_progress                   = false;
    $error                           = false;
	
	/* --------------------------------------------------- *
     * Set the USER INPUT values
     * --------------------------------------------------- */
    if (isset($_REQUEST['form_complete'])) {
        $sale_price                      = $_REQUEST['sale_price'];
        $annual_interest_percent         = $_REQUEST['annual_interest_percent'];
        $year_term                       = $_REQUEST['year_term'];
        $down_percent                    = $_REQUEST['down_percent'];
        $show_progress                   = (isset($_REQUEST['show_progress'])) ? $_REQUEST['show_progress'] : false;
        $form_complete                   = $_REQUEST['form_complete'];
    }
	
	/* --------------------------------------------------- */
    // This function does the actual mortgage calculations
    // by plotting a PVIFA (Present Value Interest Factor of Annuity)
    // table...
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
	
	// If the form is complete, we'll start the math
    if ($form_complete) {
        // We'll set all the numeric values to JUST
        // numbers - this will delete any dollars signs,
        // commas, spaces, and letters, without invalidating
        // the value of the number
        $sale_price              = ereg_replace( "[^0-9.]", "", $sale_price);
        $annual_interest_percent = eregi_replace("[^0-9.]", "", $annual_interest_percent);
        $year_term               = eregi_replace("[^0-9.]", "", $year_term);
        $down_percent            = eregi_replace("[^0-9.]", "", $down_percent);
        
        if (((float) $year_term <= 0) || ((float) $sale_price <= 0) || ((float) $annual_interest_percent <= 0)) {
            $error = "You must enter a <b>Sale Price of Home</b>, <b>Length of Motgage</b> <i>and</i> <b>Annual Interest Rate</b>";
        }
        
        if (!$error) {
            $month_term              = $year_term * 12;
            $down_payment            = $sale_price * ($down_percent / 100);
            $annual_interest_rate    = $annual_interest_percent / 100;
            $monthly_interest_rate   = $annual_interest_rate / 12;
            $financing_price         = $sale_price - $down_payment;
            $monthly_factor          = get_interest_factor($year_term, $monthly_interest_rate);
            $monthly_payment         = $financing_price / $monthly_factor;
        }
    } else {
        if (!$sale_price)              { $sale_price              = $default_sale_price;              }
        if (!$annual_interest_percent) { $annual_interest_percent = $default_annual_interest_percent; }
        if (!$year_term)               { $year_term               = $default_year_term;               }
        if (!$down_percent)            { $down_percent            = $default_down_percent;            }
        if (!$show_progress)           { $show_progress           = $default_show_progress;           }
    }
    
    if ($error) {
        print("<font color=\"red\">" . $error . "</font><br><br>\n");
        $form_complete   = false;
    }
	
	ob_start(); ?>
	
	<div class="mort-calc-form-wrap <?php echo $class; ?>" style="margin:30px;border:1px solid #d2d2d2; padding:30px;">
		<div class="mort-calc">
			<h2>$/mo</h2>
			
			<form method="GET" name="information" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="hidden" name="form_complete" value="1">
				<table cellpadding="2" cellspacing="0" border="0" width="100%">
					<tr valign="top">
						<td align="right"><img src="/images/clear.gif" width="225" height="1" border="0" alt=""></td>
						<td align="smalltext" width="100%"><img src="/images/clear.gif" width="250" height="1" border="0" alt=""></td>
					</tr>
					<tr valign="top" bgcolor="#cccccc">
						<td align="center" colspan="2"><b>Purchase &amp; Financing Information</b></td>
					</tr>
					<tr valign="top" bgcolor="#eeeeee">
						<td align="right">Sale Price of Home:</td>
						<td width="100%"><input type="text" size="10" name="sale_price" value="<?php echo $sale_price; ?>">(In Dollars)</td>
					</tr>
					<tr valign="top" bgcolor="#eeeeee">
						<td align="right">Percentage Down:</td>
						<td><input type="text" size="5" name="down_percent" value="<?php echo $down_percent; ?>">%</td>
					</tr>
					<tr valign="top" bgcolor="#eeeeee">
						<td align="right">Length of Mortgage:</td>
						<td><input type="text" size="3" name="year_term" value="<?php echo $year_term; ?>">years</td>
					</tr>
					<tr valign="top" bgcolor="#eeeeee">
						<td align="right">Annual Interest Rate:</td>
						<td><input type="text" size="5" name="annual_interest_percent" value="<?php echo $annual_interest_percent; ?>">%</td>
					</tr>
					<tr valign="top" bgcolor="#eeeeee">
						<td align="right">Explain Calculations:</td>
						<td><input type="checkbox" name="show_progress" value="1" <?php if ($show_progress) { print("checked"); } ?>> 
						Show me the calculations and amortization</td>
					</tr>
					<tr valign="top" bgcolor="#eeeeee">
						<td>&nbsp;</td>
						<td><input type="submit" value="Calculate"><br>
						<?php if ($form_complete) { print("<a href=\"" . $_SERVER['PHP_SELF'] . "\">Start Over</a><br>"); } ?><br></td>
					</tr>
				<?php
					// If the form has already been calculated, the $down_payment
					// and $monthly_payment variables will be figured out, so we
					// can show them in this table
					if ($form_complete && $monthly_payment) {
				?>
						<tr valign="top">
							<td align="center" colspan="2" bgcolor="#000000"><font color="#ffffff"><b>Mortgage Payment Information</b></font></td>
						</tr>
						<tr valign="top" bgcolor="#eeeeee">
							<td align="right">Down Payment:</td>
							<td><b><?php echo "\$" . number_format($down_payment, "2", ".", ","); ?></b></td>
						</tr>
						<tr valign="top" bgcolor="#eeeeee">
							<td align="right">Amount Financed:</td>
							<td><b><?php echo "\$" . number_format($financing_price, "2", ".", ","); ?></b></td>
						</tr>
						<tr valign="top" bgcolor="#cccccc">
							<td align="right">Monthly Payment:</td>
							<td><b><?php echo "\$" . number_format($monthly_payment, "2", ".", ","); ?></b><br><font>(Principal &amp; Interest ONLY)</font></td>
						</tr>
						<?php
							if ($down_percent < 20) {
								$pmi_per_month = 55 * ($financing_price / 100000);
						?>
								<tr valign="top" bgcolor="#FFFFCC">
									<td align="right">&nbsp;</td>
									<td>
										<br>
									  Since you are putting LESS than 20% down, you will need to pay PMI
									  (<a href="http://www.google.com/search?hl=en&q=private+mortgage+insurance">Private Mortgage Insurance</a>), which tends
									   to be about $55 per month for every $100,000 financed (until you have paid off 20% of your loan). This could add
										<?php echo "\$" . number_format($pmi_per_month, "2", ".", ","); ?> to your monthly payment.
									</td>
								</tr>
								<tr valign="top" bgcolor="#FFFF99">
									<td align="right">Monthly Payment:</td>
									<td><b><?php echo "\$" . number_format(($monthly_payment + $pmi_per_month), "2", ".", ","); ?></b><br><font>
									(Principal &amp; Interest, and PMI)</td>
								</tr>
						<?php
							}
						?>
						<tr valign="top" bgcolor="#CCCCFF">
							<td align="right">&nbsp;</td>
							<td>
								<br>
								<?php
									$assessed_price          = ($sale_price * .85);
									$residential_yearly_tax  = ($assessed_price / 1000) * 14;
									$residential_monthly_tax = $residential_yearly_tax / 12;

									if ($pmi_per_month) {
										$pmi_text = "PMI and ";
									}
								?>
								Residential (or Property) Taxes are a little harder to figure out... In Massachusetts, the average resedential tax rate seems
								 to be around $14 per year for every $1,000 of your property's assessed value.
								<br><br>
								Let's say that your property's <i>assessed value</i> is 85% of what you actually paid for it - 
								<?php echo "\$" . number_format($assessed_price, "2", ".", ","); ?>. This would mean that your yearly residential taxes will be around
								 <?php echo "\$" . number_format($residential_yearly_tax, "2", ".", ","); ?>
								This could add <?php echo "\$" . number_format($residential_monthly_tax, "2", ".", ","); ?> to your monthly payment.
							</td>
						</tr>
						<tr valign="top" bgcolor="#9999FF">
							<td align="right">TOTAL Monthly Payment:</td>
							<td><b><?php echo "\$" . number_format(($monthly_payment + $pmi_per_month + $residential_monthly_tax), "2", ".", ","); ?></b><br><font>
							(including <?php echo $pmi_text; ?> residential tax)</font></td>
						</tr>
				<?php    
					}
				?>
				</table>
			</form>
			
			<?php
			// This prints the calculation progress and 
			// the instructions of HOW everything is figured out
			if ($form_complete && $show_progress) {
				$step = 1;
			?>
				<br><br>
				<table cellpadding="5" cellspacing="0" border="1" width="100%">
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>down payment</b>
							 = The price of the home multiplied by the percentage down divided by 100 (for 5% down becomes 5/100 or 0.05)<br><br>
							$<?php echo number_format($down_payment,"2",".",","); ?> = $<?php echo number_format($sale_price,"2",".",","); ?> X 
							(<?php echo $down_percent; ?> / 100)
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>interest rate</b> = The annual interest percentage divided by 100<br><br>
							<?php echo $annual_interest_rate; ?> = <?php echo $annual_interest_percent; ?>% / 100
						</td>
					</tr>
					<tr valign="top" bgcolor="#cccccc">
						<td colspan="2">
							The <b>monthly factor</b> = The result of the following formula:
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>monthly interest rate</b> = The annual interest rate divided by 12 (for the 12 months in a year)<br><br>
							<?php echo $monthly_interest_rate; ?> = <?php echo $annual_interest_rate; ?> / 12
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>month term</b> of the loan in months = The number of years you've taken the loan out for times 12<br><br>
							<?php echo $month_term; ?> Months = <?php echo $year_term; ?> Years X 12
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The montly payment is figured out using the following formula:<br>
							Monthly Payment = <?php echo number_format($financing_price, "2", "", ""); ?> * 
							(<?php echo number_format($monthly_interest_rate, "4", "", ""); ?> / 
							(1 - ((1 + <?php echo number_format($monthly_interest_rate, "4", "", ""); ?>)
							<sup>-(<?php echo $month_term; ?>)</sup>)))
							<br><br>
							The <a href="#amortization">amortization</a> breaks down how much of your monthly payment goes towards the bank's interest,
							 and how much goes into paying off the principal of your loan.
						</td>
					</tr>
				</table>
				<br>
			<?php
				// Set some base variables
				$principal     = $financing_price;
				$current_month = 1;
				$current_year  = 1;
				// This basically, re-figures out the monthly payment, again.
				$power = -($month_term);
				$denom = pow((1 + $monthly_interest_rate), $power);
				$monthly_payment = $principal * ($monthly_interest_rate / (1 - $denom));

				print("<br><br><a name=\"amortization\"></a>Amortization For Monthly Payment: <b>\$" . number_format($monthly_payment, "2", ".", ",") . "</b> over " . $year_term . " years<br>\n");
				print("<table cellpadding=\"5\" cellspacing=\"0\" bgcolor=\"#eeeeee\" border=\"1\" width=\"100%\">\n");

				// This LEGEND will get reprinted every 12 months
				$legend  = "\t<tr valign=\"top\" bgcolor=\"#cccccc\">\n";
				$legend .= "\t\t<td align=\"right\"><b>Month</b></td>\n";
				$legend .= "\t\t<td align=\"right\"><b>Interest Paid</b></td>\n";
				$legend .= "\t\t<td align=\"right\"><b>Principal Paid</b></td>\n";
				$legend .= "\t\t<td align=\"right\"><b>Remaing Balance</b></td>\n";
				$legend .= "\t</tr>\n";

				echo $legend;

				// Loop through and get the current month's payments for 
				// the length of the loan 
				while ($current_month <= $month_term) {        
					$interest_paid     = $principal * $monthly_interest_rate;
					$principal_paid    = $monthly_payment - $interest_paid;
					$remaining_balance = $principal - $principal_paid;

					$this_year_interest_paid  = $this_year_interest_paid + $interest_paid;
					$this_year_principal_paid = $this_year_principal_paid + $principal_paid;

					print("\t<tr valign=\"top\" bgcolor=\"#eeeeee\">\n");
					print("\t\t<td align=\"right\">" . $current_month . "</td>\n");
					print("\t\t<td align=\"right\">\$" . number_format($interest_paid, "2", ".", ",") . "</td>\n");
					print("\t\t<td align=\"right\">\$" . number_format($principal_paid, "2", ".", ",") . "</td>\n");
					print("\t\t<td align=\"right\">\$" . number_format($remaining_balance, "2", ".", ",") . "</td>\n");
					print("\t</tr>\n");

					($current_month % 12) ? $show_legend = FALSE : $show_legend = TRUE;

					if ($show_legend) {
						print("\t<tr valign=\"top\" bgcolor=\"#ffffcc\">\n");
						print("\t\t<td colspan=\"4\"><b>Totals for year " . $current_year . "</td>\n");
						print("\t</tr>\n");

						$total_spent_this_year = $this_year_interest_paid + $this_year_principal_paid;
						print("\t<tr valign=\"top\" bgcolor=\"#ffffcc\">\n");
						print("\t\t<td>&nbsp;</td>\n");
						print("\t\t<td colspan=\"3\">\n");
						print("\t\t\tYou will spend \$" . number_format($total_spent_this_year, "2", ".", ",") . " on your house in year " . $current_year . "<br>\n");
						print("\t\t\t\$" . number_format($this_year_interest_paid, "2", ".", ",") . " will go towards INTEREST<br>\n");
						print("\t\t\t\$" . number_format($this_year_principal_paid, "2", ".", ",") . " will go towards PRINCIPAL<br>\n");
						print("\t\t</td>\n");
						print("\t</tr>\n");

						print("\t<tr valign=\"top\" bgcolor=\"#ffffff\">\n");
						print("\t\t<td colspan=\"4\">&nbsp;<br><br></td>\n");
						print("\t</tr>\n");

						$current_year++;
						$this_year_interest_paid  = 0;
						$this_year_principal_paid = 0;

						if (($current_month + 6) < $month_term) {
							echo $legend;
						}
					}

					$principal = $remaining_balance;
					$current_month++;
				}
				print("</table>\n");
			}
			?>
			
		</div><!-- end class mort-calc -->
	</div><!-- end class mort-calc-form-wrap -->
	
	<?php
	return ob_get_clean();
}


// Creates mortgage calculator form with prepopulated form data from property
// References:
// http://www.loansanddebts.com/view.php?file=calculator_code.php
// http://ravingroo.com/decoded/download-simple-mortgage-payment-calculator.php
add_shortcode('MORT_CALC_FORM', 'tbb_mortgage_calc_form');
function tbb_mortgage_calc_form( $atts ) {
	$atts = shortcode_atts( array(
		'id' => '',
		'class' => ''
	), $atts );
	
	$id = sanitize_text_field( $atts['id'] );
	$class = sanitize_text_field( $atts['class'] );
	
	$sale_price              = intval( get_post_meta( $id, 'REAL_HOMES_property_price', true ) );
    $annual_interest_percent = 3.5;
    $year_term               = 30;
    $down_percent            = 20;
	
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
	
	// Monthly payment calculated with php
	$monthly_payment         = $financing_price / $monthly_factor;
	
	ob_start(); ?>
	
	<script type="text/javascript">
	function validNumber(fieldinput){ var unicode=fieldinput.charCode? fieldinput.charCode : fieldinput.keyCode;if ((unicode!=8) && (unicode!=46)) { if (unicode<48||unicode>57) return false; } }
		
	function addCommas(nStr){nStr+='';x=nStr.split('.');x1=x[0];x2=x.length>1?'.'+x[1]:'';var rgx=/(\d+)(\d{3})/;while(rgx.test(x1)){x1=x1.replace(rgx,'$1'+','+'$2');}return x1+x2;}
		
	function findpercentdown(){var price=document.mortgagecalc.price.value;var downpayment=document.mortgagecalc.down.value;var percentdown=(downpayment/price)*100;document.getElementById('down-percent').innerHTML = percentdown.toFixed(2)+'% Down';}

	function myPayment(){
	document.getElementById('priceError').innerHTML = ''; document.getElementById('downError').innerHTML = ''; document.getElementById('yearsError').innerHTML = ''; document.getElementById('rateError').innerHTML = '';

	// Form validation checking
	if ((document.mortgagecalc.price.value === null) || (document.mortgagecalc.price.value.length === 0) || (isNaN(document.mortgagecalc.price.value) === true)){
		document.getElementById('priceError').innerHTML = 'Numeric value required. Example: 165000';
	}
	else if ((document.mortgagecalc.down.value === null) || (document.mortgagecalc.down.value.length === 0) || (isNaN(document.mortgagecalc.down.value) === true)){
		document.getElementById('downError').innerHTML = 'Numeric value required. Example: 50000';
	} else if ((document.mortgagecalc.years.value === null) || (document.mortgagecalc.years.value.length === 0) || (isNaN(document.mortgagecalc.years.value) === true)){
		document.getElementById('yearsError').innerHTML = 'Numeric value required. Example: 30';
	} else if ((document.mortgagecalc.rate.value === null) || (document.mortgagecalc.rate.value.length === 0) || (isNaN(document.mortgagecalc.rate.value) === true)){
		document.getElementById('rateError').innerHTML = 'Numeric value required. Example: 3.25';
	} else{
	// Set variables from form data
	var price = document.mortgagecalc.price.value;
	var downpayment = document.mortgagecalc.down.value;
	var loanprincipal = price - downpayment;
	var months = document.mortgagecalc.years.value * 12;
	var interest = document.mortgagecalc.rate.value / 1200;
	// Calculate mortgage payment and display result
	var monthlypayment = '$' + (loanprincipal * interest / (1 - (Math.pow(1/(1 + interest), months)))).toFixed(0)+'/mo';
	document.getElementById('monthly-payment').innerHTML = addCommas(monthlypayment);
	}
	// payment = principle * monthly interest/(1 - (1/(1+MonthlyInterest)*Months))
	}	
	</script>
	
	<div class="mort-calc-form-wrap <?php echo $class; ?>" style="margin:30px;border:1px solid #d2d2d2; padding:30px;">
		<div class="mort-calc">
			<h2 id="monthly-payment">$<?php echo number_format($monthly_payment); ?>/mo</h2>
			<h3>Estimate Your <span>Monthly Payment</span></h3>
			
			<div class="smpc-div">
				<form name=mortgagecalc method=POST>
					<p>Listing Price<br>
						<input id="mort-price-value" type="text" onkeypress="return validNumber(event)" onChange="findpercentdown(); myPayment();" onkeyup="this.onchange();" name="price" value="<?php echo $sale_price; ?>"> 
						<span class="smpc-error" id="priceError"></span>
					</p>
					<p>Down Payment<br>
						<input id="mort-down-value" type="text" onkeypress="return validNumber(event)" onChange="findpercentdown(); myPayment();" onkeyup="this.onchange();" name="down" value="<?php echo $down_payment; ?>"> 
						<span id="down-percent"><?php echo $down_percent; ?>.00%</span> 
						<span class="smpc-error" id="downError"></span>
					</p>
					<p>Amortization<br>
						<select id="mort-term-value" onChange="myPayment();" onkeyup="this.onchange();" name="years">
							<option value="5">5 Years</option>
							<option value="10">10 Years</option>
							<option value="15">15 Years</option>
							<option value="20">20 Years</option>
							<option value="25">25 Years</option>
							<option value="<?php echo $year_term; ?>" selected="selected">30 Years</option>
							<option value="35">35 Years</option>
						</select>
						<span class="smpc-error" id="yearsError"></span>
					</p>
					<p>Interest Rate<br>
						<input id="mort-interest-value" type="text" onkeypress="return validNumber(event)" onChange="myPayment();" onkeyup="this.onchange();" name="rate" value="<?php echo $annual_interest_percent; ?>"> 
						<span class="smpc-error" id="rateError"></span>
					</p>
					<!--input type=button onClick="return myPayment()" value=Calculate-->
				</form>
			</div>
			
			<a href="<?php echo $_SERVER['REQUEST_URI']; ?>?explain-calcs=show">Explain Calculations</a> | 
			<a href="<?php echo $_SERVER['REQUEST_URI']; ?>?payment-breakdown=show">View Monthly Payment Breakdown</a>
			
			<?php
			$explain = ''; $breakdown = '';
			$explain = $_GET['explain-calcs'];
			$breakdown = $_GET['payment-breakdown'];
			
			if( $explain == 'show' ) { ?>
			
				<table cellpadding="5" cellspacing="0" border="1" width="100%">
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>down payment</b>
							 = The price of the home multiplied by the percentage down divided by 100 (for 5% down becomes 5/100 or 0.05)<br><br>
							$<?php echo number_format($down_payment,"2",".",","); ?> = $<?php echo number_format($sale_price,"2",".",","); ?> X 
							(<?php echo $down_percent; ?> / 100)
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>interest rate</b> = The annual interest percentage divided by 100<br><br>
							<?php echo $annual_interest_rate; ?> = <?php echo $annual_interest_percent; ?>% / 100
						</td>
					</tr>
					<tr valign="top" bgcolor="#cccccc">
						<td colspan="2">
							The <b>monthly factor</b> = The result of the following formula:
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>monthly interest rate</b> = The annual interest rate divided by 12 (for the 12 months in a year)<br><br>
							<?php echo $monthly_interest_rate; ?> = <?php echo $annual_interest_rate; ?> / 12
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The <b>month term</b> of the loan in months = The number of years you've taken the loan out for times 12<br><br>
							<?php echo $month_term; ?> Months = <?php echo $year_term; ?> Years X 12
						</td>
					</tr>
					<tr valign="top">
						<td><b><?php echo $step++; ?></b></td>
						<td>
							The montly payment is figured out using the following formula:<br>
							Monthly Payment = <?php echo number_format($financing_price, "2", "", ""); ?> * 
							(<?php echo number_format($monthly_interest_rate, "4", "", ""); ?> / 
							(1 - ((1 + <?php echo number_format($monthly_interest_rate, "4", "", ""); ?>)
							<sup>-(<?php echo $month_term; ?>)</sup>)))
							<br><br>
							The amortization breaks down how much of your monthly payment goes towards the bank's interest,
							 and how much goes into paying off the principal of your loan.
						</td>
					</tr>
				</table>
				
			<?php } 
			
			if( $breakdown == 'show' ) {
			
				// Set some base variables
				$principal     = $financing_price;
				$current_month = 1;
				$current_year  = 1;
				// This basically, re-figures out the monthly payment, again.
				$power = -($month_term);
				$denom = pow((1 + $monthly_interest_rate), $power);
				$monthly_payment = $principal * ($monthly_interest_rate / (1 - $denom));

				print("<br><br><a name=\"amortization\"></a>Amortization For Monthly Payment: <b>\$" . number_format($monthly_payment, "2", ".", ",") . "</b> over " . $year_term . " years<br>\n");
				print("<table cellpadding=\"5\" cellspacing=\"0\" bgcolor=\"#eeeeee\" border=\"1\" width=\"100%\">\n");

				// This LEGEND will get reprinted every 12 months
				$legend  = "\t<tr valign=\"top\" bgcolor=\"#cccccc\">\n";
				$legend .= "\t\t<td align=\"right\"><b>Month</b></td>\n";
				$legend .= "\t\t<td align=\"right\"><b>Interest Paid</b></td>\n";
				$legend .= "\t\t<td align=\"right\"><b>Principal Paid</b></td>\n";
				$legend .= "\t\t<td align=\"right\"><b>Remaing Balance</b></td>\n";
				$legend .= "\t</tr>\n";

				echo $legend;

				// Loop through and get the current month's payments for 
				// the length of the loan 
				while ($current_month <= $month_term) {        
					$interest_paid     = $principal * $monthly_interest_rate;
					$principal_paid    = $monthly_payment - $interest_paid;
					$remaining_balance = $principal - $principal_paid;

					$this_year_interest_paid  = $this_year_interest_paid + $interest_paid;
					$this_year_principal_paid = $this_year_principal_paid + $principal_paid;

					print("\t<tr valign=\"top\" bgcolor=\"#eeeeee\">\n");
					print("\t\t<td align=\"right\">" . $current_month . "</td>\n");
					print("\t\t<td align=\"right\">\$" . number_format($interest_paid, "2", ".", ",") . "</td>\n");
					print("\t\t<td align=\"right\">\$" . number_format($principal_paid, "2", ".", ",") . "</td>\n");
					print("\t\t<td align=\"right\">\$" . number_format($remaining_balance, "2", ".", ",") . "</td>\n");
					print("\t</tr>\n");

					($current_month % 12) ? $show_legend = FALSE : $show_legend = TRUE;

					if ($show_legend) {
						print("\t<tr valign=\"top\" bgcolor=\"#ffffcc\">\n");
						print("\t\t<td colspan=\"4\"><b>Totals for year " . $current_year . "</td>\n");
						print("\t</tr>\n");

						$total_spent_this_year = $this_year_interest_paid + $this_year_principal_paid;
						print("\t<tr valign=\"top\" bgcolor=\"#ffffcc\">\n");
						print("\t\t<td>&nbsp;</td>\n");
						print("\t\t<td colspan=\"3\">\n");
						print("\t\t\tYou will spend \$" . number_format($total_spent_this_year, "2", ".", ",") . " on your house in year " . $current_year . "<br>\n");
						print("\t\t\t\$" . number_format($this_year_interest_paid, "2", ".", ",") . " will go towards INTEREST<br>\n");
						print("\t\t\t\$" . number_format($this_year_principal_paid, "2", ".", ",") . " will go towards PRINCIPAL<br>\n");
						print("\t\t</td>\n");
						print("\t</tr>\n");

						print("\t<tr valign=\"top\" bgcolor=\"#ffffff\">\n");
						print("\t\t<td colspan=\"4\">&nbsp;<br><br></td>\n");
						print("\t</tr>\n");

						$current_year++;
						$this_year_interest_paid  = 0;
						$this_year_principal_paid = 0;

						if (($current_month + 6) < $month_term) {
							echo $legend;
						}
					}

					$principal = $remaining_balance;
					$current_month++;
				}
				print("</table>\n");
			
			} ?>
			
		</div><!-- end class mort-calc -->
	</div><!-- end class mort-calc-form-wrap -->
	
	<?php
	return ob_get_clean();
}
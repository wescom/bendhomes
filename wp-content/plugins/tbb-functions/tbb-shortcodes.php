<?php
// Shortcodes

add_shortcode('SCHEMA_ADDRESS', 'tbb_schema_address');
function tbb_schema_address( $atts ) {
	$atts = shortcode_atts( array(
		'address' => '',
		'city' => '',
		'state' => '',
		'zip' => '',
		'phone' => '',
		'fax' => '',
		'link' => ''
	), $atts );
	
	$address = sanitize_text_field( $atts['address'] );
	$city = sanitize_text_field( $atts['city'] );
	$state = sanitize_text_field( $atts['state'] );
	$zip = sanitize_text_field( $atts['zip'] );
	$phone = sanitize_text_field( $atts['phone'] );
	$phone_url = str_replace("-", '', $phone);
	$fax = sanitize_text_field( $atts['fax'] );
	$link = sanitize_text_field( $atts['link'] );
	
	ob_start(); ?>
    
    <div itemid="LocalBusiness" itemprop="location" itemscope="" itemtype="http://schema.org/LocalBusiness">
        <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
            <p><a itemprop="url" href="<?php echo $link; ?>" target="_blank"><span itemprop="streetAddress"><?php echo $address; ?></span> <br>
            
            <span itemprop="addressLocality"><?php echo $city; ?></span>, <span itemprop="addressRegion"><?php echo $state; ?></span> <span itemprop="postalCode"><?php echo $zip; ?></span></a><br>
            
            <?php if(!empty($phone)) { ?>
            <strong>Phone:</strong> <a href="tel://<?php echo $phone_url; ?>" data-track-event="set">
            <span itemprop="telephone"><?php echo $phone; ?></span></a> <br>
            <?php } ?>
            
            <?php if(!empty($fax)) { ?>
            <strong>Fax:</strong> <span itemprop="faxNumber"><?php echo $fax; ?></span></p>
            <?php } ?>
        </div>
    </div>
    
    <?php
	return ob_get_clean();
}


// Creates map link to open native maps app on mobile devices.
add_shortcode('MAP_LINK', 'tbb_map_link');
function tbb_map_link($atts, $content = null) {

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

	return '<a href="'. $link .'" class="'. implode(' ', $classes) .'">'. $content .'</a>' . $mobile_script;
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


// Display any post type in a 1-6 column grid
add_shortcode('BH_CUSTOM_POSTS', 'tbb_custom_posts');
function tbb_custom_posts( $defaults ) {
	$defaults = shortcode_atts( array(
		'type' => 'post',
		'limit' => '12',
		'offset' => '',
		'category_type' => '',
		'categories' => '',
		'featured_image' => '',
		'excerpt_length' => '12',
		'meta_key' => '',
		'meta_value' => '',
		'meta_compare' => '=',
		'classes' => '',
		'columns' => '3',
		'order' => 'ASC',
		'orderby' => 'name'
	), $defaults );
	
	$classes = sanitize_text_field( $defaults['classes'] );
	
	switch( $defaults['columns'] ) {
		case "6":
			$cols = "six";
			$image_size = 'grid-view-image';
			break;
		case "5":
			$cols = "five";
			$image_size = 'grid-view-image';
			break;
		case "4":
			$cols = "four";
			$image_size = 'grid-view-image';
			break;
		case "3":
			$cols = "three";
			$image_size = 'gallery-two-column-image';
			break;
		case "2":
			$cols = "two";
			$image_size = 'gallery-two-column-image';
			break;
		case "1":
			$cols = "one";
			$image_size = 'post-featured-image';
			break;
	}
	
	// Transform categories to array
	if ( $defaults['category_type'] && $defaults['categories'] ) {
		$cat_slugs = preg_replace( '/\s+/', '', $defaults['categories'] );
		$cat_slugs = explode( ',', $defaults['categories'] );
	} else {
		$cat_slugs = array();
	}
	
	// Initialize the query array
	$args = array(
		'post_type' 		=> $defaults['type'],
		'has_password' 		=> false,
		'order' => $defaults['order'],
		'orderby' => $defaults['orderby'],
		'limit' => $defaults['limit']
	);
	
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
			)
		);
	}
		
	$count = 1;

	$custom_posts = new WP_Query( $args );
	
	if ( $custom_posts->have_posts() ) :
	
	$output = '<div class="custom-posts-wrapper post-'. $defaults['type'] .'"><div class="custom-posts-container clearfix">';
	
		// Loop through returned posts
		// Setup the inner HTML for each elements
		while ( $custom_posts->have_posts() ) : $custom_posts->the_post();
			
			$permalink = get_permalink();
			
			$title = get_the_title();
			
			// Show additional meta fields based on post type chosen
			$additional_meta = '';
			switch( $defaults['type'] ) {
				
				case "property" :
					$bedrooms = floatval( get_post_meta( get_the_ID(), 'REAL_HOMES_property_bedrooms', true ) );
					$bathrooms = floatval( get_post_meta( get_the_ID(), 'REAL_HOMES_property_bathrooms', true ) );
						if( $bedrooms >= 1 ) $bedrooms = sprintf( '<span>%s Bd</span>', $bedrooms );
						if( $bathrooms >= 1 ) $bathrooms = sprintf( '<span>%s Ba</span>', $bathrooms );
						if( $bedrooms < 1 && $bathrooms < 1 ) $spacer = ' / ';
					$additional_meta = sprintf( '<h5 class="property-price">%s%s</h5><div class="extra-meta property-meta">%s%s%s</div>', 
							get_property_price(), inspiry_get_property_types( get_the_ID() ), $bedrooms, $spacer, $bathrooms );
					break;
					
				case "agent" :
					$image_size = 'agent-image';
					break;
					
				case "company" :
					$phone = get_post_meta( get_the_ID(), 'company_office_phone', true );
					$fax = get_post_meta( get_the_ID(), 'company_office_fax', true );
					if( !empty($phone))
						$phone = sprintf( '<div class="phone">Phone: %s</div>', $phone );
					if( !empty($fax))
						$fax = sprintf( '<div class="fax">Fax: %s</div>', $fax );
					$additional_meta = sprintf( '<div class="extra-meta agent-meta">%s%s</div>', $phone, $fax );
					break;
					
			}
			
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $image_size, true);
			
			$output .= sprintf( '<div class="custom-post custom-post-%s %s %s"><div class="custom-post-item clearfix">', $count, $cols, $classes );
			
				if( empty( $defaults['featured_image'] ) && !empty( $image ) ) {
				
					$output .= sprintf( '<figure class="custom-post-image image-%s %s"><a href="%s"><img src="%s" width="%s" height="%s" /></a></figure>', 
							$count, $image_size, $permalink, $image[0], $image[1], $image[2] );
			
				}
				
				$output .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', $permalink, $title );
				
				if( $defaults['excerpt_length'] != 0 ) {
					
					$output .= sprintf( '<p class="custom-post-excerpt">%s</p>', get_framework_excerpt( $defaults['excerpt_length'] ) );
				
				}
				
				$output .= $additional_meta;
				
				$output .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', $permalink );
			
			$output .= '</div></div>';
			
			$count++;
			if( ($count % $cols) == 0 ){
				echo '<div class="clearfix"></div>';
			}
			
		endwhile;
	
	$output .= '</div></div>';
	
	endif;
	
	return $output;
	
	//wp_reset_query();
	wp_reset_postdata();
}
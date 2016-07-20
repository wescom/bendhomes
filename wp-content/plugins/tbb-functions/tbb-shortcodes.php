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
		'orderby' => 'name',
		'show_search' => '',
		'all_featured_agents' => ''
	), $defaults );
	
	$classes = sanitize_text_field( $defaults['classes'] );
	
	switch( $defaults['columns'] ) {
		case "6":
			$cols_per_row = 6;
			$cols = "six";
			$image_size = 'grid-view-image';
			break;
		case "5":
			$cols_per_row = 5;
			$cols = "five";
			$image_size = 'grid-view-image';
			break;
		case "4":
			$cols_per_row = 4;
			$cols = "four";
			$image_size = 'grid-view-image';
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
			$image_size = 'post-featured-image';
			break;
	}
	
	// Transform categories to array
	if ( $defaults['category_type'] && $defaults['categories'] ) {
		//$cat_slugs = preg_replace( '/\s+/', '', $defaults['categories'] );
		$cat_slugs = explode( ',', $defaults['categories'] );
	} else {
		$cat_slugs = array();
	}
	
	// Initialize the query array
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type' 	=> $defaults['type'],
		'posts_per_page' => $defaults['limit'],
		'paged' 	=> $paged,
		'has_password' => false,
		'order' => $defaults['order'],
		'orderby' => $defaults['orderby']
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
	
	// Create merged array to display Featured Agents then Standard Agents all inside 1 loop with pagination
	// http://wordpress.stackexchange.com/questions/39483/broken-pagination
	/*if( !empty( $defaults['featured_agents'] ) ) {
		$terms = array(
          'featured-agent' => array(
            'class' => 'featured-agent-type',
            'title' => 'Featured Agents',
            'title_label' => 'Featured Agent'
          ),
          'standard-agent' => array(
            'class' => 'standard-agent-type',
            'title' => 'Standard Agents',
            'title_label' => 'Standard Agent'
          )
        );
	}
	
foreach($terms as $term_key => $term_val) {
		
	if( !empty( $defaults['featured_agents'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'agent_types',
				'terms' => $term_key,
				'field' => 'slug',
				'include_children' => false,
				'operator' => 'IN'
			)
		);	
	}*/
	

	$custom_posts = new WP_Query( $args );
	
	if ( $custom_posts->have_posts() ) :
	
	$output = '<div class="custom-posts-wrapper post-'. $defaults['type'] .'"><div class="custom-posts-container clearfix">';
	
		if( !empty( $defaults['show_search'] ) ) {
			
			$output .= '<div class="custom-search-wrap">';
				$output .= '
					<form role="search" action="'. site_url('/') .'" method="get" id="searchform">
						<input type="text" class="search-field" name="s" placeholder="Find a '. $defaults['type'] .'"/>
						<input type="hidden" name="post_type" value="'. $defaults['type'] .'" />
						<input type="submit" class="btn real-btn" alt="Search" value="Search" />
					</form>
				';
			$output .= '</div>';
			
		}
	
		$count = 1;
		// Loop through returned posts
		// Setup the inner HTML for each elements
		while ( $custom_posts->have_posts() ) : $custom_posts->the_post();
		
			$id = get_the_ID();
			$permalink = get_permalink();
			$title = get_the_title();
			
			// Show additional meta fields based on post type chosen
			$property_price = ''; $additional_meta = ''; $category_classes = '';
			
			switch( $defaults['type'] ) {
				
				case "property" :
					$property_price = sprintf( '<h5 class="property-price">%s%s</h5>', get_property_price(), inspiry_get_property_types( get_the_ID() ) );
					$bedrooms = floatval( get_post_meta( get_the_ID(), 'REAL_HOMES_property_bedrooms', true ) );
					$bathrooms = floatval( get_post_meta( get_the_ID(), 'REAL_HOMES_property_bathrooms', true ) );
						if( $bedrooms != 0 && $bathrooms != 0 ) { $spacer = ' / '; } else { $spacer = ''; }
						$bedrooms = $bedrooms != 0 ? sprintf( '<span>%s Bd</span>', $bedrooms ) : '';
						$bathrooms = $bathrooms != 0 ? sprintf( '<span>%s Ba</span>', $bathrooms ) : '';
					$additional_meta = sprintf( '<div class="extra-meta property-meta">%s%s%s</div>', $bedrooms, $spacer, $bathrooms );
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
					
							
					if( $defaults['all_featured_agents'] == 'yes' ) {
						wp_reset_query();
								
						// Query the Company of this Agent and see if the company is featured
						$company_post = new WP_Query( array(
							'post_type' => 'company',
							'name' => sanitize_title( $brokerage )
						) );
						
						if( $company_post->have_posts() ) :
							while( $company_post->have_posts() ) : $company_post->the_post();
								
								$company_is_featured = get_field( 'company_featured_company' );
																
							endwhile;
						endif;
						
						wp_reset_query();
					}
					
					// If the agent OR the agent's company is featured return Yes.
					if( $category_classes == 'featured-agent' || $company_is_featured == 1 ) {
						$check = 'Yes';	
					} else {
						$check = 'No';
					}
					
					$additional_meta = sprintf( '
						<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s ( %s )</div>', 
							$brokerage, $address, $phone, $check );
					
					break;
					
				case "company" :
					$image_size = 'medium';
					$phone = get_field( 'company_office_phone' );
					$fax = get_field( 'company_office_fax' );
					$address = get_field( 'company_office_address' );
					if( $address )
						$address = sprintf( '<p class="address">%s</p>', $address );
					if( $phone )
						$phone = sprintf( '<div class="phone"><i class="fa fa-mobile"></i> <a href="tel:%s">%s</a></div>', preg_replace("/[^0-9]/", "", $phone), $phone );
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
			
			$has_image_class = !empty( $image ) ? 'with-image' : '';
			
			
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
	
	$output .= sprintf( '</div>%s</div>', get_theme_ajax_pagination( $custom_posts->max_num_pages) );
	
	endif;
			
	return $output;
	
	wp_reset_query();
}


// Shortcode to display featured agents by featured-company and featured-agent
add_shortcode('FEATURED_AGENTS', 'tbb_featured_agents');
function tbb_featured_agents( $defaults ) {
	$defaults = shortcode_atts( array(
		'limit' => '12',
		'classes' => '',
		'columns' => '2',
		'order' => 'ASC',
		'orderby' => 'name',
		'show_search' => ''
	), $defaults );
	
	$classes = sanitize_text_field( $defaults['classes'] );
	
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
	
	// Initialize the query array
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	
	$args = array(
		'post_type' 	=> 'agent',
		'posts_per_page' => $defaults['limit'],
		'paged' 	=> $paged,
		'has_password' => false,
		'order' => $defaults['order'],
		'orderby' => $defaults['orderby']
	);
	
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
	
		$count = 1;
		// Loop through returned posts
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
			$has_image_class = !empty( $image ) ? 'with-image' : '';
			
			//wp_reset_query();				
			// Query the Company of this Agent and see if the company is featured
			$company_post = new WP_Query( array(
				'post_type' => 'company',
				'name' => sanitize_title( $brokerage )
			) );
			if( $company_post->have_posts() ) :
				while( $company_post->have_posts() ) : $company_post->the_post();
					
					$company_is_featured = get_field( 'company_featured_company' );
													
				endwhile;
			endif;
			//wp_reset_query();
			
			// If the company OR the agent is featured then display them
			if( $category_classes == 'featured-agent' || $company_is_featured == 1 ) {
			
			
				// Begin item output
				$output .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
								$count, $cols, $classes, $has_image_class, $category_classes );
				
					if( !empty( $image ) ) {
						$output .= sprintf( '<figure class="custom-post-image image-agent-image %s"><a href="%s"><img src="%s" width="%s" height="%s" /></a></figure>', 
										$count, $permalink, $image[0], $image[1], $image[2] );
					}
									
					$output .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', 
									$permalink, $title );
					
					$output .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s (%s)</div>', 
										$brokerage, $address, $phone, $company_is_featured );
					
					$output .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', 
									$permalink );
				
				$output .= '</div></div>';
				// End item ouput
			
			
			}
			
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
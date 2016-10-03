<?php // Search results loop for Agents post type

global $post;
    
$id = get_the_ID();
$permalink = get_permalink();
$title = get_the_title();
$featured_agent = get_field( 'agent_is_featured' );
$image_size = 'agent-image';
$brokerage = get_field( 'brk_office_name' );
$category_classes = sanitize_title( strip_tags( get_the_term_list( $id, 'agent_types', '', ' ', '' ) ) );
$address = get_field( 'brk_office_address' );
$phone = get_field( 'brk_office_phone' );
$mobile_phone = get_field( 'REAL_HOMES_mobile_number' );
if( $phone )
	$phone = sprintf( '<div class="phone"><i class="fa fa-mobile"></i> <a href="tel:%s">%s</a></div>', preg_replace("/[^0-9]/", "", $phone), $phone );
if( $mobile_phone )
	$mobile_phone = sprintf( '<div class="mobile"><i class="fa fa-mobile"></i> <a href="tel:%s">%s</a></div>', preg_replace("/[^0-9]/", "", $mobile_phone), $mobile_phone );
	
$mobile = $featured_agent == 1 ? $mobile_phone : '';
				
$additional_meta = sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s%s</div>', $brokerage, $address, $phone, $mobile );

$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $image_size, true);
$image_parts = pathinfo( $image[0] );
if( $image_parts['filename'] == 'default' ) $image = '';

$has_image_class = !empty( $image ) ? 'with-image' : '';
?>



	<?php
	// Begin item output
	$output = sprintf( '<article class="custom-post one %s %s %s"><div class="custom-post-item clearfix">', 
					$classes, $has_image_class, $category_classes );
	
		if( !empty( $image ) ) {
			$output .= sprintf( '<figure class="custom-post-image %s"><a href="%s"><img src="%s" width="%s" height="%s" /></a></figure>', 
							$image_size, $permalink, $image[0], $image[1], $image[2] );
		}
				
		$output .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', 
						$permalink, $title );
		
		$output .= $additional_meta;
		
		$output .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', 
						$permalink );
	
	$output .= '</div></article>';
	// End item ouput
	
	echo $output;
	?>


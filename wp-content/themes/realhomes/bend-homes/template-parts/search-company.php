<?php // Search results loop for Company post type

global $post;
    
$id = get_the_ID();
$permalink = get_permalink();
$title = get_the_title();
		
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


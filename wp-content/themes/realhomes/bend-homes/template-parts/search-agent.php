<?php // Search results loop for Agents post type

global $post;
    
$id = get_the_ID();
$permalink = get_permalink();
$title = get_the_title();
		
$image_size = 'agent-image';
$brokerage = get_field( 'brk_office_name' );
$category_classes = sanitize_title( strip_tags( get_the_term_list( $id, 'agent_types', '', ' ', '' ) ) );
$address = get_field( 'brk_office_address' );
$phone = get_field( 'brk_office_phone' );
if( $phone )
	$phone = sprintf( '<div class="phone"><i class="fa fa-mobile"></i> <a href="tel:%s">%s</a></div>', preg_replace("/[^0-9]/", "", $phone), $phone );
				
$additional_meta = sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', $brokerage, $address, $phone );

$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $image_size, true);
$image_parts = pathinfo( $image[0] );
if( $image_parts['filename'] == 'default' ) $image = '';

$has_image_class = !empty( $image ) ? 'with-image' : '';
?>

<article <?php post_class( array('custom-post', 'one') ); ?>>


	<?php
	// Begin item output
	$output = sprintf( '<div class="custom-post %s %s %s %s"><div class="custom-post-item clearfix">', 
					$cols, $classes, $has_image_class, $category_classes );
	
		if( !empty( $image ) ) {
			$output .= sprintf( '<figure class="custom-post-image image-%s %s"><a href="%s"><img src="%s" width="%s" height="%s" /></a></figure>', 
							$count, $image_size, $permalink, $image[0], $image[1], $image[2] );
		}
				
		$output .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', 
						$permalink, $title );
		
		$output .= $additional_meta;
		
		$output .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', 
						$permalink );
	
	$output .= '</div></div>';
	// End item ouput
	
	echo $output;
	?>

</article>
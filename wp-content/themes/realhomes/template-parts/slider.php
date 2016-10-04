<?php 
// Queries properties my MLS# using custom settings page located at Properties > Property Settings.
// Settings admin page is built in function.php in theme realhomes-child

$banner_mls_nums = get_option('banner_mls_numbers');
$mls_numbers = explode( ',', $banner_mls_nums );

$wpIds = array();

foreach( $mls_numbers as $num) {
	$args = array(
        'post_type' => 'property',
        'meta_query' => array(
            array(
                'key' => 'REAL_HOMES_property_id',
                'value' => $num
            )
        )
    );
    $getPosts = new WP_Query($args);
    if( $getPosts->have_posts() ) {
        while( $getPosts->have_posts() ) {
          	$getPosts->the_post();
          	echo "xx".get_the_ID();
          	array_push($wpIds, get_the_ID());
        } // end while
    } else {
      	//skip.  MLS must not be correct;
    }
	//
}
print_r($wpIds);

$slider_args = array(
	'post_type' => 'property',
	'posts_per_page' => -1,
	'nopaging' => true,
	'post__in' => array(277494,267314,278921,277494,277785,277413),
	'orderby' => 'post__in'
);

$mls_query = array();

foreach( $mls_numbers as $k => $v ) {
	$mls_query[$k]['key'] = 'REAL_HOMES_property_id';
	$mls_query[$k]['value'] = $v;
	$mls_query[$k]['compare'] = '=';
}

$slider_args['meta_query'] = $mls_query;
$slider_args['meta_query']['relation'] = 'OR';

print_r( $slider_args );

$slider_query = new WP_Query( $slider_args );

// The Loop
if ( $slider_query->have_posts() ) { ?>

<div id="home-flexslider" class="clearfix">
    <div class="flexslider loading">
        <ul class="slides">

		<?php
			while ( $slider_query->have_posts() ) {
				$slider_query->the_post();
								
				$slider_image_id = get_post_meta( $post->ID, 'REAL_HOMES_slider_image', true );
				$image_id = !empty( $slider_image_id ) ? $slider_image_id : get_post_thumbnail_id();			
				
				$slider_image = wp_get_attachment_image_src( $image_id, 'large', true);
				$image_parts = pathinfo( $slider_image[0] );
				if( $image_parts['filename'] == 'default' )
					$slider_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large', true);
				?>
                
				<li>
                	<div class="desc-wrap">
                        <div class="slide-description">
                            <h3><a href="<?php the_permalink(); ?>"><?php bh_the_title(); ?></a></h3>
                            <p><?php framework_excerpt(15); ?></p>
                            <?php
                            $price = get_property_price();
                            if ( $price ){
                                echo '<span>'.$price.'</span>';
                            }
                            $sliderMLS = get_post_meta( $post->ID, 'REAL_HOMES_property_id', true );
                            echo ' - <span>'.$sliderMLS.'</span>';
                            echo ' - <span>'.$post->ID.'</span>';

                            brokerage_label( $post->ID, 'large' );
                            ?>
                            <a href="<?php the_permalink(); ?>" class="know-more">View Property</a>
                        </div>
                    </div>
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo $slider_image[0]; ?>" width="<?php echo $slider_image[1]; ?>" height="<?php echo $slider_image[2]; ?>" alt="<?php the_title(); ?>">
                    </a>
                </li>
                
                <?php
				
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>
    
        </ul>
    </div>
</div><!-- End Slider -->

<?php } else {
	get_template_part('banners/default_page_banner');
}
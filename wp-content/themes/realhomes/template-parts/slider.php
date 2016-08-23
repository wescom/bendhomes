<?php 
// Queries properties my MLS# using custom settings page located at Properties > Property Settings.
// Settings admin page is built in function.php in theme realhomes-child

$banner_mls_nums = get_option('banner_mls_numbers');
$mls_numbers = explode( ',', $banner_mls_nums );

$meta_key = 'REAL_HOMES_property_id';

$slider_args = array(
	'post_type' => 'property',
	'posts_per_page' => -1,
	'nopaging' => true,
	'orderby' => 'none'
);

$mls_query = array();

foreach( $mls_numbers as $k => $v ) {
	$mls_query[$k]['key'] = $meta_key;
	$mls_query[$k]['value'] = $v;
	$mls_query[$k]['compare'] = '=';
}

$slider_args['meta_query'] = $mls_query;
$slider_args['meta_query']['relation'] = 'OR';

$slider_query = new WP_Query( $slider_args );

$slider_ids = array();
while (have_posts()) : the_post();
  $slider_ids[] = get_the_ID();
endwhile;

function tbb_sort_slider_by_meta_key( array $ids, $meta_key ) {
    $user_order = array();
    foreach( $ids as $id ) {
        $user_order[$id] = intval( get_post_meta( $id, $meta_key, true ) );
    }
    asort( $user_order );

    return array_keys( $user_order );
}

return tbb_sort_slider_by_meta_key( $slider_ids, $meta_key );


// The Loop
if ( $slider_query->have_posts() ) { ?>

<div id="home-flexslider" class="clearfix">
    <div class="flexslider loading">
        <ul class="slides">

		<?php
			while ( $slider_query->have_posts() ) {
				$slider_query->the_post();
				
				$image_id = get_post_meta( $post->ID, 'REAL_HOMES_slider_image', true );
				if( !$image_id ) $image_id = get_post_thumbnail_id();
				$slider_image = wp_get_attachment_image_src( $image_id, 'large', true);
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



/*if($slider_query->have_posts()){
    ?>
    <!-- Slider -->
    <!-- <?php print_r($slider_query); ?> -->
    <div id="home-flexslider" class="clearfix">
        <div class="flexslider loading">
            <ul class="slides">
                <?php
                while ( $slider_query->have_posts() ) :
                    $slider_query->the_post();
                    $slider_image_id = get_post_meta( $post->ID, 'REAL_HOMES_slider_image', true );
                    if($slider_image_id){
						$slider_image = wp_get_attachment_image_src( $slider_image_id, 'large', true);
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
                                    brokerage_label( $post->ID, 'large' );
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="know-more"><?php _e('Know More','framework'); ?></a>
                                </div>
                            </div>
                            <a href="<?php the_permalink(); ?>">
                            	<img src="<?php echo $slider_image[0]; ?>" width="<?php echo $slider_image[1]; ?>" height="<?php echo $slider_image[2]; ?>" alt="<?php the_title(); ?>">
                            </a>
                        </li>
                        <?php
                    }
                endwhile;
                wp_reset_query();
                ?>
            </ul>
        </div>
    </div><!-- End Slider -->
    <?php
}else{
    get_template_part('banners/default_page_banner');
}*/
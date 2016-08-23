<?php 

$number_of_slides = intval(get_option('theme_number_of_slides'));
if(!$number_of_slides){
    $number_of_slides = -1;
}

$banner_mls_nums = get_option('banner_mls_numbers');
$mls_numbers = explode( ',', $banner_mls_nums );

print_r($mls_numbers);

$slider_args = array(
    /*'post_type' => 'property',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'REAL_HOMES_property_id',
            'value' => $mls_numbers,
            'compare' => 'IN'
        )
    )*/
	'post_type' => 'property',
	'posts_per_page' => -1,
	'nopaging' => true,
	/*'meta_query' => array(
        array(
            'key' => 'REAL_HOMES_property_id',
            'value' => $mls_numbers,
			'compare' =>'EXITSTS'
        )
    )*/
);

$mls_query = array();
foreach( $mls_numbers as $k => $v ) {
	$mls_query[$k]['key'] = 'REAL_HOMES_property_id';
	$mls_query[$k]['value'] = $v;
	$mls_query[$k]['compare'] = '=';
}

$slider_args['meta_query'] = $mls_query;
$slider_args['meta_query']['relation'] = 'OR';

print_r($slider_args);


$slider_query = new WP_Query( $slider_args );

if($slider_query->have_posts()){
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
}
?>

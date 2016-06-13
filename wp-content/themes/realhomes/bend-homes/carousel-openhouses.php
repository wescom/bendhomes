<?php
/* Featured Properties Query Arguments */
$openhouse_properties_args = array(
    'post_type' => 'property',
    'posts_per_page' => 12,
    'tax_query' => array(
      array(
        'taxonomy' => 'property-status',
        'field' => 'slug',
        'terms' => 'open-house'
      )
    )
);

$openhouse_properties_query = new WP_Query( $openhouse_properties_args );

if ( $openhouse_properties_query->have_posts() ) :
    ?>
    <section class="featured-properties-carousel clearfix">
        <?php
        $openhouse_prop_title = get_option('theme_openhouse_prop_title');
        $openhouse_prop_text = get_option('theme_openhouse_prop_text');

        if(!empty($openhouse_prop_title)){
            ?>
            <div class="narrative">
               <h3><?php echo $openhouse_prop_title; ?></h3>
                <?php
                if(!empty($openhouse_prop_text)){
                    ?><p><?php echo $openhouse_prop_text; ?></p><?php
                }
                ?>
            </div>
            <?php
        }

        ?>
        <div class="carousel es-carousel-wrapper">
            <div class="es-carousel">
                <ul class="clearfix">
                    <?php
                    while ( $openhouse_properties_query->have_posts() ) :
                        $openhouse_properties_query->the_post();
                        // echo '<pre>';
                        // var_dump($openhouse_properties_query->the_post());
                        // echo '</pre>';
                        ?>
                        <li>
                            <figure>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                    <?php
                                    if( has_post_thumbnail() ){
                                        the_post_thumbnail( 'property-thumb-image' );
                                    } else {
                                        inspiry_image_placeholder( 'property-thumb-image' );
                                    }
                                    ?>
                                </a>
                            </figure>
                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <p><?php framework_excerpt(8); ?> <a href="<?php the_permalink() ?>"> <?php _e('Know More','framework'); ?> </a> </p>
                            <?php
                            $price = get_property_price();
                            if ( $price ){
                                echo '<span class="price">'.$price.'</span>';
                            }
                            ?>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_query();
                    ?>
                </ul>
            </div>
        </div>
    </section>
    <?php
endif;
?>

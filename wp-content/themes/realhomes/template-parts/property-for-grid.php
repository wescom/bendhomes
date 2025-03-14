<article class="property-item clearfix">

    <figure>
        <a href="<?php the_permalink() ?>">
            <?php 
            global $post;
            if( has_post_thumbnail( $post->ID ) ) {
                the_post_thumbnail( 'property-thumb-image' );
            } else {
                inspiry_image_placeholder( 'property-thumb-image' );
            }
            ?>
        </a>

        <?php display_figcaption( $post->ID ); ?>

    </figure>


    <h4><a href="<?php the_permalink() ?>"><?php bh_the_title(); ?></a></h4>
    <p><?php framework_excerpt( 9 ); ?> <a class="more-details" href="<?php the_permalink() ?>"><?php _e('More Details ','framework'); ?><i class="fa fa-caret-right"></i></a></p>
    <?php
        $price = get_property_price();
        if ( $price ){
            echo '<span>'.$price.'</span>';
        }
        echo '<br style="clear: both;">';
        brokerage_label( $post->ID, 'small' );
        ?>

</article>

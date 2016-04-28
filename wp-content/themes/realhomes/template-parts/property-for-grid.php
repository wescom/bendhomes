<article class="property-item clearfix">

    <figure>
        <a href="<?php the_permalink() ?>">
            <?php
            echo 'test1777 grid view ph-A';
            global $post;
            if( has_post_thumbnail( $post->ID ) ) {
                the_post_thumbnail( 'grid-view-image' );
                echo 'test1777 grid view ph-B';
            } else {
                inspiry_image_placeholder( 'grid-view-image' );
                echo 'test1777 grid view ph-C';
            }
            ?>
        </a>

        <?php display_figcaption( $post->ID ); ?>

    </figure>


    <h4><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
    <p><?php framework_excerpt( 9 ); ?> <a class="more-details" href="<?php the_permalink() ?>"><?php _e('More Details ','framework'); ?><i class="fa fa-caret-right"></i></a></p>
    <?php
        $price = get_property_price();
        if ( $price ){
            echo '<span>'.$price.'</span>';
        }
    ?>
</article>

<?php
if ( has_post_thumbnail() ){ ?>

    <figure>
        <span class="format-icon image"></span>
        <?php
        if( is_single() ){
            $image_id = get_post_thumbnail_id();
            $image_url = wp_get_attachment_url($image_id);
            ?>
            <a href="<?php echo $image_url; ?>" class="<?php echo get_lightbox_plugin_class(); ?>" title="<?php the_title(); ?>">
            <?php
        }else{
            ?>
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
            <?php
        }

        the_post_thumbnail('property-thumb-image');

        ?>
        </a>
    </figure>
    
<?php } else { ?>
  
	<figure>
    	<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/bh-placeholder.jpg" alt="" />
    </figure>
  
<?php } ?>

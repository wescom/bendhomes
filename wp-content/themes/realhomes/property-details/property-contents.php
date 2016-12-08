<?php $display_social_share = get_option('theme_display_social_share'); ?>

<article class="property-item clearfix">
    <div class="wrap clearfix">
        <h5 class="price">
            <span class="status-label">
                <?php
                /* Property Status. For example: For Sale, For Rent */
                $status_terms = get_the_terms( $post->ID,"property-status" );
                if(!empty( $status_terms )){
                    $status_count = 0;
                    foreach( $status_terms as $term ){
                        if( $status_count > 0 ){
                            echo ', ';
                        }
                        echo $term->name;
                        $status_count++;
                    }
                }else{
                    echo '&nbsp;';
                }
                ?>
            </span>
            <span>
                <?php
                /* Property Price */
                property_price();

                /* Property Type. For example: Villa, Single Family Home */
                echo inspiry_get_property_types( $post->ID );
                ?>
            </span>
        </h5>
        <h4 class="title">
        	<a href="#mortgage-calculator" data-toggle="modal" onClick="ga('send', 'pageview', '<?php echo tbb_current_url( 'mortcalc=viewed' ); ?>');"><i class="fa fa-calculator"></i> Calculate Payment</a>
            <?php

            /* Property ID if exists */
            /*$property_id = get_post_meta($post->ID, 'REAL_HOMES_property_id', true);
            if(!empty($property_id)){
                // echo __('Property ID','framework').' : '.$property_id;
                echo 'MLS# : '.$property_id;
            }*/

            ?>
        </h4>
    </div>
    
    <?php if ( $display_social_share == 'true' ) {
		?>
		<div class="share-networks clearfix">
			<span class="share-label"><?php _e('Share this', 'framework'); ?></span>
			<span class="share-facebook"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"><i class="fa fa-facebook fa-lg"></i><?php _e('Facebook','framework'); ?></a></span>
			<span class="share-twitter"><a target="_blank" href="https://twitter.com/share?url=<?php the_permalink(); ?>" ><i class="fa fa-twitter fa-lg"></i><?php _e('Twitter','framework'); ?></a></span>
			<span class="share-googleplus"><a target="_blank" href="https://plus.google.com/share?url={<?php the_permalink(); ?>}" onclick="javascript:window.open(this.href,  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes')"><i class="fa fa-google-plus fa-lg"></i><?php _e('Google','framework'); ?></a></span>
            <?php // Add to favorites
        	//get_template_part('property-details/property-add-to-favorites'); ?>
            <!-- Print link -->
        	<span class="printer-icon"><a href="javascript:window.print()"><i class="fa fa-print"></i><?php _e('Print','framework'); ?></a></span>
		</div>
		<?php
	} ?>

    <div class="property-meta clearfix">
        <?php
        // property meta
        get_template_part('property-details/property-metas');

        // Add to favorites
        //get_template_part('property-details/property-add-to-favorites');

        ?>
        <!-- Print link -->
        <!--span class="printer-icon"><a href="javascript:window.print()"><i class="fa fa-print"></i><?php //_e('Print','framework'); ?></a></span-->
    </div>

    <div class="content clearfix">
        <?php
		
        // contents from WordPress editor
        the_content();

        // Property additional details from meta boxes
        get_template_part( 'property-details/property-additional-details' );

        // common note from theme options
        get_template_part( 'property-details/property-common-note' );

        ?>
    </div>


    <?php
    /* Property Features */
    $features_terms = get_the_terms( $post->ID,"property-feature" );
    if(!empty($features_terms)){
        ?>
        <div class="features">
            <?php
            $property_features_title = get_option('theme_property_features_title');
            if(!empty($property_features_title)){
                ?><h4 class="title"><?php echo $property_features_title; ?></h4><?php
            }
            ?>
            <ul class="arrow-bullet-list clearfix">
            <?php
            foreach($features_terms as $fet_trms){
                echo '<li><a href="'.get_term_link($fet_trms->slug, 'property-feature').'">'.$fet_trms->name.'</a></li>';
            }
            ?>
            </ul>
        </div>
        <?php
    }
    ?>
</article>

<article class="about-agent clearfix <?php echo $term_val['class']; ?>">
    <?php
      $title_label = '<span class="label">'.$term_val['title_label'].'</span>';
    ?> 
    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo $title_label; ?></h4>

        <div class="row-fluid">

            <div class="span3">
                <?php
                if(has_post_thumbnail()){
                    ?>
                    <figure class="agent-pic">
                        <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('agent-image'); ?>
                        </a>
                    </figure>
                <?php
                } else if( function_exists( 'get_avatar' ) ) {
                    ?>
                    <!-- user avatar -->
                    <figure class="agent-pic">
                        <a title="<?php $user->display_name; ?>" href="<?php echo $author_page_url; ?>">
                            <?php echo get_avatar( $user->user_email, '180' ); ?>
                        </a>
                    </figure>
                    <?php
                }
                ?>
            </div>

            <div class="span9">

                <div class="agent-content">
                    <p><?php framework_excerpt(45); ?></p>
                </div>

                <?php

                /* Agent Brokerage Info */
                brokerageBlock($post->ID);
                // get_template_part( 'bend-homes/template-parts/brokerage-block' );

                /* Agent Contact Info */
                $agent_mobile = get_post_meta($post->ID, 'REAL_HOMES_mobile_number',true);
                $agent_office_phone = get_post_meta($post->ID, 'REAL_HOMES_office_number',true);
                $agent_office_fax = get_post_meta($post->ID, 'REAL_HOMES_fax_number',true);

                if( !empty( $agent_office_phone ) || !empty( $agent_mobile ) || !empty( $agent_office_fax ) ) {
                    ?>
                    <ul class="contacts-list">
                        <?php
                        if(!empty($agent_office_phone)){
                            ?><li class="office"><?php include( get_template_directory() . '/images/icon-phone.svg' ); _e('Office', 'framework'); ?> : <?php echo $agent_office_phone; ?></li><?php
                        }
                        if(!empty($agent_mobile)){
                            ?><li class="mobile"><?php include( get_template_directory() . '/images/icon-mobile.svg' ); _e('Mobile', 'framework'); ?> : <?php echo $agent_mobile; ?></li><?php
                        }
                        if(!empty($agent_office_fax)){
                            ?><li class="fax"><?php include( get_template_directory() . '/images/icon-printer.svg' ); _e('Fax', 'framework'); ?>  : <?php echo $agent_office_fax; ?></li><?php
                        }
                        ?>
                    </ul>
                    <?php
                }
                ?>

            </div>

        </div><!-- end of .row-fluid -->

    <div class="follow-agent clearfix">
        <a class="real-btn btn" href="<?php the_permalink(); ?>"><?php _e('More Details','framework'); ?></a>
        <?php

        $facebook_url = get_post_meta($post->ID, 'REAL_HOMES_facebook_url',true);
        $twitter_url = get_post_meta($post->ID, 'REAL_HOMES_twitter_url',true);
        $google_plus_url = get_post_meta($post->ID, 'REAL_HOMES_google_plus_url',true);
        $linked_in_url = get_post_meta($post->ID, 'REAL_HOMES_linked_in_url',true);

        if(!empty($facebook_url) || !empty($twitter_url) || !empty($google_plus_url) || !empty($linked_in_url)){
            ?>
            <!-- Agent's Social Navigation -->
            <ul class="social_networks clearfix">
                <?php
                if(!empty($facebook_url)){
                    ?>
                    <li class="facebook">
                        <a target="_blank" href="<?php echo $facebook_url; ?>"><i class="fa fa-facebook fa-lg"></i></a>
                    </li>
                    <?php
                }
                if(!empty($twitter_url)){
                    ?>
                    <li class="twitter">
                        <a target="_blank" href="<?php echo $twitter_url; ?>" ><i class="fa fa-twitter fa-lg"></i></a>
                    </li>
                    <?php
                }
                if(!empty($linked_in_url)){
                    ?>
                    <li class="linkedin">
                        <a target="_blank" href="<?php echo $linked_in_url; ?>"><i class="fa fa-linkedin fa-lg"></i></a>
                    </li>
                    <?php
                }

                if(!empty($google_plus_url)){
                    ?>
                    <li class="gplus">
                        <a target="_blank" href="<?php echo $google_plus_url; ?>"><i class="fa fa-google-plus fa-lg"></i></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
        ?>
    </div>

</article>

<?php
get_header();
?>

    <!-- Page Head -->
    <?php get_template_part("banners/default_page_banner"); ?>

    <!-- Content -->
    <div class="container contents listing-grid-layout">

        <div class="row">

            <div class="span9 main-wrap">
            
            	<?php

				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						
						$featured_agent = get_field( 'agent_featured' );
						$office_name = get_field( 'brk_office_name' );
						$office_address = get_field( 'brk_office_address' );
						$agent_office_phone = get_post_meta($post->ID, 'REAL_HOMES_office_number',true);
						$agent_mobile = get_post_meta($post->ID, 'REAL_HOMES_mobile_number',true);
						$agent_office_fax = get_post_meta($post->ID, 'REAL_HOMES_fax_number',true);
						?>

                <!-- Main Content -->
                <div class="main" style="margin-top: 0;">
                
                		<?php /*print_r($post). '<br/>';
						$meta = get_post_meta($post->ID); 
						foreach($meta as $key=>$val){
							echo $key . ' : ' . $val[0] . '   ';
						}*/ ?>

                    <section class="listing-layout">
                        <div class="list-container">
                            
                            <article class="about-agent agent-single clearfix featured-<?php echo $featured_agent; ?>">

                                <div class="detail">

                                    <div class="row-fluid" style="padding-bottom: 16px;">

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
                                            }
                                            ?>
                                        </div>

                                        <div class="span9">
                                        
                                        	<div class="brokerage-address">
                                            	<strong><?php echo $office_name; ?></strong><br>
                                            	<?php echo $office_address; ?>
                                            </div>

                                            <?php

                                            /* Agent Brokerage Info */
                                            //brokerageBlock($post->ID);
                                            // get_template_part( 'bend-homes/template-parts/brokerage-block' );                                            

                                            if( !empty( $agent_office_phone ) || !empty( $agent_mobile ) || !empty( $agent_office_fax ) ) {
                                                ?>
                                                <hr/>
                                                <h5><?php _e('Contact Details', 'framework'); ?></h5>
                                                <ul class="contacts-list">
                                                    <?php
                                                    if(!empty($agent_office_phone)){
                                                        ?><li class="office">
                                                        <?php 
														if($featured_agent) {
															echo '<i class="fa fa-phone"></i> Office: <a href="tel:'. str_replace("-", '', $agent_office_phone) .'">'. $agent_office_phone .'</a>';
                                                        } else {
                                                        	echo '<i class="fa fa-phone"></i> Office: '. $agent_office_phone;
                                                        }
														?>
                                                        </li><?php
                                                    }
                                                    if(!empty($agent_mobile) && $featured_agent){
                                                        ?><li class="mobile">
                                                        <?php echo '<i class="fa fa-mobile"></i> Mobile: <a href="tel:'. str_replace("-", '', $agent_mobile) .'">'. $agent_mobile .'</a>'; ?>
                                                        </li><?php
                                                    }
                                                    if(!empty($agent_office_fax) && $featured_agent){
                                                        ?><li class="fax"><i class="fa fa-printer"></i> Fax: <?php echo $agent_office_fax; ?></li><?php
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                            } ?>
											
											<div class="agent-content">
                                                <?php //the_content(); ?>
                                            </div>

                                            <?php // Agent contact form
                                            get_template_part( 'template-parts/agent-contact-form' );
                                            ?>

                                        </div>

                                    </div><!-- end of .row-fluid -->

                                </div>

                                <div class="follow-agent clearfix">
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

							<?php
                            /**
                             * Agent properties
                             */
                            /*$number_of_properties = intval(get_option('theme_number_of_properties_agent'));
                            if(!$number_of_properties){
                                $number_of_properties = 6;
                            }*/
							
							if( $featured_agent ) {

                            	$agent_id = $post->ID;
							
								global $paged;
	
								$agent_properties_args = array(
									'post_type' => 'property',
									'posts_per_page' => 10,
									'meta_query' => array(
										array(
											'key' => 'REAL_HOMES_agents',
											'value' => $agent_id,
											'compare' => '='
										)
									),
									'paged' => $paged
								);
	
								$agent_properties_listing_query = new WP_Query( $agent_properties_args );
	
								if ( $agent_properties_listing_query->have_posts() ) :
									while ( $agent_properties_listing_query->have_posts() ) :
										$agent_properties_listing_query->the_post();
	
										/* Display Property for Listing */
										get_template_part('template-parts/property-for-listing');
	
									endwhile;
									wp_reset_postdata();
								endif;
								
								theme_pagination( $agent_properties_listing_query->max_num_pages);
							
							}
                            ?>
                        </div>
                    </section>

                </div><!-- End Main Content -->
                
                <?php
					endwhile;
				endif; ?>

            </div> <!-- End span9 -->

            <?php get_sidebar('agent'); ?>

        </div><!-- End contents row -->

    </div><!-- End Content -->

<?php get_footer(); ?>

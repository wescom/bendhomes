<?php
get_header();
?>

<!-- Page Head -->
<?php get_template_part("banners/default_page_banner"); ?>

<!-- Content -->
<div class="container contents listing-grid-layout">

    <div class="row">

        <div class="span9 main-wrap">

            <!-- Main Content -->
            <div class="main" style="margin-top: 0;">

                <section class="listing-layout">
                    <div class="list-container">
                        <?php
                        while ( have_posts() ) :
                            the_post();
                            ?>
                            <article class="about-company company-single clearfix">

                                <div class="detail">

                                    <div class="row-fluid">

										<?php if(has_post_thumbnail()){ ?>
                                        <div class="span3">
                                            <figure class="agent-pic">
                                                <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('medium'); ?>
                                                </a>
                                            </figure>
                                        </div>

                                        <div class="span9">
                                        <?php } else { ?>
                                        
                                        <div class="span12">
										<?php } ?>

                                            <div class="agent-content">
                                                <?php the_content(); ?>
                                            </div>
                                            <?php

                                            // Company Contact Info
                                            $company_office_phone = get_post_meta($post->ID, 'company_office_phone',true);
                                            $company_office_fax = get_post_meta($post->ID, 'company_office_fax',true);
                                            $company_office_address = get_post_meta($post->ID, 'company_office_address', true);

                                            if( !empty( $company_office_phone ) || !empty( $company_office_fax ) ) {
                                                ?>
                                                <h5><?php the_title(); ?></h5>
                                                
                                                <?php
                                                if(!empty($company_office_address)){
                                                    echo do_shortcode('<p>[MAP_LINK address="'. $company_office_address .'"]'. $company_office_address .'[/MAP_LINK]</p>');
                                                }
                                                ?>
                                                
                                                <ul class="contacts-list">
                                                    <?php
                                                    if(!empty($company_office_phone)){
                                                        ?><li class="office">
														<?php include( get_template_directory() . '/images/icon-phone.svg' ); _e('Office', 'framework'); ?> : 
														<?php echo '<a href="tel:'. str_replace("-", '', $company_office_phone) .'">'. $company_office_phone .'</a>'; ?>
                                                        </li><?php
                                                    }
                                                    if(!empty($company_office_fax)){
                                                        ?><li class="fax"><?php include( get_template_directory() . '/images/icon-printer.svg' ); _e('Fax', 'framework'); ?>  : <?php echo $company_office_fax; ?></li><?php
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                            }

                                            // Agent contact form
                                            //get_template_part( 'template-parts/agent-contact-form' );
                                            ?>

                                        </div>

                                    </div><!-- end .row-fluid -->
                                    
                                    <?php
									$agents_array = array_diff( get_field( 'company_agents' ), array('') );
									
									$agents_test = get_field( 'company_agents' );
									
									print_r($agents_test);
									
									$agent_args = array(
										'post_type' => 'agent',
										'post__in' => $agents_array,
										'posts_per_page' => -1,
										'order' => 'ASC',
										'orderby' => 'title'
									);
									
									$agents = new WP_Query( $agent_args );
									
									$unique_agents = array();
									
									if( $agents->have_posts() ) : ?>
																			
										<h3>Agents</h3>
                                        
                                        <div class="agents-list-wrap clearfix">
									
											<?php
                                            while( $agents->have_posts() ) : $agents->the_post(); 
												
											$agent_name = get_the_title();
											$agent_category = sanitize_title( strip_tags( get_the_term_list( $id, 'agent_types', '', ' ', '' ) ) );
											
											// Make sure there's no duplicate agents in the list
											if( !in_array($agent_name, $unique_agents) ) {
												
												array_push($unique_agents, $agent_name); 
												
												if( $agent_category == 'featured-agent' || $agent_category == 'standard-agent' ) { ?>
												
												<div class="company-agent">
													<a class="company-agent-inner" href="<?php echo get_permalink(); ?>">
														<figure class="agent-image">
															<?php  if(has_post_thumbnail()){
																the_post_thumbnail('thumbnail');
															} else {
																echo '<div class="no-agent-image"></div>';	
															}?>
														</figure>                                                        
														<div class="agent-name"><?php echo $agent_name; ?></div>
													</a>
												</div>
												
												<?php }
											}
                                                                                  
                                            endwhile; ?>
                                        
                                    	</div>
                                        
                                    <?php endif; // end agents query ?>

                                </div><!-- end .detail -->

                            </article>
                        <?php
                        endwhile;
                        ?>
                    </div>
                </section>

            </div><!-- End Main Content -->

        </div> <!-- End span9 -->

        <?php get_sidebar('agent'); ?>

    </div><!-- End contents row -->

</div><!-- End Content -->

<?php get_footer(); ?>

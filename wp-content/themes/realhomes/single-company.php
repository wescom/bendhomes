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
			while ( have_posts() ) :
				the_post();
				
				$company_featured = get_field( 'company_featured_company' );	
				?>
                
            <!-- Main Content -->
            <div class="main" style="margin-top: 0;">

                <section class="listing-layout">
                    <div class="list-container">
                        
                            <article class="about-company company-single clearfix">

                                <div class="detail">

                                    <div class="row-fluid">

										<?php if(has_post_thumbnail()){ ?>
                                        <div class="span3">
                                            <figure class="agent-pic">
                                                <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('agent-image'); ?>
                                                </a>
                                            </figure>
                                        </div>

                                        <div class="span9">
                                        <?php } else { ?>
                                        
                                        <div class="span12">
										<?php }

                                            // Company Contact Info
                                            $company_office_phone = get_field( 'company_office_phone' );
                                            $company_office_fax = get_field( 'company_office_fax' );
                                            $company_office_address = get_field( 'company_office_address' );

                                            if( !empty( $company_office_phone ) || !empty( $company_office_fax ) ) {
                                                ?>
                                                <h5 class="company-featured-<?php echo $company_featured; ?>"><?php the_title(); ?></h5>
                                                
                                                <?php
                                                if(!empty($company_office_address) && $company_featured == 1){
                                                    echo do_shortcode('<p>[MAP_LINK address="'. $company_office_address .'"]'. $company_office_address .'[/MAP_LINK]</p>');
                                                } else {
													echo '<p>'. $company_office_address .'</p>';	
												}
                                                ?>
                                                
                                                <ul class="contacts-list">
                                                    <?php
                                                    if(!empty($company_office_phone)){
                                                        ?><li class="office">
														<?php include( get_template_directory() . '/images/icon-phone.svg' ); _e('Office', 'framework'); ?> : 
														<?php if( $company_featured == 1 ) {
															echo '<a href="tel:'. str_replace("-", '', $company_office_phone) .'">'. $company_office_phone .'</a>';
														} else {
															echo $company_office_phone;
														} ?>
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
                                            
                                            <?php if( $company_featured == 1 ) { ?>
                                            <div class="agent-content">
												<?php the_content(); ?>
                                            </div>
                                            <?php } ?>

                                        </div>

                                    </div><!-- end .row-fluid -->
                                    
                                    <?php			
									if( $company_featured == 1 ) {	
																						
										$agents_array = array_diff( get_field( 'company_agents' ), array('') );
																												
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
																		//the_post_thumbnail('thumbnail');
																		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'thumbnail', true);
																		echo sprintf('<img src="%s" alt="%s" width="%s" height="%s" />', $image[0], $agent_name, $image[1], $image[2] );
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
											

										<?php endif; // end agents query
										
									} ?>

                                </div><!-- end .detail -->

                            </article>
                    </div>
                </section>

            </div><!-- End Main Content -->
            
            <?php
			endwhile;
			?>

        </div> <!-- End span9 -->

        <?php get_sidebar('agent'); ?>

    </div><!-- End contents row -->

</div><!-- End Content -->

<?php get_footer(); ?>

<?php
get_header();
?>

    <!-- Page Head -->
    <?php //get_template_part("banners/default_page_banner"); ?>

    <!-- Content -->
    <div class="container contents listing-grid-layout">

        <div class="row">

            <div class="span9 main-wrap">

                <!-- Main Content -->
                <div class="main" style="margin-top: 0;">

                    <section class="listing-layout">
                        <div class="list-container">
                            <?php

                            if ( have_posts() ) :
                                while ( have_posts() ) :
                                    the_post();
                                    ?>
                                    <article class="about-company company-single clearfix">

                                        <div class="detail">

                                            <div class="row-fluid">

                                                <div class="span3">
                                                    <?php
                                                    if(has_post_thumbnail()){
                                                        ?>
                                                        <figure class="agent-pic">
                                                            <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                                                                <?php the_post_thumbnail('medium'); ?>
                                                            </a>
                                                        </figure>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>

                                                <div class="span9">

                                                    <div class="agent-content">
                                                        <?php the_content(); ?>
                                                    </div>
                                                    <?php

                                                    /* Company Contact Info */
                                                    $company_office_phone = get_post_meta($post->ID, 'company_office_phone',true);
                                                    $company_office_fax = get_post_meta($post->ID, 'company_office_fax',true);
													$company_office_address = get_post_meta($post->ID, 'company_office_address', true);

                                                    if( !empty( $company_office_phone ) || !empty( $company_office_fax ) ) {
                                                        ?>
                                                        <hr/>
                                                        <h5><?php _e('Contact Details', 'framework'); ?></h5>
                                                        
                                                        <?php
														if(!empty($company_office_address)){
															echo '<p>'. $company_office_address .'</p>';
														}
														?>
                                                        
                                                        <ul class="contacts-list">
                                                            <?php
                                                            if(!empty($company_office_phone)){
                                                                ?><li class="office"><?php include( get_template_directory() . '/images/icon-phone.svg' ); _e('Office', 'framework'); ?> : <?php echo $company_office_phone; ?></li><?php
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
                                            
                                            <div class="row-fluid">
                                            	
                                                <?php
												$agents = get_field( 'company_agents' );
												
												if( $agents ) { ?>
                                                
                                                	<ul>
													
													<?php foreach( $agents as $agent ) { ?>
														
                                                        <li>
                                                        	<a href="<?php echo get_permalink( $agent->ID ); ?>">
																<?php echo get_the_title( $agent->ID ); ?>
                                                            </a>
                                                        </li>
                                                        
													<?php } ?>
                                                    
                                                    </ul>
                                                    
												<?php
												}
												?>
                                                
                                            </div><!-- end .row-fluid -->

                                        </div>

                                    </article>
                                <?php
                                endwhile;
                            endif;
                            ?>
                        </div>
                    </section>

                </div><!-- End Main Content -->

            </div> <!-- End span9 -->

            <?php get_sidebar('agent'); ?>

        </div><!-- End contents row -->

    </div><!-- End Content -->

<?php get_footer(); ?>

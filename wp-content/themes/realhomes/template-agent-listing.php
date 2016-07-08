<?php
/* 
*   Template Name: Agent Listing Template
*/
get_header();
?>

        <!-- Page Head -->
        <?php get_template_part("banners/default_page_banner"); 

        $terms = array(
          'featured-agent' => array(
            'class' => 'featured-agent-type',
            'title' => 'Featured Agents',
            'title_label' => 'Featured Agent'
          ),
          'standard-agent' => array(
            'class' => 'standard-agent-type',
            'title' => 'Standard Agents',
            'title_label' => 'Standard Agent'
          )
        );

        ?>

        <!-- Content -->
        <div class="container contents listing-grid-layout">

            <div class="row">

                <div class="span9 main-wrap">

                    <!-- Main Content -->
                    <div class="main">
                        <section class="listing-layout">
                            <?php
                            $title_display = get_post_meta( $post->ID, 'REAL_HOMES_page_title_display', true );
                            if( $title_display != 'hide' ){
                                ?>
                                <h3 class="title-heading"><?php the_title(); ?></h3>
                                <?php
                            }
                            ?>

                            <div class="list-container">
                                <?php
                                $number_of_posts = intval(get_option('theme_number_posts_agent'));
                                if(!$number_of_posts){
                                    $number_of_posts = 20;
                                }

                                foreach($terms as $term_key => $term_val) {
                                  // alter query to get featured agenst first, then
                                  // the  rest

                                  // echo '<h2>class: '.$term_val['class'].'</h2>';

                                  $agents_query = array(
									  'post_type' => 'agent',
									  'posts_per_page' => $number_of_posts,
									  'paged' => $paged,
									  'tax_query' => array(
										  array(
											  'taxonomy' => 'agent_types',
											  'terms' => $term_key,
											  'field' => 'slug',
											  'include_children' => false,
											  'operator' => 'IN'
										  )
									  ),
								  );

								
								
								// Jarel is working on this still
								/*if($number_of_posts > 0) {
									$agents = new WP_Query( $agents_query );
								  
									if ( $agents->have_posts() ) :
										while ( $agents->have_posts() ) :
										
											$agent_display_type = bhLookupTaxonomy( $agents->ID, 'agent_types' );
											$agent_office = get_field( 'brk_office_name' );
										
											wp_reset_query();
											
											$company_post = new WP_Query( array(
												'post_type' => 'company',
												'name' => sanitize_title( $agent_office )
											) );
											
											if( $company_post->have_posts() ) :
												while( $company_post->have_posts() ) : $company_post->the_post();
													
													$company_is_featured = get_field( 'company_featured_company' );
													
												endwhile;
											endif;
											
											wp_reset_query();
											
											if( $agent_display_type == 'featured-agent' || $company_is_featured == true ) {
												
												include(locate_template('bend-homes/template-parts/agent-listing-loop.php' ));
													
											}
										
										endwhile;
									endif;
									
								}*/
								  
								  
								  
								  
								  
								  
								  
								  if($number_of_posts > 0) {
                                    $agent_listing_query = new WP_Query( $agents_query );

                                    // echo "<h2>Found: $agent_listing_query->found_posts</h2>";

                                    // start if loop of agents
                                    if ( $agent_listing_query->have_posts() ) :
                                        while ( $agent_listing_query->have_posts() ) :
                                            $agent_listing_query->the_post();
                                            include(locate_template('bend-homes/template-parts/agent-listing-loop.php' ));
                                        endwhile;
                                        wp_reset_query();
                                        // end if look of agents
                                    else:
                                        ?>
                                        <!-- <?php _e('Sorry No Results Found', 'framework') ?> -->
                                    <?php
                                    endif;
                                  }

                                  // do math! So we an have an accurate count of standard agent posts_per_page
                                  // found_posts is the entire query count in db. So it's value can go
                                  // below zero when doing this math
                                  $queried_post_count = $agent_listing_query->found_posts;
                                  $next_loop_count = ($number_of_posts - $queried_post_count);
                                  if($next_loop_count < 0) {
                                    $number_of_posts = 0;
                                  } else {
                                    $number_of_posts = $next_loop_count;
                                  }

                                }
                                ?>
                            </div>

                            <?php theme_pagination( $agent_listing_query->max_num_pages); ?>

                        </section>

                    </div><!-- End Main Content -->

                </div> <!-- End span9 -->

                <?php get_sidebar('pages'); ?>

            </div><!-- End contents row -->

        </div><!-- End Content -->

<?php get_footer(); ?>

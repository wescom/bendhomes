<?php 
get_header();

$theme_property_detail_variation = get_option('theme_property_detail_variation');

// Banner Image
$banner_image_path = "";
$banner_image_id = get_post_meta( $post->ID, 'REAL_HOMES_page_banner_image', true );
if( $banner_image_id ){
    $banner_image_path = wp_get_attachment_url($banner_image_id);
}else{
    $banner_image_path = get_default_banner();
}

get_template_part('bend-homes/property-details/property-agent-functions');
get_template_part('bend-homes/property-details/property-agent-for-sidebar');


 // 1777 -- removal of graphic head area on property detail view
    /*
    <div class="page-head" style="background-repeat: no-repeat;background-position: center top;background-image: url('<?php echo $banner_image_path; ?>'); background-size: cover;">
        <?php if(!('true' == get_option('theme_banner_titles'))): ?>
            <div class="container">
                <div class="wrap clearfix">
                    <h1 class="page-title"><span><?php the_title(); ?></span></h1>
                    <?php
                    $display_property_breadcrumbs = get_option( 'theme_display_property_breadcrumbs' );
                    if ( $display_property_breadcrumbs == 'true' ) {
                        get_template_part( 'property-details/property-breadcrumbs' );
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div><!-- End Page Head -->
    */
    ?>

    <div class="page-spacer"></div>

    <!-- Content -->
    <div class="container contents detail">
        <div class="row">
            <div class="span9 main-wrap">
              <?php
              // 1777
              $display_property_breadcrumbs = get_option( 'theme_display_property_breadcrumbs' );
              if ( $display_property_breadcrumbs == 'true' ) {
                  get_template_part( 'property-details/property-breadcrumbs' );
              }
              ?>
                <!-- Main Content -->
                <div class="main">

                    <div id="overview">
                        <?php
                        // 1777
                        echo '<h1 class="property-title">';
                        bh_the_title();
                        echo '</h1>';

                        if ( have_posts() ) :
                            while ( have_posts() ) :
                                the_post();

                                if ( ! post_password_required() ) { 
								
									$mls_number = get_field( 'REAL_HOMES_property_id' );
									if(!empty($mls_number)) $mls = sprintf( 'MLS #: <span class="font-roboto text-green">%s</span>', $mls_number );
									
									$status_terms = get_the_terms( get_the_ID(), 'property-status' );
									if ( $status_terms && !is_wp_error( $status_terms ) ) :
										$term_links = array();
										foreach( $status_terms as $status ) {
											$term_links[] = $status->name;
										}
										$on_status = join( ', ', $term_links );
										$status_list = sprintf( '<span class="header-status">Status: %s</span>', esc_html( $on_status ) );
									endif;
									?>
									
                                    <div class="quick-header-info">
                                    	<span class="header-price font-roboto"><?php property_price(); ?></span>
                                        <span class="header-mls"><?php echo $mls; ?></span>
                                        <?php echo $status_list; ?>
                                    </div>
									
									<?php
                                    /*
                                    * 1. Property Images Slider
                                    */
                                    $gallery_slider_type = get_post_meta($post->ID, 'REAL_HOMES_gallery_slider_type', true);
                                    /* For demo purpose only */
                                    if(isset($_GET['slider-type'])){
                                        $gallery_slider_type = $_GET['slider-type'];
                                    }
                                    if( $gallery_slider_type == 'thumb-on-bottom' ){
                                        get_template_part('property-details/property-slider-two');
                                    }else{
                                        get_template_part('property-details/property-slider');
                                    }


                                    /*
                                    * 2. Property Information Bar, Icons Bar, Text Contents and Features
                                    */
                                    get_template_part('property-details/property-contents');

                                    /*
                                    * 2.5. Property Agent information, if not a featured agent
                                    */
                                    bhAgentRender('body');

                                    /*
                                    * 3. Property Floor Plans
                                    */
                                    get_template_part('property-details/property-floor-plans');

                                    /*
                                    * 4. Property Video
                                    */
                                    get_template_part('property-details/property-video');

                                    /*
                                    * 5. Property Map
                                    */
                                    get_template_part('property-details/property-map');

                                    /*
                                    * 6. Property Attachments
                                    */
                                    get_template_part('property-details/property-attachments');

                                    /*
                                    * 7. Child Properties
                                    */
                                    get_template_part('property-details/property-children');

                                    /*
                                    * 8. Property Agent
                                    */
                                    if ( isset( $_GET[ 'variation' ] ) ) {
                                        $theme_property_detail_variation = $_GET[ 'variation' ];    // For demo purpose only
                                    }

                                    if ( $theme_property_detail_variation != "agent-in-sidebar" ) {
                                        get_template_part( 'property-details/property-agent' );
                                    }

                                } else {
                                    echo get_the_password_form();
                                }

                            endwhile;
                        endif;
                        ?>
                    </div>

                </div><!-- End Main Content -->

                <?php
                /*
                 * 8. Similar Properties
                 */
                get_template_part('property-details/similar-properties');
                ?>

            </div> <!-- End span9 -->

            <?php
            if( $theme_property_detail_variation == "agent-in-sidebar" ) {
                ?>
                <div class="span3 sidebar-wrap">
                    <!-- Sidebar -->
                    <aside class="sidebar">
                    	
                        <?php 
						/*$property_ID = $post->ID;
						$property_agents = get_field( 'REAL_HOMES_agents' );
						
						wp_reset_query();
						
						$agent_post = new WP_Query( array(
							'post_type' => 'agent',
							'p' => $property_agents 
						) );
						
						if( $agent_post->have_posts() ) :
							while( $agent_post->have_posts() ) : $agent_post->the_post();
						
								$agent_brokerage_office = sanitize_title( get_field( 'brk_office_name' ) );
								echo '<p>Brokerage Office: '. $agent_brokerage_office .'</p>';
							
							endwhile;
						endif;
						
						wp_reset_query();
						
						$company_post = new WP_Query( array(
							'post_type' => 'company',
							'name' => $agent_brokerage_office
							//'post_status' => 'publish',
  							//'numberposts' => 1
						) );
						
						if( $company_post->have_posts() ) :
							while( $company_post->have_posts() ) : $company_post->the_post();
								
								$company_is_featured = get_field( 'company_featured_company' );
								$is_featured = $company_is_featured == true ? 'Yes' : 'No';
								echo '<p>Is Featured: '. $is_featured .'</p>';
								
							endwhile;
						endif;*/
                    
                    
                        
                        bhAgentRender('sidebar');
                        if ( ! dynamic_sidebar( 'property-sidebar' ) ) :
                        endif;
                        get_template_part( 'template-parts/rail-ad' );
                        ?>
                    </aside>
                    <!-- End Sidebar -->
                </div>
            <?php
            }else{
                get_sidebar('property');
            }

            ?>

        </div><!-- End contents row -->
    </div><!-- End Content -->

<?php get_footer(); ?>

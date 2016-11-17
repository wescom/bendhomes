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
           <!--******* Begin Custom Design *******-->
			
			<?php while( have_posts() ): the_post();
			
				$id = get_the_ID();	
					
				$mls_number = get_field( 'REAL_HOMES_property_id' );
				if(!empty($mls_number)) $mls = sprintf( 'MLS #: %s', $mls_number );
					
				$property_type = get_field( 'REAL_HOMES_property_features_subtype' );
				if(!empty($property_type)) $property_type = sprintf('Type: %s', $property_type);
					
				$status_terms = get_the_terms( $id, 'property-status' );
				if ( $status_terms && !is_wp_error( $status_terms ) ) :
					$term_links = array();
					foreach( $status_terms as $status ) {
						$term_links[] = $status->name;
					}
					$on_status = join( ', ', $term_links );
					$statusClass = "x".str_replace(", ", ",", esc_html($on_status));
					$statusClass = str_replace(" ", "-", $statusClass );
					$statusClass = str_replace(",", " ", $statusClass );
					$status_list = sprintf( '<span class="header-status %s">Status: <strong>%s</strong></span>', $statusClass, esc_html($on_status));
				endif;
					
				$current_date = date('Y-m-d');
				$listing_date = get_field( 'REAL_HOMES_property_listing_date' );
				$listing_date = '';//DateTime::createFromFormat('Y-m-d', $listing_date)->format('Y-m-d');
				$days_on_site = $current_date - $listing_date;
				if( $days_on_site < 1 ) {
					$onsite = 'New Today';
				} elseif( $days_on_site == 1 ) {
					$onsite = '1 Day on Market';
				} else {
					$onsite = $days_on_site .' Days on Market';
				}
					
				$beds = get_field('REAL_HOMES_property_bedrooms');
				$baths = get_field('REAL_HOMES_property_bathrooms');
				$sqft = intval(get_field('REAL_HOMES_property_size'));
				$acres = get_field('REAL_HOMES_exterior_acres');
				$built = get_field('REAL_HOMES_property_features_year_built');
				$price = intval(get_field('REAL_HOMES_property_price'));
				$p_sqft = intval($price / $sqft);
				$video = get_field('REAL_HOMES_tour_video_url');
				
			?>
			<div class="row-fluid">
				<div class="span7">
					<h1 class="property-title"><?php echo bh_the_title(); ?></h1>
					<div class="quick-header-info clearfix">
						<span class="header-price text-green">$<?php echo $price; ?></span>
						<span class="header-type"><?php echo $property_type; ?></span>
						<?php echo $status_list; ?>
						<span class="header-mls"><?php echo $mls; ?></span>
						<div class="newness">
							<?php echo $onsite; ?>
						</div>
						<div class="updated">

						</div>
					</div>
				</div>
				
				<div class="span5">
					<?php
					// Show featured agent box
					bhAgentRender('sidebar');
					bhAgentRender('body');
					?>
				</div>
			</div>
			
			<div class="main-wrap">
				<div class="row-fluid">
					<div class="span7">
						<ul class="nav nav-tabs" id="myTab">
							<li class="active"><a href="#tab-photos" data-toggle="tab">Photos</a></li>
							<li><a href="#tab-map" data-toggle="tab">Map</a></li>
							<?php if(!empty($video)) { ?>
							<li><a href="#tab-video" data-toggle="tab">Video</a></li>
							<?php } ?>
						</ul>

						<div class="tab-content">
							<div class="tab-pane active" id="tab-photos">
								<?php get_template_part('property-details/property-slider-two'); ?>
							</div>
							<div class="tab-pane" id="tab-map">
								<?php get_template_part('property-details/property-map'); ?>
							</div>
							<?php if(!empty($video)) { ?>
							<div class="tab-pane" id="tab-video">
								<?php get_template_part('property-details/property-video'); ?>
							</div>
							<?php } ?>
						</div>
					</div>
					
					<div class="span5">
						<h2 class="text-center property-price"><?php property_price(); ?></h2>
						<div class="main-items">
							<div class="item"><span class="val"><?php echo $beds; ?></span><span class="key">Beds</span></div>
							<div class="item"><span class="val"><?php echo $baths; ?></span><span class="key">Baths</span></div>
							<div class="item"><span class="val"><?php echo $sqft; ?></span><span class="key">SqFt</span></div>
							<div class="item"><span class="val"><?php echo $acres; ?></span><span class="key">Acres</span></div>
							<div class="item"><span class="val"><?php echo $built; ?></span><span class="key">Built</span></div>
							<div class="item"><span class="val"><?php echo $p_sqft; ?></span><span class="key">$/SqFt</span></div>
						</div>
						<?php 
						// open house info, if array_change_key_case
						get_template_part('bend-homes/open-house-fragment');
					
						// Mortgage calculator
						echo do_shortcode('[MORT_CALC_FORM id="'. $id .'"]');
						
						// Show share bar icons
						echo do_shortcode('[SHARE_BAR]');
						?>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span7">
						<div class="description">
							<h3>Description</h3>
							<?php the_content(); ?>
						</div>
					</div>
					<div class="span5">
						<div class="schools">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th colspan="2"><h3>Schools</h3></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Elementary School</td>
										<td><?php echo get_the_term_list( $id, 'elementary_school' ); ?></td>
									</tr>
									<tr>
										<td>Middle School </td>
										<td><?php echo get_the_term_list( $id, 'middle_school' ); ?></td>
									</tr>
									<tr>
										<td>High School</td>
										<td><?php echo get_the_term_list( $id, 'high_school' ); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span4">
						<h3>Property Features</h3>
					</div>
					
					<div class="span4">
						<h3>Interior Features</h3>
					</div>
					
					<div class="span4">
						<h3>Exterior Features</h3>
					</div>
				</div>
			</div><!-- end main-wrap -->
			
			<?php get_template_part('property-details/similar-properties'); ?>
				
				
			<?php endwhile; ?>
            <?php wp_reset_query(); ?>
           
           
           
           <!--******* End Custom Design *******-->
           
           <?php /*
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
									if(!empty($mls_number)) $mls = sprintf( 'MLS #: <span class="font-roboto">%s</span>', $mls_number );
									
									$status_terms = get_the_terms( get_the_ID(), 'property-status' );
									if ( $status_terms && !is_wp_error( $status_terms ) ) :
										$term_links = array();
										foreach( $status_terms as $status ) {
											$term_links[] = $status->name;
										}
										$on_status = join( ', ', $term_links );
                                        $statusClass = "x".str_replace(", ", ",", esc_html($on_status));
                                        $statusClass = str_replace(" ", "-", $statusClass );
                                        $statusClass = str_replace(",", " ", $statusClass );
										$status_list = sprintf( '<span class="header-status %s">Status: %s</span>', $statusClass, esc_html($on_status));
									endif;
									?>
									
                                    <div class="quick-header-info clearfix">
                                    	<span class="header-price font-roboto text-green"><?php property_price(); ?></span>
                                        <span class="header-mls"><?php echo $mls; ?></span>
                                        <?php echo $status_list; ?>
                                        
                                        <?php // open house info, if array_change_key_case
								        get_template_part('bend-homes/open-house-fragment');
										?>
                                    </div>
									
									<?php
                                    /*
                                    * 1. Property Images Slider
                                    */
                                    /*$gallery_slider_type = get_post_meta($post->ID, 'REAL_HOMES_gallery_slider_type', true);
                                    // For demo purpose only
                                    if(isset($_GET['slider-type'])){
                                        $gallery_slider_type = $_GET['slider-type'];
                                    }
                                    if( $gallery_slider_type == 'thumb-on-bottom' ){
                                        get_template_part('property-details/property-slider-two');
                                    }else{
                                        get_template_part('property-details/property-slider');
                                    }*/
									
									/*
									// Use this slider style instead of regular /property-slider because it looks better.
									get_template_part('property-details/property-slider-two');


                                    
                                    // 2. Property Information Bar, Icons Bar, Text Contents and Features

                                    get_template_part('property-details/property-contents');

                                    
                                    // 2.5. Property Agent information, if not a featured agent
                                    
                                    bhAgentRender('body');

                                    
                                    // 3. Property Floor Plans
                                    
                                    get_template_part('property-details/property-floor-plans');

                                    
                                    // 4. Property Video
                                    
                                    get_template_part('property-details/property-video');

                                    
                                    // 5. Property Map
                                    
                                    get_template_part('property-details/property-map');

                                    /
                                    // 6. Property Attachments
                                    
                                    get_template_part('property-details/property-attachments');

                                    
                                    // 7. Child Properties
                                    
                                    get_template_part('property-details/property-children');

                                    
                                    // 8. Property Agent
                                    
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
                        
                        <?php echo do_shortcode('[MORT_CALC_FORM id="'. $post->ID .'"]'); ?>
						<p>&nbsp;</p>
                
                    </div>

                </div><!-- End Main Content -->

                <?php
                
                // 8. Similar Properties
                 
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
                    
                    
                        /*
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

            */ ?>

        </div><!-- End contents row -->
    </div><!-- End Content -->

<?php get_footer(); ?>

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

			// MLS Number
			$mls_number = get_field( 'REAL_HOMES_property_id' );
			if(!empty($mls_number)) $mls = sprintf( 'MLS #: <strong>%s</strong>', $mls_number );

			// Property SubType
			$property_type = get_field( 'REAL_HOMES_property_features_subtype' );
			if(!empty($property_type)) $property_type = sprintf('Type: <strong>%s</strong>', $property_type);

			// Property Status: i.e. For Sale, Pending, Contingent Bumpable, Sold, etc.
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

			// Number of days on market. $onsite.
			$today = time();
			$listing_date = get_field( 'REAL_HOMES_property_listing_date' );
			$date1 = new DateTime();
			$date2 = new DateTime( $listing_date );
			$date_diff = ($date2->diff($date1)->format("%a")) - 1; 
			$onsite = $date_diff .' Days on Market';

			if( $onsite == '0 Days on Market' ) {
				$onsite = 'New Today';
			} 
			if( $onsite == '1 Days on Market' ) {
				$onsite = '1 Day on Market';
			}
						
			// Basic Fields
			$price = intval(get_field('REAL_HOMES_property_price'));
			$video = get_field('REAL_HOMES_tour_video_url');
			$hoa = get_field('REAL_HOMES_property_features_hoa');
			$hoa_amount = intval(get_field('REAL_HOMES_property_features_hoa_amount'));
			$hoa_per = get_field('REAL_HOMES_property_features_hoa_per');
			$zoning = get_field('REAL_HOMES_property_features_zoning');
			$county = get_the_term_list( $id, 'county' );
			$area = get_the_term_list( $id, 'area' );

			// Main Fields
			$main_items = [
				'Beds' => get_field('REAL_HOMES_property_bedrooms'),
				'Baths' => get_field('REAL_HOMES_property_bathrooms'),
				'SqFt' => intval(get_field('REAL_HOMES_property_size')),
				'Acres' => get_field('REAL_HOMES_exterior_acres'),
				'Built' => get_field('REAL_HOMES_property_features_year_built'),
				'$/SqFt' => intval($price / $sqft),
			];
			
			// Schools
			$schools = [
				'Elementary School' => get_the_term_list( $id, 'elementary_school' ),
				'Middle School' => get_the_term_list( $id, 'middle_school' ),
				'High School' => get_the_term_list( $id, 'high_school' )
			];

			// Exterior Features
			$exterior_features = [
				'Construction' => get_field('REAL_HOMES_exterior_construction'),
				'Additions' => get_field('REAL_HOMES_exterior_additions'),
				'Exterior' => get_field('REAL_HOMES_exterior_exterior'),
				'Foundation' => get_field('REAL_HOMES_exterior_foundation'),
				'Irrigated Acres' => get_field('REAL_HOMES_exterior_irrigated_acres'),
				'Irrigation' => get_field('REAL_HOMES_exterior_irrigation'),
				'Parking' => get_field('REAL_HOMES_exterior_parking'),
				'Roof' => get_field('REAL_HOMES_exterior_roof'),
				'Exterior Style' => get_field('REAL_HOMES_exterior_style'),
				'Exterior View' => get_field('REAL_HOMES_exterior_view'),
			];
			
			// Interior Features
			$interior_features = [
				'Interior' => get_field('REAL_HOMES_interior_interior'),
				'Rooms' => get_field('REAL_HOMES_interior_rooms'),
				'Doors/Windows' => get_field('REAL_HOMES_interior_doors_windows'),
				'Floors' => get_field('REAL_HOMES_interior_floors'),
				'Heating/Cooling' => get_field('REAL_HOMES_interior_heat_cool'),
				'Bathroom Description' => get_field('REAL_HOMES_interior_bathroom_desc'),
				'Kitchen Description' => get_field('REAL_HOMES_interior_kitchen_desc'),
				'Levels' => get_field('REAL_HOMES_interior_levels'),
				'Ventilation' => get_field('REAL_HOMES_interior_ventilation'),
				'Water Heater' => get_field('REAL_HOMES_interior_water_heater'),
			];
			
			// Property Featured
			$property_features = [
				'New Construction' => get_field('REAL_HOMES_property_features_new_construction'),
				'Property Subtype' => get_field('REAL_HOMES_property_features_subtype'),
				'CCRs' => get_field('REAL_HOMES_property_features_ccrs'),
				'Cross Street' => get_field('REAL_HOMES_property_features_cross_street'),
				'Electric Company' => get_field('REAL_HOMES_property_features_electric_company'),
				'Eps Energy Score' => get_field('REAL_HOMES_property_features_energy_score'),
				'Exempt' => get_field('REAL_HOMES_property_features_exempt'),
				'Tax Year' => get_field('REAL_HOMES_property_features_tax_year'),
				'Tax Amount' => get_field('REAL_HOMES_property_features_tax_amount'),
				'Terms' => get_field('REAL_HOMES_property_features_terms'),
				'Number of Units' => get_field('REAL_HOMES_property_features_number_units'),
				'Subdivision' => get_field('REAL_HOMES_property_features_subdivision'),
				'Existing Water' => get_field('REAL_HOMES_property_features_existing_water'),
				'Farm Deferral' => get_field('REAL_HOMES_property_features_farm_deferral'),
				'Included' => get_field('REAL_HOMES_property_features_included'),
				'Included 2' => get_field('REAL_HOMES_property_features_included2'),
				'Lot Number' => get_field('REAL_HOMES_property_features_lot_number'),
				'Percent Shared Interest' => get_field('REAL_HOMES_property_features_percent_shared'),
				'Sellers Disclosure' => get_field('REAL_HOMES_property_features_sellers_disclosure'),
				'Sewer/Septic' => get_field('REAL_HOMES_property_features_sewer_septic'),
				'Water District' => get_field('REAL_HOMES_property_features_water_district'),
				'Sale Inclusions' => get_field('REAL_HOMES_property_features_sale_inclusions'),
				'Sale Exclusions' => get_field('REAL_HOMES_property_features_sale_exclusions'),
				'Utilities Available' => get_field('REAL_HOMES_utilities'),
				'Road Type' => get_field('REAL_HOMES_road_type'),
				'Current Use' => get_field('REAL_HOMES_current_use'),
				'Directions' => get_field('REAL_HOMES_directions'),
				'Soil Type' => get_field('REAL_HOMES_soil'),
				'Topography' => get_field('REAL_HOMES_topography'),
				'Parking Available' => get_field('REAL_HOMES_parking'),
				'Office Type' => get_field('REAL_HOMES_office_type'),
				'Lease' => get_field('REAL_HOMES_lease'),
				'Business Sale' => get_field('REAL_HOMES_business_sale'),
			];
				
			?>
			<div class="row-fluid">
				<div class="span7">
					<h1 class="property-title"><?php echo bh_the_title(); ?></h1>
					<div class="quick-header-info clearfix">
						<span class="header-price text-green"><strong>$<?php echo number_format($price); ?></strong></span>
						<span class="header-type"><?php echo $property_type; ?></span>
						<?php echo $status_list; ?>
						<span class="header-mls"><?php echo $mls; ?></span>
						<div>
							<?php if( !empty($listing_date))
							echo sprintf('<span class="newness">On Site: <strong>%s</strong></span>', $onsite); ?>
							<span class="updated">Updated: <strong><?php properties_updated_timestamp(); ?></strong></span>
						</div>
					</div>
				</div>
				
				<div class="span5">
					<?php
					// Show featured agent box
					bhAgentRender('sidebar');
					?>
				</div>
			</div>
			
			<div class="main-wrap">
				<div class="row-fluid section1">
					<div class="span7">
						<div class="tabs-wrap">
							<ul class="nav nav-tabs" id="prop-tabs">
								<li class="active"><a href="#tab-photos" data-toggle="tab">Photos</a></li>
								<li><a href="#tab-map" data-toggle="tab">Map</a></li>
								<?php if(!empty($video)) { ?>
								<li><a href="#tab-video" data-toggle="tab">Video</a></li>
								<?php } ?>
							</ul>

							<div id="overview" class="tab-content">
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
					</div>
					
					<div class="span5">
						<div class="main-items-wrap">
							<h2 class="text-center property-price">$<?php echo number_format($price); ?></h2>
							<div class="main-items clearfix">
								<?php								
								foreach( $main_items as $key => $val ) {
									if( !empty($val) ) {
										echo sprintf( '<div class="item"><span class="val">%s</span><span class="key">%s</span></div>', $val, $key );
									}
								}
								?>
							</div>
							<?php 
							// open house info, if array_change_key_case
							get_template_part('bend-homes/open-house-fragment');
							?>

							<div class="basic-info2">
								<table class="table table-striped table-hover">
									<tbody>
										<?php 
										if(!empty($hoa))
										echo sprintf('<tr><td>HOA</td><td class="text-right">%s</td></tr>
											<tr><td>HOA Amount</td><td class="text-right">$%s %s</td></tr>', $hoa, $hoa_amount, $hoa_per);

										if(!empty($county))
										echo sprintf('<tr><td>County</td><td class="text-right">%s</td></tr>', $county);

										if(!empty($area))
										echo sprintf('<tr><td>Area</td><td class="text-right">%s</td></tr>', $area);

										if(!empty($zoning))
										echo sprintf('<tr><td>Zoning</td><td class="text-right">%s</td></tr>', $zoning);
										?>
									</tbody>
								</table>
							</div>
							
							<div class="schools">
								<?php if( implode( $schools ) )
									echo '<h3>School Information</h3>'; ?>
									
								<table class="table table-striped table-hover schools">
									<tbody>
										<?php								
										foreach( $schools as $key => $val ) {
											if( !empty($val) ) {
												echo sprintf( '<tr><td>%s</td><td class="text-right">%s</td></tr>', $key, $val );
											}
										}
										?>
									</tbody>
								</table>
							</div>

							<?php
							// Show share bar icons
							echo do_shortcode('[SHARE_BAR]');
							?>
						</div>
					</div>
				</div>
				
				<div class="row-fluid section2">
					<div class="span5">
						<div class="description">
							<h3>Description</h3>
							<div class="desc-content">
								<?php 
								the_content(); 
								?>
							</div>
							<?php
							bhAgentRender('body');
							?>
						</div>
					</div>
					<div class="span7">
						<?php
						// Mortgage calculator
						echo do_shortcode('[MORT_CALC_FORM id="'. $id .'"]<div class="mort-sponsor"><h4>Find what the real terms of your loan could be&hellip;</h4>[EVERGREEN_LOANS]</div>[/MORT_CALC_FORM]'); ?>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span4">
						<?php if( implode( $exterior_features ) )
							echo '<h3>Exterior Features</h3>'; ?>
						
						<div class="slide-wrapper">
							<div class="slide-content">
								<table class="table table-striped table-hover exterior">
									<tbody>
									<?php								
									foreach( $exterior_features as $key => $val ) {
										if( !empty($val) ) {
											echo sprintf( '<tr><td>%s</td><td class="text-right">%s</td></tr>', $key, $val );
										}
									}
									?>
									</tbody>
								</table>
							</div>
							<div class="slide-menu">
								<a href="#">Read More</a>
							</div>
						</div>
					</div>
					
					<script type="text/javascript">
						var sliderHeight = "100px";
 
						$(document).ready(function(){
							$('.slide-content').each(function () {
										var current = $(this);
										current.attr("box_h", current.height());
									}

							 );

							$(".slide-content").css("height", sliderHeight, "overflow", "hidden");
							$(".slide-menu").html('<a href="#">Read More</a>');
							$(".slide-menu a").click(function() { openSlider() })

						});

						function openSlider()

						{
							var open_height = $(".slide-content").attr("box_h") + "px";
							$(".slide-content").animate({"height": open_height}, {duration: "slow" });
							$(".slide-menu").html('<a href="#">Close</a>');
							$(".slide-menu a").click(function(e) { closeSlider(); e.preventDefault; })
						}

						function closeSlider()

						{
							$(".slide-content").animate({"height": sliderHeight}, {duration: "slow" });
							$(".slide-menu").html('<a href="#">Read More</a>');
							$(".slide-menu a").click(function(e) { openSlider(); e.preventDefault; })
						}
					</script>
					
					<div class="span4">
						<?php if( implode( $interior_features ) )
							echo '<h3>Interior Features</h3>'; ?>
						
						<table class="table table-striped table-hover interior">
							<tbody>
							<?php								
							foreach( $interior_features as $key => $val ) {
								if( !empty($val) ) {
									echo sprintf( '<tr><td>%s</td><td class="text-right">%s</td></tr>', $key, $val );
								}
							}
							?>
							</tbody>
						</table>
					</div>
					
					<div class="span4">
						<?php if( implode( $property_features ) )
							echo '<h3>Property Features</h3>'; ?>
							
						<table class="table table-striped table-hover features">
							<tbody>
							<?php								
							foreach( $property_features as $key => $val ) {
								if( !empty($val) ) {
									echo sprintf( '<tr><td>%s</td><td class="text-right">%s</td></tr>', $key, $val );
								}
							}
							?>
							</tbody>
						</table>
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

<?php
/**
 * Register meta boxes
 */

add_filter( 'rwmb_meta_boxes', 'inspiry_register_meta_boxes' );

if( !function_exists( 'inspiry_register_meta_boxes' ) ) {
    function inspiry_register_meta_boxes( $meta_boxes ) {

        $prefix = 'REAL_HOMES_';

        // Video embed code meta box for video post format
        $meta_boxes[] = array(
            'id' => 'video-meta-box',
            'title' => __('Video Embed Code', 'framework'),
            'post_types' => array('post'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Video Embed Code', 'framework'),
                    'desc' => __('If you are not using self hosted videos then please provide the video embed code and remove the width and height attributes.', 'framework'),
                    'id' => "{$prefix}embed_code",
                    'type' => 'textarea',
                    'cols' => '20',
                    'rows' => '3'
                )
            )
        );


        // Gallery Meta Box
        $meta_boxes[] = array(
            'id' => 'gallery-meta-box',
            'title' => __('Gallery Images', 'framework'),
            'post_types' => array('post'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Upload Gallery Images', 'framework'),
                    'id' => "{$prefix}gallery",
                    'desc' => __('Images should have minimum width of 830px and minimum height of 323px, Bigger size images will be cropped automatically.', 'framework'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 48
                )
            )
        );


        // Agents
        $agents_array = array(-1 => __('None', 'framework'));
        $agents_posts = get_posts(array('post_type' => 'agent', 'posts_per_page' => -1, 'suppress_filters' => 0));
        if (!empty($agents_posts)) {
            foreach ($agents_posts as $agent_post) {
                $agents_array[$agent_post->ID] = $agent_post->post_title;
            }
        }

        // Property Details Meta Box
        $meta_boxes[] = array(
            'id' => 'property-meta-box',
            'title' => __('Property', 'framework'),
            'post_types' => array('property'),
            'tabs' => array(
                'details' => array(
                    'label' => __('Basic Information', 'framework'),
                    'icon' => 'dashicons-admin-home',
                ),
				'exterior' => array(
					'label' => __('Exterior Features', 'framework'),
					'icon' => 'dashicons-list-view',
				),
				'interior' => array(
					'label' => __('Interior Features', 'framework'),
					'icon' => 'dashicons-flag',
				),
				'property-features' => array(
					'label' => __('Property Features', 'framework'),
					'icon' => 'dashicons-tag',
				),
                'gallery' => array(
                    'label' => __('Gallery Images', 'framework'),
                    'icon' => 'dashicons-format-gallery',
                ),
                'floor-plans' => array(
                    'label' => __('Floor Plans', 'framework'),
                    'icon' => 'dashicons-layout',
                ),
                'video' => array(
                    'label' => __('Property Video', 'framework'),
                    'icon' => 'dashicons-format-video',
                ),
                'agent' => array(
                    'label' => __('Agent Information', 'framework'),
                    'icon' => 'dashicons-businessman',
                ),
                'misc' => array(
                    'label' => __('Misc', 'framework'),
                    'icon' => 'dashicons-lightbulb',
                ),
                'home-slider' => array(
                    'label' => __('Homepage Slider', 'framework'),
                    'icon' => 'dashicons-images-alt',
                ),
                /*'banner' => array(
                    'label' => __('Top Banner', 'framework'),
                    'icon' => 'dashicons-format-image',
                ),*/
            ),
            'tab_style' => 'left',
            'fields' => array(

                // Details
                array(
                    'id' => "{$prefix}property_price",
                    'name' => __('Sale or Rent Price ( Only digits )', 'framework'),
                    'desc' => __('Example Value: 435000', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_price_postfix",
                    'name' => __('Price Postfix', 'framework'),
                    'desc' => __('Example Value: Per Month', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_size",
                    'name' => __('Area Size ( Only digits )', 'framework'),
                    'desc' => __('Example Value: 2500', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_size_postfix",
                    'name' => __('Size Postfix', 'framework'),
                    'desc' => __('Example Value: Sq Ft', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_bedrooms",
                    'name' => __('Bedrooms', 'framework'),
                    'desc' => __('Example Value: 4', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_bathrooms",
                    'name' => __('Bathrooms', 'framework'),
                    'desc' => __('Example Value: 2', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_garage",
                    'name' => __('Garages', 'framework'),
                    'desc' => __('Example Value: 1', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_id",
                    'name' => __('Property ID', 'framework'),
                    'desc' => __('It will help you search a property directly.', 'framework'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'details',
                ),


                // Map
                array(
                    'type' => 'divider',
                    'columns' => 12,
                    'id' => 'google_map_divider', // Not used, but needed
                    'tab' => 'details',
                ),
                array(
                    'name' => __('Do you want to hide Google map on property detail page ?', 'framework'),
                    'id' => "{$prefix}property_map",
                    'type' => 'checkbox',
                    'std' => 0,
                    'desc' => __( 'Yes', 'framework' ),
                    'columns' => 12,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_address",
                    'name' => __('Property Address', 'framework'),
                    'desc' => __('Leaving it empty will hide the google map on property detail page.', 'framework'),
                    'type' => 'text',
                    'std' => get_option( 'theme_submit_default_address' ),
                    'columns' => 12,
                    'tab' => 'details',
                ),
                array(
                    'id' => "{$prefix}property_location",
                    'name' => __('Property Location at Google Map*', 'framework'),
                    'desc' => __('Drag the google map marker to point your property location. You can also use the address field above to search for your property.', 'framework'),
                    'type' => 'map',
                    'std' => get_option( 'theme_submit_default_location' ),   // 'latitude,longitude[,zoom]' (zoom is optional)
                    'style' => 'width: 95%; height: 400px',
                    'address_field' => "{$prefix}property_address",
                    'columns' => 12,
                    'tab' => 'details',
                ),
				
				// Exterior Features
				array(
					'id' => "{$prefix}exterior_construction",
					'name' => __('Construction', 'framework'),
					'desc' => __('Example Value: Frame', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 12,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_exterior",
					'name' => __('Exterior', 'framework'),
					'desc' => __('Example Value: Sprinkler system', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_foundation",
					'name' => __('Foundation', 'framework'),
					'desc' => __('Example Value: Stemwall', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_irrigated_acres",
					'name' => __('Irrigated Acres', 'framework'),
					'desc' => __('Example Value: 2', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_irrigation",
					'name' => __('Irrigation', 'framework'),
					'desc' => __('Example Value: No', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_parking",
					'name' => __('Parking', 'framework'),
					'desc' => __('Example Value: Driveway', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_roof",
					'name' => __('Roof', 'framework'),
					'desc' => __('Example Value: Composition', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_style",
					'name' => __('Exterior Style', 'framework'),
					'desc' => __('Example Value: Traditional', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				array(
					'id' => "{$prefix}exterior_view",
					'name' => __('Exterior View', 'framework'),
					'desc' => __('Example Value: Cascade Mountain', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'exterior',
				),
				
				// Interior Featured
				array(
					'id' => "{$prefix}interior_bathroom_desc",
					'name' => __('Bathroom Desc.', 'framework'),
					'desc' => __('Example Value: Tiled vanity', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_doors_windows",
					'name' => __('Doors/Windows', 'framework'),
					'desc' => __('Example Value: Sliding Glass', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_floors",
					'name' => __('Floors', 'framework'),
					'desc' => __('Example Value: Carpet', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_heat_cool",
					'name' => __('Heating/Cooling', 'framework'),
					'desc' => __('Example Value: Forced Air', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_interior",
					'name' => __('Interior', 'framework'),
					'desc' => __('Example Value: Ceiling fan', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_kitchen_desc",
					'name' => __('Kitchen Description', 'framework'),
					'desc' => __('Example Value: Island', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_levels",
					'name' => __('Levels', 'framework'),
					'desc' => __('Example Value: 2 Story', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_rooms",
					'name' => __('Rooms', 'framework'),
					'desc' => __('Example Value: Kitchen', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_ventilation",
					'name' => __('Ventilation', 'framework'),
					'desc' => __('Example Value: Natural Ventilation', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				array(
					'id' => "{$prefix}interior_water_heater",
					'name' => __('Water Heater', 'framework'),
					'desc' => __('Example Value: Electric', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'interior',
				),
				
				// Property Featured
				array(
					'id' => "{$prefix}property_features_year_built",
					'name' => __('Year Built', 'framework'),
					'desc' => __('Example Value: 2001', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_ccrs",
					'name' => __('CCRs', 'framework'),
					'desc' => __('Example Value: Yes', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_cross_street",
					'name' => __('Cross Street Address', 'framework'),
					'desc' => __('Example Value: SW Lava', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_electric_company",
					'name' => __('Electric Company', 'framework'),
					'desc' => __('Example Value: Pp&L', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_energy_score",
					'name' => __('Eps Energy Score', 'framework'),
					'desc' => __('Example Value: 0', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_exempt",
					'name' => __('Exempt', 'framework'),
					'desc' => __('Example Value: No', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_tax_year",
					'name' => __('Tax Year', 'framework'),
					'desc' => __('Example Value: 2016', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_tax_amount",
					'name' => __('Tax Amount', 'framework'),
					'desc' => __('Example Value: 2000', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_terms",
					'name' => __('Terms', 'framework'),
					'desc' => __('Example Value: New Loan', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 4,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_existing_water",
					'name' => __('Existing Water', 'framework'),
					'desc' => __('Example Value: Back Flow', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_farm_deferral",
					'name' => __('Farm Deferral', 'framework'),
					'desc' => __('Example Value: No', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_hoa",
					'name' => __('HOA', 'framework'),
					'desc' => __('Example Value: No', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_hoa_amount",
					'name' => __('HOA Amount', 'framework'),
					'desc' => __('Example Value: 200', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_included",
					'name' => __('Included', 'framework'),
					'desc' => __('Example Value: Stove', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_included2",
					'name' => __('Included 2', 'framework'),
					'desc' => __('Example Value: Patio Screened Room', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_lot_number",
					'name' => __('Lot Number', 'framework'),
					'desc' => __('Example Value: 103', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_new_construction",
					'name' => __('New Construction', 'framework'),
					'desc' => __('Example Value: No', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_percent_shared",
					'name' => __('Percent of Shared Interest', 'framework'),
					'desc' => __('Example Value: 0', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_subtype",
					'name' => __('Property Subtype', 'framework'),
					'desc' => __('Example Value: Residential', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_sellers_disclosure",
					'name' => __('Sellers Disclosure', 'framework'),
					'desc' => __('Example Value: Yes', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_sewer_septic",
					'name' => __('Sewer/Septic', 'framework'),
					'desc' => __('Example Value: City Sewer', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_water_district",
					'name' => __('Water District', 'framework'),
					'desc' => __('Example Value: City', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),
				array(
					'id' => "{$prefix}property_features_zoning",
					'name' => __('Zoning', 'framework'),
					'desc' => __('Example Value: Rs', 'framework'),
					'type' => 'text',
					'std' => "",
					'columns' => 6,
					'tab' => 'property-features',
				),

                // Gallery
                array(
                    'name' => __('Gallery Type You Want to Use', 'framework'),
                    'id' => "{$prefix}gallery_slider_type",
                    'type' => 'radio',
                    'std' => 'thumb-on-right',
                    'options' => array(
                        'thumb-on-right' => __('Gallery with thumbnails on right', 'framework'),
                        'thumb-on-bottom' => __('Gallery with thumbnails on bottom', 'framework')
                    ),
                    'columns' => 12,
                    'tab' => 'gallery',
                ),
                array(
                    'name' => __('Property Gallery Images', 'framework'),
                    'id' => "{$prefix}property_images",
                    'desc' => __('Images should have minimum size of 770px by 386px for thumbnails on right and 830px by 460px for thumbnails on bottom. Bigger size images will be cropped automatically.', 'framework'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 48,
                    'columns' => 12,
                    'tab' => 'gallery',
                ),

				// Floor Plans
                array(
                    'id'    => "inspiry_floor_plans",
                    'type'  => 'group',
                    'columns' => 12,
                    'clone' => true,
                    'tab'   => 'floor-plans',
                    'fields' => array(
                        array(
                            'name' => __( 'Floor Name', 'framework' ),
                            'id'   => "inspiry_floor_plan_name",
                            'desc' => __( 'Example: Ground Floor', 'framework' ),
                            'type' => 'text',
                        ),
                        array(
                            'name' => __( 'Floor Price ( Only digits )', 'framework' ),
                            'id'   => "inspiry_floor_plan_price",
                            'desc' => __( 'Example: 4000', 'framework' ),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => __( 'Price Postfix', 'framework' ),
                            'id'   => "inspiry_floor_plan_price_postfix",
                            'desc' => __( 'Example: Per Month', 'framework' ),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => __( 'Floor Size ( Only digits )', 'framework' ),
                            'id'   => "inspiry_floor_plan_size",
                            'desc' => __( 'Example: 2500', 'framework' ),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => __( 'Size Postfix', 'framework' ),
                            'id'   => "inspiry_floor_plan_size_postfix",
                            'desc' => __( 'Example: Sq Ft', 'framework' ),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => __( 'Bedrooms', 'framework' ),
                            'id'   => "inspiry_floor_plan_bedrooms",
                            'desc' => __( 'Example: 4', 'framework' ),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => __( 'Bathrooms', 'framework' ),
                            'id'   => "inspiry_floor_plan_bathrooms",
                            'desc' => __( 'Example: 2', 'framework' ),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => __( 'Description', 'framework' ),
                            'id'   => "inspiry_floor_plan_descr",
                            'type' => 'textarea',
                        ),
                        array(
                            'name' => __( 'Floor Plan Image', 'framework' ),
                            'id'   => "inspiry_floor_plan_image",
                            'desc' => __( 'The recommended minimum width is 770px and height is flexible.', 'framework' ),
                            'type' => 'file_input',
                            'max_file_uploads' => 1,
                        ),
                    ),
                ),


                // Property Video
                array(
                    'id' => "{$prefix}tour_video_url",
                    'name' => __('Virtual Tour Video URL', 'framework'),
                    'desc' => __('Provide virtual tour video URL. YouTube, Vimeo, SWF File and MOV File are supported', 'framework'),
                    'type' => 'text',
                    'columns' => 12,
                    'tab' => 'video',
                ),
                array(
                    'name' => __('Virtual Tour Video Image', 'framework'),
                    'id' => "{$prefix}tour_video_image",
                    'desc' => __('Provide an image that will be displayed as a place holder and when user will click over it the video will be opened in a lightbox. You must provide this image otherwise the video will not be displayed. Image should have minimum width of 818px and minimum height 417px. Bigger size images will be cropped automatically.', 'framework'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'columns' => 12,
                    'tab' => 'video',
                ),

                // Agents
                array(
                    'name' => __('What to display in agent information box ?', 'framework'),
                    'id' => "{$prefix}agent_display_option",
                    'type' => 'radio',
                    'std' => 'none',
                    'options' => array(
                        'my_profile_info' => __('Author information.', 'framework'),
                        'agent_info' => __('Agent Information. ( Select the agent below )', 'framework'),
                        'none' => __('None. ( Hide information box )', 'framework'),
                    ),
                    'columns' => 12,
                    'tab' => 'agent',
                ),
                array(
                    'name' => __('Agent', 'framework'),
                    'id' => "{$prefix}agents",
                    'type' => 'select',
                    'options' => $agents_array,
                    'multiple' => true,
                    'columns' => 12,
                    'tab' => 'agent',
                ),

                // Misc
                array(
                    'name' => __('Do you want to mark this property as featured ?', 'framework'),
                    'id' => "{$prefix}featured",
                    'type' => 'checkbox',
                    'std' => 0,
                    'desc' => __('Yes', 'framework'),
                    'columns' => 12,
                    'tab' => 'misc',
                ),
                array(
                    'id' => "{$prefix}attachments",
                    'name' => __('Attachments', 'framework'),
                    'desc' => __('You can attach PDF files, Map images OR other documents to provide further details related to property.', 'framework'),
                    'type' => 'file_advanced',
                    'mime_type' => '',
                    'columns' => 12,
                    'tab' => 'misc',
                ),
                array(
                    'id' => "{$prefix}property_private_note",
                    'name' => __('Private Note', 'framework'),
                    'desc' => __('In this textarea, You can write your private note about this property. This field will not be displayed anywhere else.', 'framework'),
                    'type' => 'textarea',
                    'std' => "",
                    'columns' => 12,
                    'tab' => 'misc',
                ),

                // Homepage Slider
                /*array(
                    'name' => __('Do you want to add this property in Homepage Slider ?', 'framework'),
                    'desc' => __('If Yes, Then you need to provide a slider image below.', 'framework'),
                    'id' => "{$prefix}add_in_slider",
                    'type' => 'radio',
                    'std' => 'no',
                    'options' => array(
                        'yes' => __('Yes ', 'framework'),
                        'no' => __('No', 'framework')
                    ),
                    'columns' => 12,
                    'tab' => 'home-slider',
                ),*/
                array(
                    'name' => __('Slider Image', 'framework'),
                    'id' => "{$prefix}slider_image",
                    'desc' => __('The recommended image size is 2000px by 700px. You can use bigger or smaller image but try to keep the same height to width ratio and use the exactly same size images for all properties that will be added in slider.', 'framework'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'columns' => 12,
                    'tab' => 'home-slider',
                ),

                // Top Banner
                /*array(
                    'name' => __('Top Banner Image', 'framework'),
                    'id' => "{$prefix}page_banner_image",
                    'desc' => __('Upload the banner image, If you want to change it for this property. Otherwise default banner image uploaded from theme options will be displayed. Image should have minimum width of 2000px and minimum height of 230px.', 'framework'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'columns' => 12,
                    'tab' => 'banner',
                )*/

            )
        );


        // Partners Meta Box
        $meta_boxes[] = array(
            'id' => 'partners-meta-box',
            'title' => __('Partner Information', 'framework'),
            'post_types' => array('partners'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Website URL', 'framework'),
                    'id' => "{$prefix}partner_url",
                    'desc' => __('Provide Website URL', 'framework'),
                    'type' => 'text',
                )
            )
        );

        // Agent Meta Box
        $meta_boxes[] = array(
            'id' => 'agent-meta-box',
            'title' => __('Provide Related Information', 'framework'),
            'post_types' => array('agent'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Email Address', 'framework'),
                    'id' => "{$prefix}agent_email",
                    'desc' => __("Provide Agent Email Address. Agent related messages from contact form on property details page, will be sent on this email address.", "framework"),
                    'type' => 'text'
                ),
                array(
                    'name' => __('Mobile Number', 'framework'),
                    'id' => "{$prefix}mobile_number",
                    'desc' => __("Provide Agent mobile number", "framework"),
                    'type' => 'text'
                ),
                array(
                    'name' => __('Office Number', 'framework'),
                    'id' => "{$prefix}office_number",
                    'desc' => __("Provide Agent office number", "framework"),
                    'type' => 'text'
                ),
                array(
                    'name' => __('Fax Number', 'framework'),
                    'id' => "{$prefix}fax_number",
                    'desc' => __("Provide Agent fax number", "framework"),
                    'type' => 'text'
                ),
                array(
                    'name' => __('Facebook URL', 'framework'),
                    'id' => "{$prefix}facebook_url",
                    'desc' => __("Provide Agent Facebook URL", "framework"),
                    'type' => 'text'
                ),
                array(
                    'name' => __('Twitter URL', 'framework'),
                    'id' => "{$prefix}twitter_url",
                    'desc' => __("Provide Agent Twitter URL", "framework"),
                    'type' => 'text'
                ),
                array(
                    'name' => __('Google Plus URL', 'framework'),
                    'id' => "{$prefix}google_plus_url",
                    'desc' => __("Provide Agent Google Plus URL", "framework"),
                    'type' => 'text'
                ),
                array(
                    'name' => __('LinkedIn URL', 'framework'),
                    'id' => "{$prefix}linked_in_url",
                    'desc' => __("Provide Agent LinkedIn URL", "framework"),
                    'type' => 'text'
                )
            )
        );


        // Banner Meta Box
        $meta_boxes[] = array(
            'id' => 'banner-meta-box',
            'title' => __('Top Banner Area Settings', 'framework'),
            'post_types' => array('page', 'agent'),
            'context' => 'normal',
            'priority' => 'low',
            'fields' => array(
                array(
                    'name' => __('Banner Title', 'framework'),
                    'id' => "{$prefix}banner_title",
                    'desc' => __('Please provide the Banner Title, Otherwise the Page Title will be displayed in its place.', 'framework'),
                    'type' => 'text'
                ),
                array(
                    'name' => __('Banner Sub Title', 'framework'),
                    'id' => "{$prefix}banner_sub_title",
                    'desc' => __('Please provide the Banner Sub Title.', 'framework'),
                    'type' => 'textarea',
                    'cols' => '20',
                    'rows' => '2'
                ),
                array(
                    'name' => __('Banner Image', 'framework'),
                    'id' => "{$prefix}page_banner_image",
                    'desc' => __('Please upload the Banner Image. Otherwise the default banner image from theme options will be displayed.', 'framework'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1
                ),
                array(
                    'name' => __('Revolution Slider Alias', 'framework'),
                    'id' => "{$prefix}rev_slider_alias",
                    'desc' => __('If you want to replace banner with revolution slider then provide its alias here.', 'framework'),
                    'type' => 'text'
                )
            )
        );

        // Page title show or hide
        $meta_boxes[] = array(
            'id' => 'page-title-meta-box',
            'title' => __('Page Title', 'framework'),
            'post_types' => array('page'),
            'context' => 'normal',
            'priority' => 'low',
            'fields' => array(
                array(
                    'name' => __('Page Title Display Status', 'framework'),
                    'id' => "{$prefix}page_title_display",
                    'type' => 'radio',
                    'std' => 'show',
                    'options' => array(
                        'show' => __('Show', 'framework'),
                        'hide' => __('Hide', 'framework')
                    )
                )
            )
        );

        /*
         * Meta boxes for properties list pages
         */
        $locations = array();
        inspiry_get_terms_array( 'property-city', $locations );

        $types = array();
        inspiry_get_terms_array( 'property-type', $types );

        $statuses = array();
        inspiry_get_terms_array( 'property-status', $statuses );

        $features = array();
        inspiry_get_terms_array( 'property-feature', $features );

        $meta_boxes[] = array(
            'id'        => 'properties-list-meta-box',
            'title'     => __( 'Properties Filter Settings', 'framework' ),
            'post_types'     => array( 'page' ),
            'context'   => 'normal',
            'priority'  => 'high',
            'show'   => array(
                'template'    => array(
                    'template-property-listing.php',
                    'template-property-grid-listing.php',
                    'template-map-based-listing.php',
                ),
            ),
            'fields' => array(
                array(
                    'id'    => 'inspiry_posts_per_page',
                    'name'  => __( 'Number of Properties Per Page', 'framework' ),
                    'type'  => 'number',
                    'step'  => '1',
                    'min'   => 1,
                    'std'   => 6,
                ),
                array(
                    'id'          => "inspiry_properties_order",
                    'name'        => __( 'Order Properties By', 'framework' ),
                    'type'        => 'select',
                    'options'     => array(
                        'date-desc'     => __( 'Date New to Old', 'framework' ),
                        'date-asc'      => __( 'Date Old to New', 'framework' ),
                        'price-asc'     => __( 'Price Low to High', 'framework' ),
                        'price-desc'    => __( 'Price High to Low', 'framework' ),
                    ),
                    'multiple'    => false,
                    'std'         => 'date-desc',
                ),
                array(
                    'id'          => "inspiry_properties_locations",
                    'name'        => __( 'Locations', 'framework' ),
                    'type'        => 'select',
                    'options'     => $locations,
                    'multiple'    => true,
                ),
                array(
                    'id'          => "inspiry_properties_statuses",
                    'name'        => __( 'Statuses', 'framework' ),
                    'type'        => 'select',
                    'options'     => $statuses,
                    'multiple'    => true,
                ),
                array(
                    'id'          => "inspiry_properties_types",
                    'name'        => __( 'Types', 'framework' ),
                    'type'        => 'select',
                    'options'     => $types,
                    'multiple'    => true,
                ),
                array(
                    'id'          => "inspiry_properties_features",
                    'name'        => __( 'Features', 'framework' ),
                    'type'        => 'select',
                    'options'     => $features,
                    'multiple'    => true,
                ),
                array(
                    'id'    => 'inspiry_properties_min_beds',
                    'name'  => __( 'Minimum Beds', 'framework' ),
                    'type'  => 'number',
                    'step'  => 'any',
                    'min'   => 0,
                    'std'   => 0,
                ),
                array(
                    'id'    => 'inspiry_properties_min_baths',
                    'name'  => __( 'Minimum Baths', 'framework' ),
                    'type'  => 'number',
                    'step'  => 'any',
                    'min'   => 0,
                    'std'   => 0,
                ),
                array(
                    'id'    => 'inspiry_properties_min_price',
                    'name'  => __( 'Minimum Price', 'framework' ),
                    'type'  => 'number',
                    'step'  => 'any',
                    'min'   => 0,
                    'std'   => 0,
                ),
                array(
                    'id'    => 'inspiry_properties_max_price',
                    'name'  => __( 'Maximum Price', 'framework' ),
                    'type'  => 'number',
                    'step'  => 'any',
                    'min'   => 0,
                    'std'   => 0,
                ),
            )
        );

        // apply a filter before returning meta boxes
        $meta_boxes = apply_filters('framework_theme_meta', $meta_boxes);

        return $meta_boxes;

    }
}
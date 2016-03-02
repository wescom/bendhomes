<?php
/**
 * Home Customizer
 */


if ( ! function_exists( 'inspiry_home_customizer' ) ) :
	function inspiry_home_customizer( WP_Customize_Manager $wp_customize ) {

		/* Access the property-city terms via an Array */
		$cities_array = array();
		$city_terms = get_terms( 'property-city' );
		foreach ( $city_terms as $city_term ) {
			$cities_array[ $city_term->slug ] = $city_term->name;
		}

		/* Access the property-status terms via an Array */
		$statuses_array = array();
		$status_terms = get_terms( 'property-status' );
		foreach ( $status_terms as $status_term ) {
			$statuses_array[ $status_term->slug ] = $status_term->name;
		}

		/* Access the property-type terms via an Array */
		$types_array = array();
		$type_terms = get_terms( 'property-type' );
		foreach ( $type_terms as $type_term ) {
			$types_array[ $type_term->slug ] = $type_term->name;
		}


		/**
		 * Home Panel
		 */

		$wp_customize->add_panel( 'inspiry_home_panel', array(
			'title' => __( 'Home Page', 'framework' ),
			'priority' => 122,
		) );

		/**
		 * Slider Section
		 */

		$wp_customize->add_section( 'inspiry_home_slider_area', array(
			'title' => __( 'Slider Area', 'framework' ),
			'panel' => 'inspiry_home_panel',
		) );

		/* What to display below header */
		$wp_customize->add_setting( 'theme_homepage_module', array(
			'type' => 'option',
			'default' => 'simple-banner',
		) );
		$wp_customize->add_control( 'theme_homepage_module', array(
			'label' => __( 'What to Display Below Header on Home Page ?', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_slider_area',
			'choices' => array(
				'properties-slider' => __( 'Slider Based on Properties Custom Post Type', 'framework' ),
				'slides-slider' => __( 'Slider Based on Slides Custom Post Type', 'framework' ),
				'properties-map' => __( 'Google Map with Properties Markers', 'framework' ),
				'simple-banner' => __( 'Image Based Banner', 'framework' ),
				'revolution-slider' => __( 'Slider Based on Revolution Slider Plugin.', 'framework' )
			)
		) );

		/* Number of Slides for Properties */
		$wp_customize->add_setting( 'theme_number_of_slides', array(
			'type' => 'option',
			'default' => '3',
		) );
		$wp_customize->add_control( 'theme_number_of_slides', array(
			'label' => __( 'Maximum Number of Slides to Display in Slider Based on Properties', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_home_slider_area',
			'choices' => array(
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
				'6' => 6,
				'7' => 7,
				'8' => 8,
				'9' => 9,
				'10' => 10,
			)
		) );

		/* Number of Slides for Custom Slides */
		$wp_customize->add_setting( 'theme_number_custom_slides', array(
			'type' => 'option',
			'default' => '3',
		) );
		$wp_customize->add_control( 'theme_number_custom_slides', array(
			'label' => __( 'Maximum Number of Slides to Display in Slider Based on Slides Custom Post Type', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_home_slider_area',
			'choices' => array(
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
				'6' => 6,
				'7' => 7,
				'8' => 8,
				'9' => 9,
				'10' => 10,
			)
		) );

		/* Revolution Slider Alias */
		$wp_customize->add_setting( 'theme_rev_alias', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_rev_alias', array(
			'label' => __( 'Revolution Slider Alias', 'framework' ),
			"description" => __( 'If you want to display Revolution Slider then provide its alias here.', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_home_slider_area',
		) );


		/**
		 * Home Search Section
		 */

		$wp_customize->add_section( 'inspiry_home_search', array(
			'title' => __( 'Search Form', 'framework' ),
			'panel' => 'inspiry_home_panel',
		) );

		/* Show/Hide Properties Search Form on Homepage */
		$wp_customize->add_setting( 'theme_show_home_search', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_show_home_search', array(
			'label' => __( 'Properties Search Form on Homepage', 'framework' ),
			"description" => __( 'You can configure properties search form using related section.', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_search',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );


		/**
		 * Home Slogan Section
		 */

		$wp_customize->add_section( 'inspiry_home_slogan', array(
			'title' => __( 'Slogan', 'framework' ),
			'panel' => 'inspiry_home_panel',
		) );

		/* Slogan */
		$wp_customize->add_setting( 'theme_slogan_title', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_slogan_title', array(
			'label' => __( 'Slogan Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_home_slogan',
		) );

		/* Slogan text description */
		$wp_customize->add_setting( 'theme_slogan_text', array(
			'type' => 'option',
			'sanitize_callback' => 'wp_kses_data',
		) );
		$wp_customize->add_control( 'theme_slogan_text', array(
			'label' => __( 'Description Text Below Slogan', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_home_slogan',
		) );


		/**
		 * Home Properties Section
		 */

		$wp_customize->add_section( 'inspiry_home_properties', array(
			'title' => __( 'Home Properties', 'framework' ),
			'panel' => 'inspiry_home_panel',
		) );

		/* Show or Hide Properties on Homepage */
		$wp_customize->add_setting( 'theme_show_home_properties', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_show_home_properties', array(
			'label' => __( 'Show or Hide Slogan + Properties on Homepage ?', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_properties',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Properties on Homepage */
		$wp_customize->add_setting( 'theme_home_properties', array(
			'type' => 'option',
			'default' => 'recent',
		) );
		$wp_customize->add_control( 'theme_home_properties', array(
			'label' => __( 'What Kind of Properties You Want to Display on Homepage ?', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_properties',
			'choices' => array(
				'recent' => __( 'Recent Properties', 'framework' ),
				'featured' => __( 'Featured Properties', 'framework' ),
				'based-on-selection' => __( 'Properties Based on Selected Locations, Statuses and Types from Below', 'framework' )
			)
		) );

		/* Property Locations */
		$wp_customize->add_setting( 'theme_cities_for_homepage', array(
			'type' => 'option',
			'default' => array(),
			'sanitize_callback' => 'inspiry_sanitize_multiple_checkboxes'
		) );
		$wp_customize->add_control(
			new Inspiry_Multiple_Checkbox_Customize_Control(
				$wp_customize,
				'theme_cities_for_homepage',
				array(
					'section' => 'inspiry_home_properties',
					'label' => __( 'Select Property Locations', 'framework' ),
					'choices' => $cities_array,
				)
			)
		);

		/* Property Statuses */
		$wp_customize->add_setting( 'theme_statuses_for_homepage', array(
			'type' => 'option',
			'default' => array(),
			'sanitize_callback' => 'inspiry_sanitize_multiple_checkboxes'
		) );
		$wp_customize->add_control(
			new Inspiry_Multiple_Checkbox_Customize_Control(
				$wp_customize,
				'theme_statuses_for_homepage',
				array(
					'section' => 'inspiry_home_properties',
					'label' => __( 'Select Property Statuses', 'framework' ),
					'choices' => $statuses_array,
				)
			)
		);

		/* Property Types */
		$wp_customize->add_setting( 'theme_types_for_homepage', array(
			'type' => 'option',
			'default' => array(),
			'sanitize_callback' => 'inspiry_sanitize_multiple_checkboxes'
		) );
		$wp_customize->add_control(
			new Inspiry_Multiple_Checkbox_Customize_Control(
				$wp_customize,
				'theme_types_for_homepage',
				array(
					'section' => 'inspiry_home_properties',
					'label' => __( 'Select Property Types', 'framework' ),
					'choices' => $types_array,
				)
			)
		);

		/* Properties on Homepage */
		$wp_customize->add_setting( 'theme_sorty_by', array(
			'type' => 'option',
			'default' => 'recent',
		) );
		$wp_customize->add_control( 'theme_sorty_by', array(
			'label' => __( 'Sort Properties By', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_properties',
			'choices' => array(
				'recent' => __( 'Time - Recent First', 'framework' ),
				'low-to-high' => __( 'Price - Low to High', 'framework' ),
				'high-to-low' => __( 'Price - High to Low', 'framework' ),
			)
		) );

		/* Number of Properties To Display on Home Page */
		$wp_customize->add_setting( 'theme_properties_on_home', array(
			'type' => 'option',
			'default' => '4',
		) );
		$wp_customize->add_control( 'theme_properties_on_home', array(
			'label' => __( 'Number of Properties on Each Page', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_home_properties',
			'choices' => array(
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
				'6' => 6,
				'7' => 7,
				'8' => 8,
				'9' => 9,
				'10' => 10,
				'11' => 11,
				'12' => 12,
				'13' => 13,
				'14' => 14,
				'15' => 15,
				'16' => 16,
				'17' => 17,
				'18' => 18,
				'19' => 19,
				'20' => 20,
			)
		) );

		/* AJAX Pagination */
		$wp_customize->add_setting( 'theme_ajax_pagination_home', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_ajax_pagination_home', array(
			'label' => __( 'AJAX Pagination', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_properties',
			'choices' => array(
				'true' => 'Enable',
				'false' => 'Disable',
			)
		) );


		/**
		 * Home Featured Properties Section
		 */

		$wp_customize->add_section( 'inspiry_home_featured_properties', array(
			'title' => __( 'Featured Properties', 'framework' ),
			'panel' => 'inspiry_home_panel',
		) );

		/* Show/Hide Featured Properties on Homepage */
		$wp_customize->add_setting( 'theme_show_featured_properties', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_show_featured_properties', array(
			'label' => __( 'Featured Properties on Homepage', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_featured_properties',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Title */
		$wp_customize->add_setting( 'theme_featured_prop_title', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_featured_prop_title', array(
			'label' => __( 'Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_home_featured_properties',
		) );

		/* Text */
		$wp_customize->add_setting( 'theme_featured_prop_text', array(
			'type' => 'option',
			'sanitize_callback' => 'wp_kses_data',
		) );
		$wp_customize->add_control( 'theme_featured_prop_text', array(
			'label' => __( 'Description Text', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_home_featured_properties',
		) );

		/* Exclude Featured Properties from Properties on Homepage */
		$wp_customize->add_setting( 'theme_exclude_featured_properties', array(
			'type' => 'option',
			'default' => 'false',
		) );
		$wp_customize->add_control( 'theme_exclude_featured_properties', array(
			'label' => __( 'Exclude or Include Featured Properties from Recent Properties on Homepage', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_featured_properties',
			'choices' => array(
				'true' => 'Exclude',
				'false' => 'Include',
			)
		) );


		/**
		 * Home News Section
		 */

		$wp_customize->add_section( 'inspiry_home_news', array(
			'title' => __( 'News or Blog Posts', 'framework' ),
			'panel' => 'inspiry_home_panel',
		) );

		/* Show or Hide News on Homepage */
		$wp_customize->add_setting( 'theme_show_news_posts', array(
			'type' => 'option',
			'default' => 'false',
		) );
		$wp_customize->add_control( 'theme_show_news_posts', array(
			'label' => __( 'News Posts on Homepage', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_home_news',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* News Title */
		$wp_customize->add_setting( 'theme_news_posts_title', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_news_posts_title', array(
			'label' => __( 'Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_home_news',
		) );

		/* News Text */
		$wp_customize->add_setting( 'theme_news_posts_text', array(
			'type' => 'option',
			'sanitize_callback' => 'wp_kses_data',
		) );
		$wp_customize->add_control( 'theme_news_posts_text', array(
			'label' => __( 'Description Text', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_home_news',
		) );

	}

	add_action( 'customize_register', 'inspiry_home_customizer' );
endif;
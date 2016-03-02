<?php
/**
 * Property Customizer
 */


if ( ! function_exists( 'inspiry_property_customizer' ) ) :
	function inspiry_property_customizer( WP_Customize_Manager $wp_customize ) {


		/**
		 * Property Panel
		 */

		$wp_customize->add_panel( 'inspiry_property_panel', array(
			'title' => __( 'Property Detail Page', 'framework' ),
			'priority' => 126,
		) );

		/**
		 * Breadcrumbs Section
		 */

		$wp_customize->add_section( 'inspiry_property_breadcrumbs', array(
			'title' => __( 'Breadcrumbs', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* Show/Hide Breadcrumbs */
		$wp_customize->add_setting( 'theme_display_property_breadcrumbs', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_property_breadcrumbs', array(
			'label' => __( 'Property Breadcrumbs', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_breadcrumbs',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* property breadcrumbs taxonomy */
		$wp_customize->add_setting( 'theme_breadcrumbs_taxonomy', array(
			'type' => 'option',
			'default' => 'property-city',
		) );
		$wp_customize->add_control( 'theme_breadcrumbs_taxonomy', array(
			'label' => __( 'Breadcrumbs will be based on', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_breadcrumbs',
			'choices' => array(
				'property-city' => __( 'Property City', 'framework' ),
				'property-type' => __( 'Property Type', 'framework' ),
				'property-status' => __( 'Property Status', 'framework' ),
			)
		) );


		/**
		 * Basics Section
		 */

		$wp_customize->add_section( 'inspiry_property_basics', array(
			'title' => __( 'Basics', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* property detail variation */
		$wp_customize->add_setting( 'theme_property_detail_variation', array(
			'type' => 'option',
			'default' => 'default',
		) );
		$wp_customize->add_control( 'theme_property_detail_variation', array(
			'label' => __( 'Property Detail Page Layout Variation ?', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_basics',
			'choices' => array(
				'default' => __( 'Agent info below Google Map', 'framework' ),
				'agent-in-sidebar' => __( 'Agent info in Sidebar', 'framework' ),
			)
		) );

		/* Additional Detail Title  */
		$wp_customize->add_setting( 'theme_additional_details_title', array(
			'type' => 'option',
			'default' => __( 'Additional Details', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_additional_details_title', array(
			'label' => __( 'Additional Details Title', 'framework' ),
			'description' => __( 'This will only display if a property has additional details.', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_basics',
		) );

		/* Features Title  */
		$wp_customize->add_setting( 'theme_property_features_title', array(
			'type' => 'option',
			'default' => __( 'Features', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_property_features_title', array(
			'label' => __( 'Features Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_basics',
		) );

		/* Show/Hide Social Share */
		$wp_customize->add_setting( 'theme_display_social_share', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_social_share', array(
			'label' => __( 'Social Share Icons', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_basics',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Child Properties Title  */
		$wp_customize->add_setting( 'theme_child_properties_title', array(
			'type' => 'option',
			'default' => __( 'Sub Properties', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_child_properties_title', array(
			'label' => __( 'Child Properties Title', 'framework' ),
			'description' => __( 'This will only display if a property has child properties.', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_basics',
		) );

		/* Add/Remove  Open Graph Meta Tags */
		$wp_customize->add_setting( 'theme_add_meta_tags', array(
			'type' => 'option',
			'default' => 'false',
		) );
		$wp_customize->add_control( 'theme_add_meta_tags', array(
			'label' => __( 'Open Graph Meta Tags', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_basics',
			'choices' => array(
				'true' => __( 'Enable', 'framework' ),
				'false' => __( 'Disable', 'framework' ),
			)
		) );



		/**
		 * Common Note Section
		 */

		$wp_customize->add_section( 'inspiry_property_common_note', array(
			'title' => __( 'Common Note', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* Show/Hide Note */
		$wp_customize->add_setting( 'theme_display_common_note', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_common_note', array(
			'label' => __( 'Common Note', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_common_note',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Common Note Title */
		$wp_customize->add_setting( 'theme_common_note_title', array(
			'type' => 'option',
			"default" => __( 'Note', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_common_note_title', array(
			'label' => __( 'Common Note Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_common_note',
		) );

		/* Common Note Text */
		$wp_customize->add_setting( 'theme_common_note', array(
			'type' => 'option',
			'sanitize_callback' => 'wp_kses_data',
		) );
		$wp_customize->add_control( 'theme_common_note', array(
			'label' => __( 'Common Note', 'framework' ),
			"desc" => __( 'Provide common note text. It will be displayed on all properties detail pages.', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_property_common_note',
		) );


		/**
		 * Video Section
		 */

		$wp_customize->add_section( 'inspiry_property_video', array(
			'title' => __( 'Video', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* Show/Hide Video */
		$wp_customize->add_setting( 'theme_display_video', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_video', array(
			'label' => __( 'Property Video', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_video',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Video Title */
		$wp_customize->add_setting( 'theme_property_video_title', array(
			'type' => 'option',
			"default" => __( 'Property Video', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_property_video_title', array(
			'label' => __( 'Property Video Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_video',
		) );


		/**
		 * Map Section
		 */

		$wp_customize->add_section( 'inspiry_property_map', array(
			'title' => __( 'Map', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* Show/Hide Map */
		$wp_customize->add_setting( 'theme_display_google_map', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_google_map', array(
			'label' => __( 'Google Map', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_map',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Map Title */
		$wp_customize->add_setting( 'theme_property_map_title', array(
			'type' => 'option',
			"default" => __( 'Property on Map', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_property_map_title', array(
			'label' => __( 'Property Map Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_map',
		) );


		/**
		 * Attachments Section
		 */

		$wp_customize->add_section( 'inspiry_property_attachments', array(
			'title' => __( 'Attachments', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* Show/Hide Attachments */
		$wp_customize->add_setting( 'theme_display_attachments', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_attachments', array(
			'label' => __( 'Property Attachments', 'framework' ),
			'description' => __( 'Attachments will only appear if there are any.', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_attachments',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Attachments Title */
		$wp_customize->add_setting( 'theme_property_attachments_title', array(
			'type' => 'option',
			"default" => __( 'Property Attachments', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_property_attachments_title', array(
			'label' => __( 'Property Attachments Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_attachments',
		) );


		/**
		 * Agent Section
		 */

		$wp_customize->add_section( 'inspiry_property_agent', array(
			'title' => __( 'Agent', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* Show/Hide Agent Information */
		$wp_customize->add_setting( 'theme_display_agent_info', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_agent_info', array(
			'label' => __( 'Agent Information', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_agent',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Enable/Disable Message Copy */
		$wp_customize->add_setting( 'theme_send_message_copy', array(
			'type' => 'option',
			'default' => 'false',
			'transport' => 'postMessage',
		) );
		$wp_customize->add_control( 'theme_send_message_copy', array(
			'label' => __( 'Get Copy of Message Sent to Agent', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_agent',
			'choices' => array(
				'true' => __( 'Yes', 'framework' ),
				'false' => __( 'No', 'framework' ),
			)
		) );

		/* Email Address to Get a Copy of Agent Message */
		$wp_customize->add_setting( 'theme_message_copy_email', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_email',
			'transport' => 'postMessage',
		) );
		$wp_customize->add_control( 'theme_message_copy_email', array(
			'label' => __( 'Email Address to Get Copy of Message', 'framework' ),
			"description" => __( "Given email address will get a copy of message sent to agent.", 'framework' ),
			'type' => 'email',
			'section' => 'inspiry_property_agent',
		) );



		/**
		 * Similar Properties Section
		 */

		$wp_customize->add_section( 'inspiry_property_similar', array(
			'title' => __( 'Similar Properties', 'framework' ),
			'panel' => 'inspiry_property_panel',
		) );

		/* Show/Hide Similar */
		$wp_customize->add_setting( 'theme_display_similar_properties', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_display_similar_properties', array(
			'label' => __( 'Similar Properties', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_property_similar',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Similar Properties Title */
		$wp_customize->add_setting( 'theme_similar_properties_title', array(
			'type' => 'option',
			"default" => __( 'Similar Properties', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_similar_properties_title', array(
			'label' => __( 'Similar Properties Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_property_similar',
		) );


	}

	add_action( 'customize_register', 'inspiry_property_customizer' );
endif;
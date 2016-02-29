<?php
/**
 * Members Settings
 */

if ( ! function_exists( 'inspiry_members_customizer' ) ) :
	function inspiry_members_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Members Panel
		 */

		$wp_customize->add_panel( 'inspiry_members_panel', array(
			'title' => __( 'Members', 'framework' ),
			'priority' => 127,
		) );

		/**
		 * Members Basic
		 */

		$wp_customize->add_section( 'inspiry_members_basic', array(
			'title' => __( 'Basic', 'framework' ),
			'panel' => 'inspiry_members_panel',
		) );

		/* Restrict Access */
		$wp_customize->add_setting( 'theme_restricted_level', array(
			'type' => 'option',
			'default' => '0',
			'transport' => 'postMessage',
		) );
		$wp_customize->add_control( 'theme_restricted_level', array(
			'label' => __( 'Restrict Admin Side Access', 'framework' ),
			"description" => __( 'Restrict admin side access to any user level equal to or below the selected user level.', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_members_basic',
			'choices' => array(
				'0' => __( 'Subscriber ( Level 0 )', 'framework' ),
				'1' => __( 'Contributor ( Level 1 )', 'framework' ),
				'2' => __( 'Author ( Level 2 )', 'framework' ),
				// '7' => __( 'Editor ( Level 7 )','framework'),
			)
		) );


		/**
		 * Members Login and Register
		 */

		$wp_customize->add_section( 'inspiry_members_login', array(
			'title' => __( 'Login & Register', 'framework' ),
			'panel' => 'inspiry_members_panel',
		) );

		/* Login Page URL */
		$wp_customize->add_setting( 'theme_login_url', array(
			'type' => 'option',
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_login_url', array(
			'label' => __( 'Login & Register Page URL (Optional)', 'framework' ),
			"description" => __( 'Create a Page Using Login & Register Template and Provide its URL here. By default the login modal box will appear and you do not need to configure this option.', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_members_login',
		) );


		/**
		 * Members Edit Profile
		 */

		$wp_customize->add_section( 'inspiry_members_profile', array(
			'title' => __( 'Edit Profile', 'framework' ),
			'panel' => 'inspiry_members_panel',
		) );

		/* Edit Profile URL */
		$wp_customize->add_setting( 'theme_profile_url', array(
			'type' => 'option',
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_profile_url', array(
			'label' => __( 'Edit Profile Page URL', 'framework' ),
			"description" => __( 'Create a Page Using Edit Profile Template and Provide its URL here.', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_members_profile',
		) );


		/**
		 * Members Submit
		 */

		$wp_customize->add_section( 'inspiry_members_submit', array(
			'title' => __( 'Submit Property', 'framework' ),
			'panel' => 'inspiry_members_panel',
		) );

		/* Submit URL */
		$wp_customize->add_setting( 'theme_submit_url', array(
			'type' => 'option',
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_submit_url', array(
			'label' => __( 'Submit Property Page URL', 'framework' ),
			"description" => __( 'Create a Page Using Submit Property Template and Provide its URL here.', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_members_submit',
		) );

		/* Submitted Property Status */
		$wp_customize->add_setting( 'theme_submitted_status', array(
			'type' => 'option',
			'default' => 'pending',
			'transport' => 'postMessage',
		) );
		$wp_customize->add_control( 'theme_submitted_status', array(
			'label' => __( 'Submitted Property Status', 'framework' ),
			"description" => __( 'Select the default status for submitted property.', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_members_submit',
			'choices' => array(
				'pending' => __( 'Pending ( Recommended )', 'framework' ),
				'publish' => __( 'Publish', 'framework' )
			)
		) );

		/* Default Address in Submit Form */
		$wp_customize->add_setting( 'theme_submit_default_address', array(
			'type' => 'option',
			"default" => '15421 Southwest 39th Terrace, Miami, FL 33185, USA',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_submit_default_address', array(
			'label' => __( 'Default Address in Submit Form', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_members_submit',
		) );

		/* Default Map Location */
		$wp_customize->add_setting( 'theme_submit_default_location', array(
			'type' => 'option',
			"default" => '25.7308309,-80.44414899999998',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_submit_default_location', array(
			'label' => __( 'Default Map Location in Submit Form (Latitude,Longitude)', 'framework' ),
			"description" => 'You can use <a href="http://www.latlong.net/" target="_blank">latlong.net</a> OR <a href="http://itouchmap.com/latlong.html" target="_blank">itouchmap.com</a> to get Latitude and longitude of your desired location.',
			'type' => 'text',
			'section' => 'inspiry_members_submit',
		) );

		/* Message after Submit */
		$wp_customize->add_setting( 'theme_submit_message', array(
			'type' => 'option',
			"default" => __( 'Thanks for Submitting Property!', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport' => 'postMessage',
		) );
		$wp_customize->add_control( 'theme_submit_message', array(
			'label' => __( 'Message After Successful Submit', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_members_submit',
		) );

		/* Submit Notice */
		$wp_customize->add_setting( 'theme_submit_notice_email', array(
			'type' => 'option',
			"default" => get_option( 'admin_email' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport' => 'postMessage',
		) );
		$wp_customize->add_control( 'theme_submit_notice_email', array(
			'label' => __( 'Submit Notice Email', 'framework' ),
			'type' => 'email',
			'section' => 'inspiry_members_submit',
		) );


		/**
		 * Members My Properties
		 */

		$wp_customize->add_section( 'inspiry_members_properties', array(
			'title' => __( 'My Properties', 'framework' ),
			'panel' => 'inspiry_members_panel',
		) );

		/* My Properties Page URL */
		$wp_customize->add_setting( 'theme_my_properties_url', array(
			'type' => 'option',
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_my_properties_url', array(
			'label' => __( 'My Properties Page URL', 'framework' ),
			"description" => __( 'Create a Page Using My Properties Template and Provide its URL here.', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_members_properties',
		) );


		/**
		 * Members Add to Favorites
		 */

		$wp_customize->add_section( 'inspiry_members_favorites', array(
			'title' => __( 'Add to Favorites', 'framework' ),
			'panel' => 'inspiry_members_panel',
		) );

		/* Enable/Disable Add to Favorites */
		$wp_customize->add_setting( 'theme_enable_fav_button', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_enable_fav_button', array(
			'label' => __( 'Add to Favorites Button on Property Detail Page', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_members_favorites',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Favorites Page URL */
		$wp_customize->add_setting( 'theme_favorites_url', array(
			'type' => 'option',
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_favorites_url', array(
			'label' => __( 'Favorite Properties Page URL', 'framework' ),
			"description" => __( 'Create a Page Using Favorite Properties Template and Provide its URL here.', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_members_favorites',
		) );


	}

	add_action( 'customize_register', 'inspiry_members_customizer' );
endif;
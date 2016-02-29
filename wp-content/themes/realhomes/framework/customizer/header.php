<?php
/**
 * Customizer settings for Header
 */

if ( ! function_exists( 'inspiry_header_customizer' ) ) :
	function inspiry_header_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Site Identity
		 */
		/* Logo */
		$wp_customize->add_setting( 'theme_sitelogo', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'theme_sitelogo', array(
			'label' => __( 'Site Logo', 'framework' ),
			'section' => 'title_tagline',   // id of site identity section - Ref: https://developer.wordpress.org/themes/advanced-topics/customizer-api/
			'settings' => 'theme_sitelogo',
			'priority' => 1,
		) ) );


		/**
		 * Header Panel
		 */

		$wp_customize->add_panel( 'inspiry_header_panel', array(
			'title' => __( 'Header', 'framework' ),
			'priority' => 121,
		) );


		/**
		 * Social Icons Section
		 */

		$wp_customize->add_section( 'inspiry_header_social_icons', array(
			'title' => __( 'Social Icons', 'framework' ),
			'panel' => 'inspiry_header_panel',
		) );

		/* Enable/Disable Social Icons */
		$wp_customize->add_setting( 'theme_show_social_menu', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_show_social_menu', array(
			'label' => __( 'Show or Hide Social Icons', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_header_social_icons', // Required, core or custom.
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Facebook URL */
		$wp_customize->add_setting( 'theme_facebook_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_facebook_link', array(
			'label' => __( 'Facebook URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );

		/* Twitter URL */
		$wp_customize->add_setting( 'theme_twitter_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_twitter_link', array(
			'label' => __( 'Twitter URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );

		/* LinkedIn URL */
		$wp_customize->add_setting( 'theme_linkedin_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_linkedin_link', array(
			'label' => __( 'LinkedIn URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );

		/* Google Plus URL */
		$wp_customize->add_setting( 'theme_google_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_google_link', array(
			'label' => __( 'Google Plus URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );

		/* Instagram URL */
		$wp_customize->add_setting( 'theme_instagram_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_instagram_link', array(
			'label' => __( 'Instagram URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );

		/* YouTube URL */
		$wp_customize->add_setting( 'theme_youtube_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_youtube_link', array(
			'label' => __( 'YouTube URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );

		/* Skype Username */
		$wp_customize->add_setting( 'theme_skype_username', array(
			'type' => 'option',
		) );
		$wp_customize->add_control( 'theme_skype_username', array(
			'label' => __( 'Skype Username', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_header_social_icons',
		) );

		/* Pinterest URL */
		$wp_customize->add_setting( 'theme_pinterest_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_pinterest_link', array(
			'label' => __( 'Pinterest URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );

		/* RSS URL */
		$wp_customize->add_setting( 'theme_rss_link', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'theme_rss_link', array(
			'label' => __( 'RSS URL', 'framework' ),
			'type' => 'url',
			'section' => 'inspiry_header_social_icons',
		) );


		/**
		 * User Navigation
		 */

		$wp_customize->add_section( 'inspiry_header_user_nav', array(
			'title' => __( 'User Navigation', 'framework' ),
			'panel' => 'inspiry_header_panel',
		) );

		/* Show / Hide Header Navigation for Members */
		$wp_customize->add_setting( 'theme_enable_user_nav', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_enable_user_nav', array(
			'label' => __( 'Header Navigation for User Login and Register', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_header_user_nav',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/**
		 * Header Contact Information Section
		 */

		$wp_customize->add_section( 'inspiry_header_contact_info', array(
			'title' => __( 'Contact Information', 'framework' ),
			'panel' => 'inspiry_header_panel',
		) );

		/* Header Email */
		$wp_customize->add_setting( 'theme_header_email', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_email',
		) );
		$wp_customize->add_control( 'theme_header_email', array(
			'label' => __( 'Email Address', 'framework' ),
			'type' => 'email',
			'section' => 'inspiry_header_contact_info',
		) );

		/* Header Phone Number */
		$wp_customize->add_setting( 'theme_header_phone', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_header_phone', array(
			'label' => __( 'Phone Number', 'framework' ),
			'type' => 'tel',
			'section' => 'inspiry_header_contact_info',
		) );


		/**
		 * Banner Section
		 */

		$wp_customize->add_section( 'inspiry_header_banner', array(
			'title' => __( 'Banner', 'framework' ),
			'panel' => 'inspiry_header_panel',
		) );

		$wp_customize->add_setting( 'theme_general_banner_image', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'theme_general_banner_image',
				array(
					'label' => __( 'Header Banner Image', 'framework' ),
					"description" => __( 'Required minimum height is 230px and minimum width is 2000px.', 'framework' ),
					'section' => 'inspiry_header_banner',
				)
			)
		);

		$wp_customize->add_setting( 'theme_banner_titles', array(
			'type' => 'option',
			'default' => 'false',
		) );
		$wp_customize->add_control( 'theme_banner_titles', array(
			'label' => __( 'Hide Title and Subtitle From Image Banner', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_header_banner',
			'choices' => array(
				'true' => __( 'Yes', 'framework' ),
				'false' => __( 'No', 'framework' ),
			)
		) );

		$wp_customize->add_setting( 'theme_banner_text_color', array(
			'type' => 'option',
			'default' => '#394041',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_banner_text_color',
				array(
					'label' => __( 'Banner Title Color', 'framework' ),
					'section' => 'inspiry_header_banner',
				)
			)
		);

		$wp_customize->add_setting( 'theme_banner_title_bg_color', array(
			'type' => 'option',
			'default' => '#f5f4f3',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_banner_title_bg_color',
				array(
					'label' => __( 'Banner Title Background Color', 'framework' ),
					'section' => 'inspiry_header_banner',
				)
			)
		);

		$wp_customize->add_setting( 'theme_banner_sub_text_color', array(
			'type' => 'option',
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_banner_sub_text_color',
				array(
					'label' => __( 'Banner Sub Title Color', 'framework' ),
					'section' => 'inspiry_header_banner',
				)
			)
		);

		$wp_customize->add_setting( 'theme_banner_sub_title_bg_color', array(
			'type' => 'option',
			'default' => '#37B3D9',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_banner_sub_title_bg_color',
				array(
					'label' => __( 'Banner Sub Title Background Color', 'framework' ),
					'section' => 'inspiry_header_banner',
				)
			)
		);



		/**
		 * Header Others
		 */

		$wp_customize->add_section( 'inspiry_header_others', array(
			'title' => __( 'Others', 'framework' ),
			'panel' => 'inspiry_header_panel',
		) );

		/* Sticky Header */
		$wp_customize->add_setting( 'theme_sticky_header', array(
			'type' => 'option',
			'default' => 'false',
		) );
		$wp_customize->add_control( 'theme_sticky_header', array(
			'label' => __( 'Sticky Header', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_header_others',
			'choices' => array(
				'true' => 'Enable',
				'false' => 'Disable',
			)
		) );

		/* Enable / Disable WPML Language switcher */
		$wp_customize->add_setting( 'theme_wpml_lang_switcher', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_wpml_lang_switcher', array(
			'label' => __( 'Display WPML Language Switcher in Top Header', 'framework' ),
			'description' => __( 'Only works if WPML is installed.', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_header_others',
			'choices' => array(
				'true' => __( 'Yes', 'framework' ),
				'false' => __( 'No', 'framework' ),
			)
		) );

		/* Tracking Code */
		$wp_customize->add_setting( 'theme_google_analytics', array(
			'type' => 'option',
		) );
		$wp_customize->add_control( 'theme_google_analytics', array(
			'label' => __( 'Google Analytics', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_header_others',
		) );


		/**
		 * Styles Section
		 */

		$wp_customize->add_section( 'inspiry_header_styles', array(
			'title' => __( 'Styles', 'framework' ),
			'panel' => 'inspiry_header_panel',
		) );

		/* Header Background Color */
		$wp_customize->add_setting( 'theme_header_bg_color', array(
			'type' => 'option',
			'default' => '#252A2B',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,              // WP_Customize_Manager
				'theme_header_bg_color',    // Setting id
				array(
					'label' => __( 'Header Background Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Logo Text Color */
		$wp_customize->add_setting( 'theme_logo_text_color', array(
			'type' => 'option',
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_logo_text_color',
				array(
					'label' => __( 'Logo Text Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Logo Text Hover Color */
		$wp_customize->add_setting( 'theme_logo_text_hover_color', array(
			'type' => 'option',
			'default' => '#4dc7ec',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_logo_text_hover_color',
				array(
					'label' => __( 'Logo Text Hover Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Tagline Text Color */
		$wp_customize->add_setting( 'theme_tagline_text_color', array(
			'type' => 'option',
			'default' => '#8b9293',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_tagline_text_color',
				array(
					'label' => __( 'Tagline Text Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Tagline Background Color */
		$wp_customize->add_setting( 'theme_tagline_bg_color', array(
			'type' => 'option',
			'default' => '#343a3b',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_tagline_bg_color',
				array(
					'label' => __( 'Tagline Background Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Header Text Color */
		$wp_customize->add_setting( 'theme_header_text_color', array(
			'type' => 'option',
			'default' => '#929A9B',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_header_text_color',
				array(
					'label' => __( 'Header Text Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Header Links Hover Color */
		$wp_customize->add_setting( 'theme_header_link_hover_color', array(
			'type' => 'option',
			'default' => '#b0b8b9',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_header_link_hover_color',
				array(
					'label' => __( 'Header Links Hover Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Header Borders Color */
		$wp_customize->add_setting( 'theme_header_border_color', array(
			'type' => 'option',
			'default' => '#343A3B',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_header_border_color',
				array(
					'label' => __( 'Header Borders Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Main Menu Text Color */
		$wp_customize->add_setting( 'theme_main_menu_text_color', array(
			'type' => 'option',
			'default' => '#afb4b5',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_main_menu_text_color',
				array(
					'label' => __( 'Main Menu Text Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Drop Down Menu Background Color */
		$wp_customize->add_setting( 'theme_menu_bg_color', array(
			'type' => 'option',
			'default' => '#ec894d',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_menu_bg_color',
				array(
					'label' => __( 'Drop Down Menu Background Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Drop Down Menu Text Color */
		$wp_customize->add_setting( 'theme_menu_text_color', array(
			'type' => 'option',
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_menu_text_color',
				array(
					'label' => __( 'Drop Down Menu Text Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Drop Down Menu Background Color on Mouse Over */
		$wp_customize->add_setting( 'theme_menu_hover_bg_color', array(
			'type' => 'option',
			'default' => '#dc7d44',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_menu_hover_bg_color',
				array(
					'label' => __( 'Drop Down Menu Background Color on Mouse Over', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		/* Header Phone Number Background Color */
		$wp_customize->add_setting( 'theme_phone_bg_color', array(
			'type' => 'option',
			'default' => '#4dc7ec',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_phone_bg_color',
				array(
					'label' => __( 'Header Phone Number Background Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		$wp_customize->add_setting( 'theme_phone_text_color', array(
			'type' => 'option',
			'default' => '#e7eff7',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_phone_text_color',
				array(
					'label' => __( 'Header Phone Number Text Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);

		$wp_customize->add_setting( 'theme_phone_icon_bg_color', array(
			'type' => 'option',
			'default' => '#37b3d9',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_phone_icon_bg_color',
				array(
					'label' => __( 'Header Phone Icon Background Color', 'framework' ),
					'section' => 'inspiry_header_styles',
				)
			)
		);


	}

	add_action( 'customize_register', 'inspiry_header_customizer' );
endif;
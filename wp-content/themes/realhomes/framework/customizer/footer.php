<?php
/**
 * Footer Customizer Settings
 */

if ( ! function_exists( 'inspiry_footer_customizer' ) ) :
	function inspiry_footer_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Footer Panel
		 */

		$wp_customize->add_panel( 'inspiry_footer_panel', array(
			'title' => __( 'Footer', 'framework' ),
			'priority' => 125,
		) );

		/**
		 * Partners Section
		 */

		$wp_customize->add_section( 'inspiry_footer_partners', array(
			'title' => __( 'Partners', 'framework' ),
			'panel' => 'inspiry_footer_panel',
		) );

		/* Show / Hide Partners */
		$wp_customize->add_setting( 'theme_show_partners', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_show_partners', array(
			'label' => __( 'Partners Carousel Above Footer', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_footer_partners',
			'choices' => array(
				'true' => __( 'Show', 'framework' ),
				'false' => __( 'Hide', 'framework' ),
			)
		) );

		/* Partners Title */
		$wp_customize->add_setting( 'theme_partners_title', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_partners_title', array(
			'label' => __( 'Partners Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_footer_partners',
		) );

		/**
		 * Footer Text Section
		 */

		$wp_customize->add_section( 'inspiry_footer_text', array(
			'title' => __( 'Text', 'framework' ),
			'panel' => 'inspiry_footer_panel',
		) );

		/* Copyright Text */
		$wp_customize->add_setting( 'theme_copyright_text', array(
			'type' => 'option',
			'sanitize_callback' => 'wp_kses_data',
		) );
		$wp_customize->add_control( 'theme_copyright_text', array(
			'label' => __( 'Copyright Text', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_footer_text',
		) );

		/* Designed By Text */
		$wp_customize->add_setting( 'theme_designed_by_text', array(
			'type' => 'option',
			'default' => 'Designed by <a target="_blank" href="http://www.inspirythemes.com">Inspiry Themes</a>',
			'sanitize_callback' => 'wp_kses_data',
		) );
		$wp_customize->add_control( 'theme_designed_by_text', array(
			'label' => __( 'Designed by Text', 'framework' ),
			'type' => 'textarea',
			'section' => 'inspiry_footer_text',
		) );

		/**
		 * Footer Styles
		 */

		$wp_customize->add_section( 'inspiry_footer_styles', array(
			'title' => __( 'Styles', 'framework' ),
			'panel' => 'inspiry_footer_panel',
		) );

		$wp_customize->add_setting( 'theme_footer_widget_title_color', array(
			'type' => 'option',
			'default' => '#394041',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_footer_widget_title_color',
				array(
					'label' => __( 'Footer Widget Title Color', 'framework' ),
					'section' => 'inspiry_footer_styles',
				)
			)
		);

		$wp_customize->add_setting( 'theme_footer_widget_text_color', array(
			'type' => 'option',
			'default' => '#8b9293',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_footer_widget_text_color',
				array(
					'label' => __( 'Footer Text Color', 'framework' ),
					'section' => 'inspiry_footer_styles',
				)
			)
		);

		$wp_customize->add_setting( 'theme_footer_widget_link_color', array(
			'type' => 'option',
			'default' => '#75797A',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_footer_widget_link_color',
				array(
					'label' => __( 'Footer Link Color', 'framework' ),
					'section' => 'inspiry_footer_styles',
				)
			)
		);

		$wp_customize->add_setting( 'theme_footer_widget_link_hover_color', array(
			'type' => 'option',
			'default' => '#dc7d44',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_footer_widget_link_hover_color',
				array(
					'label' => __( 'Footer Link Hover Color', 'framework' ),
					'section' => 'inspiry_footer_styles',
				)
			)
		);

		$wp_customize->add_setting( 'theme_footer_border_color', array(
			'type' => 'option',
			'default' => '#dedede',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'theme_footer_border_color',
				array(
					'label' => __( 'Footer Border Color', 'framework' ),
					'section' => 'inspiry_footer_styles',
				)
			)
		);

		$wp_customize->add_setting( 'theme_disable_footer_bg', array(
			'type' => 'option',
			'default' => 'false',
		) );
		$wp_customize->add_control( 'theme_disable_footer_bg', array(
			'label' => __( 'Do you want to disable footer background image ?', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_footer_styles',
			'choices' => array(
				'true' => __( 'Yes', 'framework' ),
				'false' => __( 'No', 'framework' ),
			)
		) );

		$wp_customize->add_setting( 'theme_footer_bg_img', array(
			'type' => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'theme_footer_bg_img',
				array(
					'label' => __( 'Footer Background Image', 'framework' ),
					"description" => __( 'Note: Default background image is 235px in height and 1770px in width.', 'framework' ),
					'section' => 'inspiry_footer_styles',
				)
			)
		);

	}

	add_action( 'customize_register', 'inspiry_footer_customizer' );
endif;
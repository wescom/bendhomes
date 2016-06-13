<?php
/**
 * Misc Customizer Settings
 */


if ( ! function_exists( 'inspiry_misc_customizer' ) ) :
	function inspiry_misc_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Misc Section
		 */

		$wp_customize->add_section( 'inspiry_misc_section', array(
			'title' => __( 'Misc', 'framework' ),
			'priority' => 130,
		) );

		/* Light Box Plugin */
		$wp_customize->add_setting( 'theme_lightbox_plugin', array(
			'type' => 'option',
			'default' => 'swipebox',
		) );
		$wp_customize->add_control( 'theme_lightbox_plugin', array(
			'label' => __( 'Lightbox Plugin', 'framework' ),
			"description" => __( 'Select the lightbox plugin that you want to use', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_misc_section',
			'choices' => array(
				'swipebox' => __( 'Swipebox Plugin', 'framework' ),
				'pretty-photo' => __( 'Pretty Photo Plugin', 'framework' )
			)
		) );

		/* Enable / Disable Recaptcha */
		$wp_customize->add_setting( 'theme_show_reCAPTCHA', array(
			'type' => 'option',
			'default' => 'false',
		) );
		$wp_customize->add_control( 'theme_show_reCAPTCHA', array(
			'label' => __( 'Google reCAPTCHA for Spam Protection', 'framework' ),
			'description' => __( 'Do you want to enable Google reCAPTCHA on contact forms ?', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_misc_section',
			'choices' => array(
				'true' => __( 'Yes', 'framework' ),
				'false' => __( 'No', 'framework' ),
			)
		) );

		/* reCAPTCHA Public Key */
		$wp_customize->add_setting( 'theme_recaptcha_public_key', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_recaptcha_public_key', array(
			'label' => __( 'Google reCAPTCHA Public Key', 'framework' ),
			"description" => __( 'Get reCAPTCHA public and private keys for your website by <a href="https://www.google.com/recaptcha/admin#whyrecaptcha" target="_blank">signing up here</a> ', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_misc_section',
		) );

		/* reCAPTCHA Private Key */
		$wp_customize->add_setting( 'theme_recaptcha_private_key', array(
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_recaptcha_private_key', array(
			'label' => __( 'Google reCAPTCHA Private Key', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_misc_section',
		) );

		/* Enable / Disable Map Localization */
		$wp_customize->add_setting( 'theme_map_localization', array(
			'type' => 'option',
			'default' => 'true',
		) );
		$wp_customize->add_control( 'theme_map_localization', array(
			'label' => __( 'Localize Google Map', 'framework' ),
			'type' => 'radio',
			'section' => 'inspiry_misc_section',
			'choices' => array(
				'true' => __( 'Yes', 'framework' ),
				'false' => __( 'No', 'framework' ),
			)
		) );

	}

	add_action( 'customize_register', 'inspiry_misc_customizer' );
endif;

<?php
/**
 * Blog/News Customizer
 */


if ( ! function_exists( 'inspiry_news_customizer' ) ) :
	function inspiry_news_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Pages Panel
		 */

		$wp_customize->add_panel( 'inspiry_various_pages', array(
			'title' => __( 'Various Pages', 'framework' ),
			'priority' => 126,
		) );

		/**
		 * News Section
		 */

		$wp_customize->add_section( 'inspiry_news_section', array(
			'title' => __( 'News/Blog Page', 'framework' ),
			'panel' => 'inspiry_various_pages',
		) );

		/* News Banner Title */
		$wp_customize->add_setting( 'theme_news_banner_title', array(
			'type' => 'option',
			"default" => __( 'News', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_news_banner_title', array(
			'label' => __( 'Banner Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_news_section',
		) );

		/* News Banner Sub Title */
		$wp_customize->add_setting( 'theme_news_banner_sub_title', array(
			'type' => 'option',
			"default" => __( 'Check out market updates', 'framework' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'theme_news_banner_sub_title', array(
			'label' => __( 'Banner Sub Title', 'framework' ),
			'type' => 'text',
			'section' => 'inspiry_news_section',
		) );


	}

	add_action( 'customize_register', 'inspiry_news_customizer' );
endif;
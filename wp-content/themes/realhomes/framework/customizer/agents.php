<?php
/**
 * Agents Customizer Settings
 */


if ( ! function_exists( 'inspiry_agents_customizer' ) ) :
	function inspiry_agents_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Agents Section
		 */

		$wp_customize->add_section( 'inspiry_agents_pages', array(
			'title' => __( 'Agents Pages', 'framework' ),
			'panel' => 'inspiry_various_pages',
		) );


		/* Number of Agents  */
		$wp_customize->add_setting( 'theme_number_posts_agent', array(
			'type' => 'option',
			'default' => '3',
		) );
		$wp_customize->add_control( 'theme_number_posts_agent', array(
			'label' => __( 'Number of Agents', 'framework' ),
			'description' => __( 'Select the maximum number of agents to display on an agents list page.', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_agents_pages',
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

		/* Number of Agents  */
		$wp_customize->add_setting( 'theme_number_of_properties_agent', array(
			'type' => 'option',
			'default' => '6',
		) );
		$wp_customize->add_control( 'theme_number_of_properties_agent', array(
			'label' => __( 'Number of Properties on Agent Detail Page', 'framework' ),
			'description' => __( 'Select the number of properties to display on Agent detail page.', 'framework' ),
			'type' => 'select',
			'section' => 'inspiry_agents_pages',
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

	}

	add_action( 'customize_register', 'inspiry_agents_customizer' );
endif;
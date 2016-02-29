<?php
/**
 * Customizer
 */

/**
 * Load custom controls
 */
if ( ! function_exists( 'inspiry_load_customize_controls' ) ) :
	function inspiry_load_customize_controls() {
		require_once( INSPIRY_FRAMEWORK . 'customizer/custom/control-multiple-checkbox.php' );
	}

	add_action( 'customize_register', 'inspiry_load_customize_controls', 0 );
endif;


/**
 * Header Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/header.php' );


/**
 * Home Page Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/home.php' );


/**
 * Properties Search Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/search.php' );


/**
 * Price Format Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/price-format.php' );


/**
 * Currency Switcher Settings
 * only if wp-currencies plugins is active
 */
if ( class_exists( 'WP_Currencies' ) ) {
	require_once( INSPIRY_FRAMEWORK . 'customizer/currency-switcher.php' );
}


/**
 * Property Detail Page Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/property.php' );


/**
 * Blog/News Page Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/news.php' );


/**
 * Gallery Page Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/gallery.php' );


/**
 * Agents Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/agents.php' );


/**
 * Contact Page Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/contact.php' );


/**
 * Properties List and Taxonomy Archive Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/list-and-taxonomy.php' );


/**
 * Misc Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/misc.php' );


/**
 * Footer Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/footer.php' );


/**
 * Members Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/members.php' );


/**
 * Payments Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/payments.php' );


/**
 * Styles Settings
 */
require_once( INSPIRY_FRAMEWORK . 'customizer/styles.php' );
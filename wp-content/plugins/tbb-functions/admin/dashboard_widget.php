<?php
// Dashboard widget for Wescom Landing Pages Site

require_once( TBB_FUNCTIONS_DIR .'/admin/custom_widgets.php' );

// Initialize custom dashboard widgets
class tbb_Dashboard_Widgets {
 
    function __construct() {
        add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
    }
 
    function remove_dashboard_widgets() {
 		global $remove_defaults_widgets;
 
		foreach ( $remove_defaults_widgets as $widget_id => $options ) {
			remove_meta_box( $widget_id, $options['page'], $options['context'] );
		}
    }
 
 	// Uncomment this line to add Wescom dashboard widget built in /admin/custom_widgets.php
    /*function add_dashboard_widgets() {
 		global $custom_dashboard_widgets;
 
		foreach ( $custom_dashboard_widgets as $widget_id => $options ) {
			wp_add_dashboard_widget(
				$widget_id,
				$options['title'],
				$options['callback']
			);
		}
    }*/
 
}
 
$wdw = new tbb_Dashboard_Widgets();
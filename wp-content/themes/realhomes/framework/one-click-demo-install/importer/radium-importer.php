<?php

/**
 * Class Radium_Theme_Importer
 *
 * This class provides the capability to import demo content as well as import widgets and WordPress menus
 *
 * @since 0.0.2
 *
 * @category RadiumFramework
 * @package  NewsCore WP
 * @author   Franklin M Gitonga
 * @link     http://radiumthemes.com/
 *
 */

 // Exit if accessed directly
 if ( !defined( 'ABSPATH' ) ) exit;

 // Don't duplicate me!
 if ( !class_exists( 'Radium_Theme_Importer' ) ) {

	class Radium_Theme_Importer {

		/**
		 * Set the theme framework in use
		 *
		 * @since 0.0.3
		 *
		 * @var object
		 */
		public $theme_options_framework = 'radium'; //supports radium framework and option tree

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		public $theme_options_file;

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		public $widgets;

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		public $content_demo;

		/**
		 * Flag imported to prevent duplicates
		 *
		 * @since 0.0.3
		 *
		 * @var array
		 */
		public $flag_as_imported = array( 'content' => false, 'menus' => false, 'options' => false, 'widgets' =>false );

		/**
		 * imported sections to prevent duplicates
		 *
		 * @since 0.0.3
		 *
		 * @var array
		 */
		public $imported_demos = array();

		/**
		 * Flag imported to prevent duplicates
		 *
		 * @since 0.0.3
		 *
		 * @var bool
		 */
		public $add_admin_menu = true;

	    /**
	     * Holds a copy of the object for easy reference.
	     *
	     * @since 0.0.2
	     *
	     * @var object
	     */
	    private static $instance;

	    /**
	     * Constructor. Hooks all interactions to initialize the class.
	     *
	     * @since 0.0.2
	     */
	    public function __construct() {

	        self::$instance = $this;

	        $this->demo_files_path 		= apply_filters('radium_theme_importer_demo_files_path', $this->demo_files_path);

	        $this->theme_options_file 	= apply_filters('radium_theme_importer_theme_options_file', $this->demo_files_path . $this->theme_options_file_name);

	        $this->widgets 				= apply_filters('radium_theme_importer_widgets_file', $this->demo_files_path . $this->widgets_file_name);

	        $this->content_demo 		= apply_filters('radium_theme_importer_content_demo_file', $this->demo_files_path . $this->content_demo_file_name);

			$this->imported_demos = get_option( 'radium_imported_demo' );

            if( $this->theme_options_framework == 'optiontree' ) {
	            if ( function_exists( 'ot_options_id' ) ) {
		            $this->theme_option_name = ot_options_id();
	            }
            }

	        if( $this->add_admin_menu ) add_action( 'admin_menu', array($this, 'add_admin') );

			add_filter( 'add_post_metadata', array( $this, 'check_previous_meta' ), 10, 5 );

      		add_action( 'radium_import_end', array( $this, 'after_wp_importer' ) );

		    /**
		     * Inspiry Demo Import Styles & Scripts
		     */
		    add_action( 'admin_enqueue_scripts', array( $this, 'inspiry_demo_import_styles' ) );
		    add_action( 'admin_enqueue_scripts', array( $this, 'inspiry_demo_import_js' ) );

		    /**
		     * Import Ajax Request Handler
		     */
		    add_action( 'wp_ajax_inspiry_demo_import', array( $this, 'inspiry_import_handler' ) );
	    }

		/**
		 * Add Panel Page
		 *
		 * @since 0.0.2
		 */
	    public function add_admin() {
	        add_submenu_page('themes.php', "Import Demo Data", "Import Demo Data", 'switch_themes', 'radium_demo_installer', array($this, 'demo_installer'));
	    }


		/**
		 * Styles required for demo import page
		 */
		function inspiry_demo_import_styles( $hook ) {
			if( $hook == 'appearance_page_radium_demo_installer' ) {
				wp_enqueue_style( 'inspiry-demo-import-styles', get_template_directory_uri() . '/framework/one-click-demo-install/inspiry-demo-import.css' );
			}
		}

		/**
		 * JavaScript required for demo import page
		 */
		function inspiry_demo_import_js( $hook ) {
			if( $hook == 'appearance_page_radium_demo_installer' ) {

				// jQuery Validate
				wp_enqueue_script(
					'jqvalidate',
					get_template_directory_uri() . '/js/jquery.validate.min.js',
					array( 'jquery' ),
					'1.11.1',
					false
				);

				// jQuery Form
				wp_enqueue_script(
					'jqform',
					get_template_directory_uri() . '/js/jquery.form.js',
					array( 'jquery' ),
					'3.40',
					false
				);

				wp_enqueue_script(
					'inspiry-demo-import-js',
					get_template_directory_uri() . '/framework/one-click-demo-install/inspiry-demo-import.js',
					array( 'jquery' ),
					INSPIRY_THEME_VERSION,
					false
				);

			}
		}

	    /**
         * Avoids adding duplicate meta causing arrays in arrays from WP_importer
         *
         * @param null    $continue
         * @param unknown $post_id
         * @param unknown $meta_key
         * @param unknown $meta_value
         * @param unknown $unique
         *
         * @since 0.0.2
         *
         * @return
         */
        public function check_previous_meta( $continue, $post_id, $meta_key, $meta_value, $unique ) {

			$old_value = get_metadata( 'post', $post_id, $meta_key );

			if ( count( $old_value ) == 1 ) {

				if ( $old_value[0] === $meta_value ) {

					return false;

				} elseif ( $old_value[0] !== $meta_value ) {

					update_post_meta( $post_id, $meta_key, $meta_value );
					return false;

				}

			}

    	}

    	/**
    	 * Add Panel Page
    	 *
    	 * @since 0.0.2
    	 */
    	public function after_wp_importer() {

			do_action( 'radium_importer_after_content_import');

			update_option( 'radium_imported_demo', $this->flag_as_imported );

		}

		/**
		 * Generates User Interface HTML
		 */
    	public function intro_html() {

    	}

	    /**
	     * demo_installer Output
	     *
	     */
	    public function demo_installer() {

		    if ( ! empty( $this->imported_demos ) ) {
			    $button_text = __( 'Import Again', 'inspiry' );
		    } else {
			    $button_text = __( 'Import Demo Data', 'inspiry' );
		    }
	        ?>

		    <div class="wrap">

			<div class="welcome-panel">

				<div class="welcome-panel-content">

					<form id="demo-import-form" class="demo-import-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">

						<h1>One Click Demo Import</h1>

						<p class="about-description">
							Importing demo data is the easiest way to setup with your theme.
							<br>
							When you import the data, Following things will happen:
						</p>

						<ol>
							<li>No existing data ( posts, pages, categories, images, custom post types ) will be deleted or modified.</li>
							<li>Few WordPress settings can be modified to setup front page and news page.</li>
							<li>New data ( posts, pages, custom post types, some images, widgets and menus ) will be imported.</li>
							<li>Demo images will be downloaded from our server, These images are copyrighted and are only for demo use.</li>
							<li>Click import button only once and wait, Import process can take few minutes to complete.</li>
						</ol>
						<?php
						if ( ! empty( $this->imported_demos ) ) {
							?>
							<div class="already-imported">
								<p><?php _e('Note: Demo is already imported', 'inspiry'); ?></p>
							</div>
							<?php
						}
						?>
						<?php wp_nonce_field( 'inspiry-demo-import-nonce', 'inspiry-secure-import' ); ?>
						<input type="hidden" name="action" value="inspiry_demo_import" />
						<input id="import-demo" class="button button-primary" name="reset" type="submit" value="<?php echo $button_text ; ?>" />
						<img id="import-loader" src="<?php echo get_template_directory_uri(); ?>/images/spinner-2x.gif" height="20px" width="20px" alt="Working...">
						<br />
						<br />
						<div id="import-message"></div>
						<div id="import-error"></div>
					</form>

				</div>

			</div>

		    </div>
		    <?php

	    }


	    function inspiry_import_handler() {

		    // First check the nonce, if it fails the function will break
		    check_ajax_referer( 'inspiry-demo-import-nonce', 'inspiry-secure-import' );

		    $this->process_imports();

		    die();
	    }

	    /**
	     * Process all imports
	     *
	     * @params $content
	     * @params $options
	     * @params $options
	     * @params $widgets
	     *
	     * @since 0.0.3
	     *
	     * @return null
	     */
	    public function process_imports( $content = true, $options = true, $widgets = true) {

			if ( $content && !empty( $this->content_demo ) && is_file( $this->content_demo ) ) {
				$this->set_demo_data( $this->content_demo );
			}

			if ( $options && !empty( $this->theme_options_file ) && is_file( $this->theme_options_file ) ) {
				$this->set_demo_theme_options( $this->theme_options_file );
			}

			if ( $options ) {
				$this->set_demo_menus();
			}

			if ( $widgets && !empty( $this->widgets ) && is_file( $this->widgets ) ) {
				$this->process_widget_import_file( $this->widgets );
			}

			do_action( 'radium_import_end');

        }

	    /**
	     * add_widget_to_sidebar Import sidebars
	     * @param  string $sidebar_slug    Sidebar slug to add widget
	     * @param  string $widget_slug     Widget slug
	     * @param  string $count_mod       position in sidebar
	     * @param  array  $widget_settings widget settings
	     *
	     * @since 0.0.2
	     *
	     * @return null
	     */
	    public function add_widget_to_sidebar($sidebar_slug, $widget_slug, $count_mod, $widget_settings = array()){

	        $sidebars_widgets = get_option('sidebars_widgets');

	        if(!isset($sidebars_widgets[$sidebar_slug]))
	           $sidebars_widgets[$sidebar_slug] = array('_multiwidget' => 1);

	        $newWidget = get_option('widget_'.$widget_slug);

	        if(!is_array($newWidget))
	            $newWidget = array();

	        $count = count($newWidget)+1+$count_mod;
	        $sidebars_widgets[$sidebar_slug][] = $widget_slug.'-'.$count;

	        $newWidget[$count] = $widget_settings;

	        update_option('sidebars_widgets', $sidebars_widgets);
	        update_option('widget_'.$widget_slug, $newWidget);

	    }

	    public function set_demo_data( $file ) {

		    if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

	        require_once ABSPATH . 'wp-admin/includes/import.php';

	        $importer_error = false;

	        if ( !class_exists( 'WP_Importer' ) ) {
	            $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	            if ( file_exists( $class_wp_importer ) ){
	                require_once( $class_wp_importer );
	            } else {
	                $importer_error = true;
	            }
	        }

	        if ( !class_exists( 'WP_Import' ) ) {
	            $class_wp_import = dirname( __FILE__ ) .'/wordpress-importer.php';
	            if ( file_exists( $class_wp_import ) ) {
		            require_once( $class_wp_import );
	            } else {
		            $importer_error = true;
	            }
	        }

		    if ( $importer_error ) {
			    die( "Error on import" );
		    } else {
			    if ( ! is_file( $file ) ) {
				    echo "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually ";
			    } else {
				    $wp_import = new WP_Import();
				    $wp_import->fetch_attachments = true;
				    $wp_import->import( $file );
				    $this->flag_as_imported[ 'content' ] = true;
			    }
		    }

		    do_action( 'radium_importer_after_theme_content_import' );

	    }

	    public function set_demo_menus() {}

	    public function set_demo_theme_options( $file ) {

	    	// Does the File exist?
			if ( file_exists( $file ) ) {

				// Get file contents and decode
				$data = file_get_contents( $file );

				if( $this->theme_options_framework == 'radium') {

					//radium framework
					$data = unserialize( trim($data, '###') );

				} elseif( $this->theme_options_framework == 'optiontree' ) {

					//option tree import
					$data = $this->optiontree_decode($data);

					if ( function_exists( 'ot_options_id' ) ) {
						update_option( ot_options_id(), $data );
					}
					
					$this->flag_as_imported['options'] = true;

				} else {

					//other frameworks
					//$data = json_decode( $data, true );
					$data = maybe_unserialize( $data );

				}

				// Only if there is data
				if ( !empty( $data ) || is_array( $data ) ) {

					// Hook before import
					$data = apply_filters( 'radium_theme_import_theme_options', $data );

					update_option( $this->theme_option_name, $data );

					$this->flag_as_imported['options'] = true;
				}

	      		do_action( 'radium_importer_after_theme_options_import', $this->demo_files_path );

      		} else {

	      		wp_die(
      				__( 'Theme options Import file could not be found. Please try again.', 'inspiry' ),
      				'',
      				array( 'back_link' => true )
      			);
       		}

	    }

	    /**
	     * Available widgets
	     *
	     * Gather site's widgets into array with ID base, name, etc.
	     * Used by export and import functions.
	     *
	     * @since 0.0.2
	     *
	     * @global array $wp_registered_widget_updates
	     * @return array Widget information
	     */
	    function available_widgets() {

	    	global $wp_registered_widget_controls;

	    	$widget_controls = $wp_registered_widget_controls;

	    	$available_widgets = array();

	    	foreach ( $widget_controls as $widget ) {

	    		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

	    			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
	    			$available_widgets[$widget['id_base']]['name'] = $widget['name'];

	    		}

	    	}

	    	return apply_filters( 'radium_theme_import_widget_available_widgets', $available_widgets );

	    }


	    /**
	     * Process import file
	     *
	     * This parses a file and triggers importation of its widgets.
	     *
	     * @since 0.0.2
	     *
	     * @param string $file Path to .wie file uploaded
	     * @global string $widget_import_results
	     */
	    function process_widget_import_file( $file ) {

	    	// File exists?
	    	if ( ! file_exists( $file ) ) {
	    		wp_die(
	    			__( 'Widget Import file could not be found. Please try again.', 'inspiry' ),
	    			'',
	    			array( 'back_link' => true )
	    		);
	    	}

	    	// Get file contents and decode
	    	$data = file_get_contents( $file );
	    	$data = json_decode( $data );

	    	// Delete import file
	    	//unlink( $file );

	    	// Import the widget data
	    	// Make results available for display on import/export page
	    	$this->widget_import_results = $this->import_widgets( $data );

	    }


	    /**
	     * Import widget JSON data
	     *
	     * @since 0.0.2
	     * @global array $wp_registered_sidebars
	     * @param object $data JSON widget data from .json file
	     * @return array Results array
	     */
	    public function import_widgets( $data ) {

	    	global $wp_registered_sidebars;

	    	// Have valid data?
	    	// If no data or could not decode
	    	if ( empty( $data ) || ! is_object( $data ) ) {
	    		return;
	    	}

	    	// Hook before import
	    	$data = apply_filters( 'radium_theme_import_widget_data', $data );

	    	// Get all available widgets site supports
	    	$available_widgets = $this->available_widgets();

	    	// Get all existing widget instances
	    	$widget_instances = array();
	    	foreach ( $available_widgets as $widget_data ) {
	    		$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
	    	}

	    	// Begin results
	    	$results = array();

	    	// Loop import data's sidebars
	    	foreach ( $data as $sidebar_id => $widgets ) {

	    		// Skip inactive widgets
	    		// (should not be in export file)
	    		if ( 'wp_inactive_widgets' == $sidebar_id ) {
	    			continue;
	    		}

	    		// Check if sidebar is available on this site
	    		// Otherwise add widgets to inactive, and say so
	    		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
	    			$sidebar_available = true;
	    			$use_sidebar_id = $sidebar_id;
	    			$sidebar_message_type = 'success';
	    			$sidebar_message = '';
	    		} else {
	    			$sidebar_available = false;
	    			$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
	    			$sidebar_message_type = 'error';
	    			$sidebar_message = __( 'Sidebar does not exist in theme (using Inactive)', 'inspiry' );
	    		}

	    		// Result for sidebar
	    		$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
	    		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
	    		$results[$sidebar_id]['message'] = $sidebar_message;
	    		$results[$sidebar_id]['widgets'] = array();

	    		// Loop widgets
	    		foreach ( $widgets as $widget_instance_id => $widget ) {

	    			$fail = false;

	    			// Get id_base (remove -# from end) and instance ID number
	    			$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
	    			$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

	    			// Does site support this widget?
	    			if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
	    				$fail = true;
	    				$widget_message_type = 'error';
	    				$widget_message = __( 'Site does not support widget', 'inspiry' ); // explain why widget not imported
	    			}

	    			// Filter to modify settings before import
	    			// Do before identical check because changes may make it identical to end result (such as URL replacements)
	    			$widget = apply_filters( 'radium_theme_import_widget_settings', $widget );

	    			// Does widget with identical settings already exist in same sidebar?
	    			if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

	    				// Get existing widgets in this sidebar
	    				$sidebars_widgets = get_option( 'sidebars_widgets' );
	    				$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

	    				// Loop widgets with ID base
	    				$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
	    				foreach ( $single_widget_instances as $check_id => $check_widget ) {

	    					// Is widget in same sidebar and has identical settings?
	    					if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

	    						$fail = true;
	    						$widget_message_type = 'warning';
	    						$widget_message = __( 'Widget already exists', 'inspiry' ); // explain why widget not imported

	    						break;

	    					}

	    				}

	    			}

	    			// No failure
	    			if ( ! $fail ) {

	    				// Add widget instance
	    				$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
	    				$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
	    				$single_widget_instances[] = (array) $widget; // add it

    					// Get the key it was given
    					end( $single_widget_instances );
    					$new_instance_id_number = key( $single_widget_instances );

    					// If key is 0, make it 1
    					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
    					if ( '0' === strval( $new_instance_id_number ) ) {
    						$new_instance_id_number = 1;
    						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
    						unset( $single_widget_instances[0] );
    					}

    					// Move _multiwidget to end of array for uniformity
    					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
    						$multiwidget = $single_widget_instances['_multiwidget'];
    						unset( $single_widget_instances['_multiwidget'] );
    						$single_widget_instances['_multiwidget'] = $multiwidget;
    					}

    					// Update option with new widget
    					update_option( 'widget_' . $id_base, $single_widget_instances );

	    				// Assign widget instance to sidebar
	    				$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
	    				$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
	    				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
	    				update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

	    				// Success message
	    				if ( $sidebar_available ) {
	    					$widget_message_type = 'success';
	    					$widget_message = __( 'Imported', 'inspiry' );
	    				} else {
	    					$widget_message_type = 'warning';
	    					$widget_message = __( 'Imported to Inactive', 'inspiry' );
	    				}

	    			}

	    			// Result for widget instance
	    			$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
	    			$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = $widget->title ? $widget->title : __( 'No Title', 'inspiry' ); // show "No Title" if widget instance is untitled
	    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
	    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;

	    		}

	    	}

			$this->flag_as_imported['widgets'] = true;

	    	// Hook after import
	    	do_action( 'radium_theme_import_widget_after_import' );

	    	// Return results
	    	return apply_filters( 'radium_theme_import_widget_results', $results );

	    }

	    /**
	     * Helper function to return option tree decoded strings
	     *
	     * @return    string
	     *
	     * @access    public
	     * @since     0.0.3
	     * @updated   0.0.3.1
	     */
	    public function optiontree_decode( $value ) {
			
			$func = 'base64' . '_decode';
			$prepared_data = maybe_unserialize( $func( $value ) );
			
			return $prepared_data;

	    }

	}//class

}//function_exists
?>

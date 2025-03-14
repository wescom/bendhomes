<?php
/**
 * Plugin Name: Meta Box Tabs
 * Plugin URI: https://metabox.io/plugins/meta-box-tabs/
 * Description: Create tabs for meta boxes easily. Support 3 WordPress-native tab styles.
 * Version: 0.1.7
 * Author: Rilwis
 * Author URI: https://www.deluxeblogtips.com
 * License: GPL2+
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class
 * @package    Meta Box
 * @subpackage Meta Box Tabs
 * @author     Tran Ngoc Tuan Anh <rilwis@gmail.com>
 */
class MB_Tabs
{
	/**
	 * Indicate that the instance of the class is working on a meta box that has tabs or not
	 * It will be set 'true' BEFORE meta box is display and 'false' AFTER
	 *
	 * @var bool
	 */
	public $active = false;

	/**
	 * Store all output of fields
	 * This is used to put fields in correct <div> for tabs
	 * The fields' output will be get via filter 'rwmb_outer_html'
	 *
	 * @var array
	 */
	public $fields_output = array();

	/**
	 * Add hooks to meta box
	 */
	public function __construct()
	{
		add_action( 'rwmb_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'rwmb_before', array( $this, 'opening_div' ), 1 ); // 1 = display first, before tab nav
		add_action( 'rwmb_after', array( $this, 'closing_div' ), 100 ); // 100 = display last, after tab panels

		add_action( 'rwmb_before', array( $this, 'show_nav' ) );
		add_action( 'rwmb_after', array( $this, 'show_panels' ) );

		add_filter( 'rwmb_outer_html', array( $this, 'capture_fields' ), 10, 2 );
	}

	/**
	 * Enqueue scripts and styles for tabs
	 */
	public function admin_enqueue_scripts()
	{
		//wp_enqueue_style( 'rwmb-tabs', plugins_url( 'tabs.css', __FILE__ ) );
		wp_enqueue_style( 'rwmb-tabs', get_template_directory_uri() . '/framework/meta-box/extensions/meta-box-tabs/tabs.css' );
		//wp_enqueue_script( 'rwmb-tabs', plugins_url( 'tabs.js', __FILE__ ), array( 'jquery' ), '0.1', true );
		wp_enqueue_script( 'rwmb-tabs', get_template_directory_uri() . '/framework/meta-box/extensions/meta-box-tabs/tabs.js', array( 'jquery' ), '0.1', true );
	}

	/**
	 * Display opening div for tabs for meta box
	 *
	 * @param RW_Meta_Box $obj Meta Box object
	 */
	public function opening_div( RW_Meta_Box $obj )
	{
		if ( empty( $obj->meta_box['tabs'] ) )
			return;

		$class = 'rwmb-tabs';
		if ( isset( $obj->meta_box['tab_style'] ) && 'default' != $obj->meta_box['tab_style'] )
			$class .= ' rwmb-tabs-' . $obj->meta_box['tab_style'];

		if ( isset( $obj->meta_box['tab_wrapper'] ) && false == $obj->meta_box['tab_wrapper'] )
			$class .= ' rwmb-tabs-no-wrapper';

		echo '<div class="' . $class . '">';

		// Set 'true' to let us know that we're working on a meta box that has tabs
		$this->active = true;
	}

	/**
	 * Display closing div for tabs for meta box
	 */
	public function closing_div()
	{
		if ( ! $this->active )
			return;

		echo '</div>';

		// Reset to initial state to be ready for other meta boxes
		$this->active        = false;
		$this->fields_output = array();
	}

	/**
	 * Display tab navigation for meta box
	 *
	 * @param RW_Meta_Box $obj Meta Box object
	 */
	public function show_nav( RW_Meta_Box $obj )
	{
		if ( ! $this->active )
			return;

		$tabs = $obj->meta_box['tabs'];

		echo '<ul class="rwmb-tab-nav">';

		$i = 0;
		foreach ( $tabs as $key => $tab_data )
		{
			if ( is_string( $tab_data ) )
			{
				$tab_data = array( 'label' => $tab_data );
			}
			$tab_data = wp_parse_args( $tab_data, array(
				'icon'  => '',
				'label' => '',
			) );
			// If icon is URL to image
			if ( filter_var( $tab_data['icon'], FILTER_VALIDATE_URL ) )
			{
				$icon = '<img src="' . $tab_data['icon'] . '">';
			}
			// If icon is icon font
			else
			{
				// If icon is dashicon, auto add class 'dashicons' for users
				if ( false !== strpos( $tab_data['icon'], 'dashicons' ) )
				{
					$tab_data['icon'] .= ' dashicons';
				}
				// Remove duplicate classes
				$tab_data['icon'] = array_filter( array_map( 'trim', explode( ' ', $tab_data['icon'] ) ) );
				$tab_data['icon'] = implode( ' ', array_unique( $tab_data['icon'] ) );

				$icon = $tab_data['icon'] ? '<i class="' . $tab_data['icon'] . '"></i>' : '';
			}

			$class = "rwmb-tab-$key";
			if ( ! $i )
				$class .= ' rwmb-tab-active';

			printf(
				'<li class="%s" data-panel="%s"><a href="#">%s%s</a></li>',
				$class,
				$key,
				$icon,
				$tab_data['label']
			);
			$i ++;
		}

		echo '</ul>';
	}

	/**
	 * Display tab navigation for meta box
	 * Note that: this public function is hooked to 'rwmb_after', when all fields are outputted
	 * (and captured by 'capture_fields' public function)
	 */
	public function show_panels()
	{
		if ( ! $this->active )
			return;

		echo '<div class="rwmb-tab-panels">';
		foreach ( $this->fields_output as $tab => $fields )
		{
			echo '<div class="rwmb-tab-panel rwmb-tab-panel-' . $tab . '">';
			echo implode( '', $fields );
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Save field output into class variable to output later
	 *
	 * @param string $output Field output
	 * @param array  $field  Field configuration
	 *
	 * @return string
	 */
	public function capture_fields( $output, $field )
	{
		// If meta box doesn't have tabs, do nothing
		if ( ! $this->active || ! isset( $field['tab'] ) )
			return $output;

		$tab = $field['tab'];

		if ( ! isset( $this->fields_output[$tab] ) )
			$this->fields_output[$tab] = array();
		$this->fields_output[$tab][] = $output;

		// Return empty string to let Meta Box plugin echoes nothing
		return '';
	}
}

if ( is_admin() )
{
	new MB_Tabs;
}

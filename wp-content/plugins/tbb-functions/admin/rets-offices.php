<?php
// Offices admin page

class RETS_Featured_Offices {
	
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_office_files' ) );
		//add_action( 'admin_action_offices', array( $this, 'admin_action' ) );
	}

	function admin_menu() {
		add_menu_page(
			'Featured Offices',
			'Offices',
			'manage_options',
			'rets-offices',
			array( $this, 'render_page' ),
			'dashicons-building',
			'20'
		);
	}
	
	function enqueue_office_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . 'css/company-settings.css' );
	}
	
	/*function admin_action() {
		//print_r($_POST);
		exit();
	}*/
	
	public function render_page() {
		
        $html = '<div class="wrap tbb-company-page">';
			$html .= '<h1>RETS Featured Offices Testing1</h1>';
			/*ob_start();
			include_once( TBB_FUNCTIONS_DIR .'/admin/rets-offices-page.php' );
			$html .= ob_get_contents();
			ob_end_clean();*/
        $html .= '</div>';
		
		echo $html;
        
	}
	
}

new RETS_Featured_Offices;
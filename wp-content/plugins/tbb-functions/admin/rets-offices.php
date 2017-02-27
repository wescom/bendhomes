<?php
// Offices admin page

class RETS_Featured_Offices {
	
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_office_files' ) );
		add_action( 'admin_action_offices', array( $this, 'admin_action' ) );
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
	
	function admin_action() {
		//print_r($_POST);
		exit();
	}
	
	abstract function render_page();

	/*public function do_page() { ?>
		
        <div class="wrap tbb-company-page">
        	<h1>Featured Offices</h1> 
            
            <div class="company-wrap">
            
                	<h2 class="nav-tab-wrapper" id="tbb-company-tabs">
                    	<a class="nav-tab nav-tab-active" id="tbb-company-tab" href="#top#company">Offices</a>
                    </h2>
                    
                    <div id="sections">
                        <section id="company" class="tbb-tab active">
                            <form id="create-companies" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
                                                                                            
                            <p>
                                <input type="hidden" name="action" value="companies_created" />
                                <input id="company-submit" class="button-primary" type="submit" value="<?php _e( 'Update', 'tbb_company' ); ?>" />
                            </p>
                            </form>
                        </section>
                    </div>
            
            </div>
        </div>
        
	<?php }*/
	
}

new RETS_Featured_Offices;
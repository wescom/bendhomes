<?php
// Settings page under Company Post Type

include( TBB_FUNCTIONS_DIR . 'rets-connect.class.php' );

class RETS_Featured_Companies {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_company_files' ) );
		add_action( 'admin_action_offices', array( $this, 'offices_admin_action' ) );
	}

	function admin_menu() {
		add_menu_page(
			'Featured Offices',
			'Offices',
			'manage_options',
			'offices',
			array(
				$this,
				'office_settings_do_page'
			),
			'dashicons-building',
			'20'
		);
	}
	
	function enqueue_company_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . 'css/company-settings.css' );
	}
	
	function offices_admin_action() {
		// Do posting function here that creates/updates the companies.
		//$this->create_company_posts();
	
		wp_redirect( $_SERVER['HTTP_REFERER'] .'&companies-created=true' );
		//print_r($_POST);
		exit();
	}
	
	function call_RETS_DB_select( $query ) {
		$rets_db = new Rets_DB();
		$select = $rets_db->select( $query );
		return $select;
	}

	function office_settings_do_page() { 
		
		$query = "
			SELECT Office_OFFI.IsActive,
			Office_OFFI.MLSID,
			Office_OFFI.OfficeName,
			Office_OFFI.OfficeDescription,
			Office_OFFI.DisplayName,
			Office_OFFI.featured,
			FROM Office_OFFI
			WHERE IsActive = 'T'
			ORDER BY OfficeName ASC
		";
		
	?>
		
        <div class="wrap tbb-company-page">
        	<h1>Featured Offices</h1>
            
            <?php if ( $_GET['companies-created'] == 'true' ) { ?>
                <div class="updated">
                    <p>Companies Created/Updated Successfully</p>
                </div>
            <?php } ?> 
            
            <div class="company-wrap">
            
                	<h2 class="nav-tab-wrapper" id="tbb-company-tabs">
                    	<a class="nav-tab nav-tab-active" id="tbb-company-tab" href="#top#company">Offices</a>
                    </h2>
                    
                    <div id="sections">
                        <section id="company" class="tbb-tab active">
                            <form id="create-companies" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
                            
                            <?php print_r( $this->call_RETS_DB_select( $query ) ); ?>
                            
                            <p>
                                <input type="hidden" name="action" value="companies_created" />
                                <input id="company-submit" class="button-primary" type="submit" value="<?php _e( 'Update', 'tbb_company' ); ?>" />
                            </p>
                            </form>
                        </section>
                    </div>
            
            </div>
        </div>
        
	<?php }
	
}

new RETS_Featured_Companies;
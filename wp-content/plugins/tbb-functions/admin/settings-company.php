<?php
// Settings page under Company Post Type

class CompanySettingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_company_files' ) );
		add_action( 'admin_action_companies_created', array( $this, 'companies_created_admin_action' ) );
	}

	function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=company',
			'Company Settings',
			'Settings',
			'manage_options',
			'company-settings',
			array(
				$this,
				'company_settings_do_page'
			)
		);
	}
	
	function enqueue_company_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . 'css/company-settings.css' );
	}
	
	function companies_created_admin_action() {
		// Do posting function here that creates the companies.
		$this->create_company_posts();
	
		wp_redirect( $_SERVER['HTTP_REFERER'] .'&companies-created="true' );
		print_r($_POST);
		exit();
	}

	function company_settings_do_page() { ?>
		
        <div class="wrap tbb-company-page">
        	<h1>Company Settings</h1>
            
            <?php if ( $_GET['companies-created'] == 'true' ) { ?>
                <div class="updated">
                    <h3>Companies Created</h3>
                </div>
            <?php } ?> 
            
            <div class="company-wrap">
            
                <form method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
                	<h2 class="nav-tab-wrapper" id="tbb-company-tabs">
                    	<a class="nav-tab nav-tab-active" id="tbb-company-tab" href="#top#company">Companies</a>
                        <a class="nav-tab" id="tbb-shortcodes-tab" href="#top#shortcodes">Shortcodes</a>
                    </h2>
                    
                    <div id="sections">
                        <section id="company" class="tbb-tab active">
                            <h3>Companies Settings</h3>
                            <p>Create companies imported from Agents meta fields. To create company posts click the button below.</p>
                            <p>
                                <input type="hidden" name="action" value="companies_created" />
                                <input class="button-primary" type="submit" value="<?php _e( 'Create Companies', 'tbb_company' ); ?>" />
                            </p>
                        </section>
                        
                        <section id="shortcodes" class="tbb-tab">
                            <h3>Shortcodes</h3>
                        </section>
                    </div>
                </form>
            
            </div>
        </div>
        
        <script type="text/javascript">
			(function($) {
				$(document).on( 'click', '.nav-tab-wrapper a', function() {
					$('section').hide();
					$('section').eq($(this).index()).show();
					return false;
				})
			})( jQuery );
		</script>
        
	<?php }
	
	function create_company_posts() {
		
	}
}

new CompanySettingsPage;
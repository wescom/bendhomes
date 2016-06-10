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
                    <p>Companies Created Successfully</p>
                </div>
            <?php } ?> 
            
            <div class="company-wrap">
            
                <form id="create-companies" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
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
                                <input id="company-submit" class="button-primary" type="submit" value="<?php _e( 'Create Companies', 'tbb_company' ); ?>" />
                            </p>
                        </section>
                        
                        <section id="shortcodes" class="tbb-tab">
                            <h3>Shortcodes</h3>
                            <p>This section displays a list of available shortcodes to use with the Company post type to display companies on the website.</p>
                            <p><strong>Shortcode: [BH_CUSTOM_POSTS]</strong></p>
                        </section>
                    </div>
                </form>
            
            </div>
        </div>
        <?php /* Unminified JS
        <script type="text/javascript">
			(function($) {
				$(document).on( 'click', '.nav-tab-wrapper a', function() {
					$('section').hide().removeClass('active');
					$('.nav-tab-wrapper a').removeClass('nav-tab-active');
					$('.nav-tab-wrapper a').eq($(this).index()).addClass('nav-tab-active');
					$('section').eq($(this).index()).show().addClass('active');
					return false;
				})
				$('#company-submit').click(function(e) {	
					e.preventDefault();
					$(this).attr("disabled","disabled");
					if( confirm("If you're sure, click OK to continue") ) {
						$("#create-companies").submit();
						$("#company-submit").after('<span class="holdon">Please hold, we\'re creating your companies.</span>');
					} else {
						$(this).removeAttr("disabled");	
					}
				});
			})( jQuery );
		</script> */ ?>
        <script type="text/javascript">
		!function(a){a(document).on("click",".nav-tab-wrapper a",function(){return a("section").hide().removeClass("active"),a(".nav-tab-wrapper a").removeClass("nav-tab-active"),a(".nav-tab-wrapper a").eq(a(this).index()).addClass("nav-tab-active"),a("section").eq(a(this).index()).show().addClass("active"),!1}),a("#company-submit").click(function(e){e.preventDefault(),a(this).attr("disabled","disabled"),confirm("If you're sure, click OK to continue")?(a("#create-companies").submit(),a("#company-submit").after('<span class="holdon">Please hold, we\'re creating your companies.</span>')):a(this).removeAttr("disabled")})}(jQuery);
		</script>
	<?php }
	
	function create_company_posts() {
		$args = array(
			'post_type' => 'agent',
			'posts_per_page' => '-1'
		);
		
		$agents = new WP_Query( $args );
		
		if ( $agents->have_posts() ) :	
			while ( $agents->have_posts() ) : $agents->the_post();
				
				$company_name = get_field( 'brk_office_name' );
				$company_phone = get_field( 'brk_office_phone' );
				$company_address = get_field( 'brk_office_address' );
				
				$page_check = get_page_by_title( $company_name, '', 'agent' );
		
				//if( !isset( $this->wp_exist_post_by_title( 'company', $company_name ) ) ) {
					//echo '<pre>'. $company_name .'</pre>';
				//}
				
					/*$new_office = array(
						'post_type' => 'company',
						'post_title' => $company_name,
						'post_status' => 'publish',
						'post_author' => 1,
					);
				
					if( !$this->wp_exist_post_by_title( 'company', $company_name ) ) {
						$new_office_id = wp_insert_post($new_office);
					}
					
					update_post_meta($new_office_id, 'company_office_phone', $company_phone );
					update_post_meta($new_office_id, 'company_office_address', $company_address );
				//}*/
			
			endwhile;
		endif;
		
		return;
		
		wp_reset_query();
	}
	
	function wp_exist_post_by_title( $post_type, $title ) {
		global $wpdb;
		$return = $wpdb->get_row( "SELECT ID FROM wp_posts WHERE post_title = '" . $title . "' && post_status = 'publish' && post_type = '" . $post_type . "' ", 'ARRAY_N' );
		if( empty( $return ) ) {
			return false;
		} else {
			return true;
		}
	}
}

new CompanySettingsPage;
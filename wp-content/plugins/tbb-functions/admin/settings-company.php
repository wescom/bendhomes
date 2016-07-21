<?php
/*class CompanySettingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_settings_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_company_files' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}
	
	function enqueue_company_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . 'css/company-settings.css' );
	}

	function add_plugin_settings_menu() {
		// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
		//add_options_page( 'Test Plugin', 'Test', 'manage_options', 'test-plugin', array($this, 'create_plugin_settings_page') );
		add_submenu_page(
			'edit.php?post_type=company',
			'Company Settings',
			'manage_options',
			'company-settings',
			array( $this, 'create_plugin_settings_page' )
		);
	}

	function create_plugin_settings_page() {
	?>
    
<div class="wrap tbb-company-page">
	<h1>Company Settings</h1>
    
    <div class="company-wrap">
            
        <h2 class="nav-tab-wrapper" id="tbb-company-tabs">
            <a class="nav-tab nav-tab-active" id="tbb-general-tab" href="#top#general">General</a>
            <a class="nav-tab" id="tbb-shortcodes-tab" href="#top#shortcodes">Shortcodes</a>
        </h2>
        
        <div id="sections">
            <section id="general" class="tbb-tab active">
                <h3>General Settings</h3>
                <form id="create-companies" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
				<?php
                    // This prints out all hidden setting fields
                    // settings_fields( $option_group )
                    settings_fields( 'main-settings-group' );
                    // do_settings_sections( $page )
                    do_settings_sections( 'test-plugin-main-settings-section' );
                ?>
                <?php submit_button('Save Changes'); ?>
                </form>
                
                <form id="setup-featured-agents" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
				<?php
                    // This prints out all hidden setting fields
                    // settings_fields( $option_group )
                    settings_fields( 'additional-settings-group' );
                    // do_settings_sections( $page )	
                    do_settings_sections( 'test-plugin-additional-settings-section' );
                ?>
                <?php submit_button('Save Changes'); ?>
                </form>
            </section>
            
            <section id="shortcodes" class="tbb-tab">
                <h3>Shortcodes</h3>
                <p>This section displays a list of available shortcodes to use with the Company post type to display companies on the website.</p>
                <p><strong>Shortcode: [BH_CUSTOM_POSTS]</strong></p>
            </section>
        </div>
    
    </div>
</div><!-- end wrap -->
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
			if( confirm("If you're sure, click OK to continue. This will take several minutes.") ) {
				$("#create-companies").submit();
				$("#company-submit").after('<span class="holdon">Please hold, we\'re creating your companies.</span>');
			} else {
				$(this).removeAttr("disabled");	
			}
		});
	})( jQuery );
</script> */ /* ?>
<script type="text/javascript">
!function(a){a(document).on("click",".nav-tab-wrapper a",function(){return a("section").hide().removeClass("active"),a(".nav-tab-wrapper a").removeClass("nav-tab-active"),a(".nav-tab-wrapper a").eq(a(this).index()).addClass("nav-tab-active"),a("section").eq(a(this).index()).show().addClass("active"),!1}),a("#company-submit").click(function(e){e.preventDefault(),a(this).attr("disabled","disabled"),confirm("If you're sure, click OK to continue. This may take a few minutes.")?(a("#create-companies").submit(),a("#company-submit").after('<span class="holdon">Please hold on, we\'re creating/updating all companies.</span>')):a(this).removeAttr("disabled")})}(jQuery);
</script>

	<?php
	}

	function register_settings() {

		// add_settings_section( $id, $title, $callback, $page )
		add_settings_section(
			'main-settings-section',
			'Create/Update Companies',
			array($this, 'print_main_settings_section_info'),
			'test-plugin-main-settings-section'
		);

		// add_settings_field( $id, $title, $callback, $page, $section, $args )
		add_settings_field(
			'some-setting', 
			'Create Featured Agents From Companies', 
			array($this, 'create_input_some_setting'), 
			'test-plugin-main-settings-section', 
			'main-settings-section'
		);

		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting( 'main-settings-group', 'test_plugin_main_settings_arraykey', array($this, 'plugin_main_settings_validate') );

		// add_settings_section( $id, $title, $callback, $page )
		add_settings_section(
			'additional-settings-section',
			'Additional Settings',
			array($this, 'print_additional_settings_section_info'),
			'test-plugin-additional-settings-section'
		);

		// add_settings_field( $id, $title, $callback, $page, $section, $args )
		add_settings_field(
			'another-setting', 
			'Another Setting', 
			array($this, 'create_input_another_setting'), 
			'test-plugin-additional-settings-section', 
			'additional-settings-section'
		);

		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting( 'additional-settings-group', 'test_plugin_additonal_settings_arraykey', array($this, 'plugin_additional_settings_validate') );
	}

	function print_main_settings_section_info() {
		echo '<p>Create companies imported from Agents meta fields. To create company posts click the button below.</p>';
	}

	function create_input_some_setting() {
		$options = get_option('test_plugin_main_settings_arraykey');
        ?><input type="text" name="test_plugin_main_settings_arraykey[some-setting]" value="<?php echo $options['some-setting']; ?>" /><?php
	}

	function plugin_main_settings_validate($arr_input) {
		$options = get_option('test_plugin_main_settings_arraykey');
		$options['some-setting'] = trim( $arr_input['some-setting'] );
		return $options;
	}

	function print_additional_settings_section_info() {
		echo '<p>This button sets the "Company is Featured" checkbox for all agents whose company is checked as "Featured". </p>';
	}

	function create_input_another_setting() {
		$options = get_option('test_plugin_additonal_settings_arraykey');
        ?><input type="text" name="test_plugin_additonal_settings_arraykey[another-setting]" value="<?php echo $options['another-setting']; ?>" /><?php
	}

	function plugin_additional_settings_validate($arr_input) {
		$options = get_option('test_plugin_additonal_settings_arraykey');
		$options['another-setting'] = trim( $arr_input['another-setting'] );
		return $options;
	}

}

new CompanySettingsPage;*/








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
		// Do posting function here that creates/updates the companies.
		$this->create_company_posts();
		// Loop thru companies to feature agents whose company is featured
		$this->feature_agents_from_company();
	
		wp_redirect( $_SERVER['HTTP_REFERER'] .'&companies-created="true' );
		//print_r($_POST);
		exit();
	}

	function company_settings_do_page() { ?>
		
        <div class="wrap tbb-company-page">
        	<h1>Company Settings</h1>
            
            <?php if ( $_GET['companies-created'] == 'true' ) { ?>
                <div class="updated">
                    <p>Companies Created/Updated Successfully</p>
                </div>
            <?php } ?> 
            
            <div class="company-wrap">
            
                	<h2 class="nav-tab-wrapper" id="tbb-company-tabs">
                    	<a class="nav-tab nav-tab-active" id="tbb-company-tab" href="#top#company">Companies</a>
                        <a class="nav-tab" id="tbb-shortcodes-tab" href="#top#shortcodes">Shortcodes</a>
                    </h2>
                    
                    <div id="sections">
                        <section id="company" class="tbb-tab active">
                            <h3>Create/Update Companies</h3>
                            <p>Create companies imported from Agents meta fields. To create company posts click the button below.</p>
                            <form id="create-companies" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
                            <p>
                                <input type="hidden" name="action" value="companies_created" />
                                <input id="company-submit" class="button" type="submit" value="<?php _e( 'Create/Update Companies', 'tbb_company' ); ?>" />
                            </p>
                            </form>
                            <br>
                            <h3>Featured Agents from Companies</h3>
                            <p>This button below sets the "Company is Featured" checkbox for all agents whose company is checked as "Featured".</p>
                            <form id="featured-agents" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
                            <p>
                                <input type="hidden" name="action" value="featured_agents" />
                                <input id="agents-submit" class="button" type="submit" value="<?php _e( 'Set Agents', 'tbb_company' ); ?>" />
                            </p>
                            </form>
                        </section>
                        
                        <section id="shortcodes" class="tbb-tab">
                            <h3>Shortcodes</h3>
                            <p>This section displays a list of available shortcodes to use with the Company post type to display companies on the website.</p>
                            <p><strong>Shortcode: [BH_CUSTOM_POSTS]</strong></p>
                        </section>
                    </div>
            
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
					if( confirm("If you're sure, click OK to continue. This will take several minutes.") ) {
						$("#create-companies").submit();
						$("#company-submit").after('<span class="holdon">Please hold, we\'re creating your companies.</span>');
					} else {
						$(this).removeAttr("disabled");	
					}
				});
			})( jQuery );
		</script> */ ?>
        <script type="text/javascript">
		!function(a){a(document).on("click",".nav-tab-wrapper a",function(){return a("section").hide().removeClass("active"),a(".nav-tab-wrapper a").removeClass("nav-tab-active"),a(".nav-tab-wrapper a").eq(a(this).index()).addClass("nav-tab-active"),a("section").eq(a(this).index()).show().addClass("active"),!1}),a("#company-submit").click(function(e){e.preventDefault(),a(this).attr("disabled","disabled"),confirm("If you're sure, click OK to continue. This may take a few minutes.")?(a("#create-companies").submit(),a("#company-submit").after('<span class="holdon">Please hold on, we\'re creating/updating all companies.</span>')):a(this).removeAttr("disabled")})}(jQuery);
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
			
				$agent_id = get_the_ID();
							
				$company_name = get_field( 'brk_office_name' );
				$company_phone = get_field( 'brk_office_phone' );
				$company_address = str_replace( '<br />', '', get_field( 'brk_office_address' ) );
								
				if ( !get_page_by_title($company_name, 'OBJECT', 'company')) {
				
					$new_office = array(
						'post_type' => 'company',
						'post_title' => $company_name,
						'post_status' => 'publish',
						'post_author' => 1,
					);
				
					$new_office_id = wp_insert_post($new_office);
					//$field_key = 'field_57572e625ce58';
					$agent_array = array( $agent_id );
					
					update_post_meta( $new_office_id, 'company_office_phone', $company_phone );
					update_post_meta( $new_office_id, 'company_office_address', $company_address );
					
					update_post_meta( $new_office_id, 'company_agents', $agent_array );
				
				} else {
				
					$company_check = get_page_by_title($company_name, 'OBJECT', 'company');
					$agents_list = get_post_meta( $company_check->ID, 'company_agents', true );
					//$company_is_featured = get_post_meta( $company_check->ID, 'company_featured_company', true );
					if( !array( $agents_list ) ) $agents_list = array();
					if( !in_array( $agent_id, $agents_list ) ) $agents_list[] = $agent_id;
					
					update_post_meta($company_check->ID, 'company_office_phone', $company_phone );
					update_post_meta($company_check->ID, 'company_office_address', str_replace('<br />', '', $company_address) );
					
					update_post_meta( $company_check->ID, 'company_agents', $agents_list );
											
					/*foreach( $agents_list as $agent_item ) {
						query_posts( 'p='. $agent_item );
						while (have_posts()) : the_post();
							update_post_meta( get_the_ID(), 'brk_office_is_featured', $company_is_featured );
						endwhile;
						wp_reset_query();
					}*/
											
				}
			
			endwhile;
		endif;
		
		return;
		
		wp_reset_query();
	}
	
	/*function feature_agents_from_company() {
		$company_args = array(
			'post_type' => 'company',
			'posts_per_page' => '-1'
		);
		
		$company = new WP_Query( $company_args );
		
		if ( $company->have_posts() ) :	
			while ( $company->have_posts() ) : $company->the_post();
			
				$company_featured = get_field( 'company_featured_company' );
				
				if( $company_featured == '1' ) {
					
					$agents_array = get_field( 'company_agents' );
					
					$agent_args = array(
						'post_type' => 'agent',
						'post__in' => $agents_array,
						'posts_per_page' => -1
					);
					
					wp_reset_query();
					
					$agents = new WP_Query( $agent_args );
					
					if ( $agents->have_posts() ) :	
						while ( $agents->have_posts() ) : $agents->the_post();
						
							update_post_meta( get_the_ID(), 'brk_office_is_featured', $company_featured );
						
						endwhile;
					endif;
				
					wp_reset_query();
					
				}
				
			endwhile;
		endif;
		
		return;
		
		wp_reset_query();
	}*/
	
}

new CompanySettingsPage;
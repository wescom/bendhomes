<?php
// Settings page under Agent Post Type

class AgentsSettingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_agents_files' ) );
		add_action( 'admin_action_agents_created', array( $this, 'featured_agents_admin_action' ) );
	}

	function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=agent',
			'Agents Settings',
			'Settings',
			'manage_options',
			'agent-settings',
			array(
				$this,
				'agents_settings_do_page'
			)
		);
	}
	
	function enqueue_agents_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . 'css/company-settings.css' );
	}
	
	function featured_agents_admin_action() {
		// Loop thru companies to feature agents whose company is featured
		$this->feature_agents_from_company();
	
		wp_redirect( $_SERVER['HTTP_REFERER'] .'&agents-featured="true' );
		//print_r($_POST);
		exit();
	}

	function agents_settings_do_page() { ?>
		
        <div class="wrap tbb-company-page tbb-agents">
        	<h1>Agents Settings</h1>
            
            <?php if ( $_GET['agents-featured'] == 'true' ) { ?>
                <div class="updated">
                    <p>Agents Featured Successfully</p>
                </div>
            <?php } ?> 
            
            <div class="company-wrap">
            
                	<h2 class="nav-tab-wrapper" id="tbb-agents-tabs">
                    	<a class="nav-tab nav-tab-active" id="tbb-agents-tab" href="#top#agents">Agents</a>
                        <!--a class="nav-tab" id="tbb-shortcodes-tab" href="#top#shortcodes">Shortcodes</a-->
                    </h2>
                    
                    <div id="sections">
                        <section id="agents" class="tbb-tab active">
                            <h3>Featured Agents</h3>
                            <p>This button will make Agents featured if their company is featured.</p>
                            <form id="featured-agents" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
                            <p>
                                <input type="hidden" name="action" value="featured_agents" />
                                <input id="agent-submit" class="button-primary" type="submit" value="<?php _e( 'Generate Featured Agents', 'tbb_company' ); ?>" />
                            </p>
                            </form>
                        </section>
                        
                        <!--section id="shortcodes" class="tbb-tab">
                            <h3>Shortcodes</h3>
                            <p>This section displays a list of available shortcodes to use with the Company post type to display companies on the website.</p>
                            <p><strong>Shortcode: [BH_CUSTOM_POSTS]</strong></p>
                        </section-->
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
				$('#agent-submit').click(function(e) {	
					e.preventDefault();
					$(this).attr("disabled","disabled");
					if( confirm("If you're sure, click OK to continue. This will take several minutes.") ) {
						$("#featured-agents").submit();
						$("#agent-submit").after('<span class="holdon">Please hold, we\'re setting featured agents.</span>');
					} else {
						$(this).removeAttr("disabled");	
					}
				});
			})( jQuery );
		</script> */ ?>
        <script type="text/javascript">
		!function(a){a(document).on("click",".nav-tab-wrapper a",function(){return a("section").hide().removeClass("active"),a(".nav-tab-wrapper a").removeClass("nav-tab-active"),a(".nav-tab-wrapper a").eq(a(this).index()).addClass("nav-tab-active"),a("section").eq(a(this).index()).show().addClass("active"),!1}),a("#agent-submit").click(function(e){e.preventDefault(),a(this).attr("disabled","disabled"),confirm("If you're sure, click OK to continue. This may take a few minutes.")?(a("#featured-agents").submit(),a("#agent-submit").after('<span class="holdon">Please hold on, we\'re setting featured agents.</span>')):a(this).removeAttr("disabled")})}(jQuery);
		</script>
	<?php }
	
	function feature_agents_from_company() {
		$company_args = array(
			'post_type' => 'company',
			'posts_per_page' => '-1',
		);
		
		$company = new WP_Query( $company_args );
		
		if ( $company->have_posts() ) :	
			while ( $company->have_posts() ) : $company->the_post();
			
				$company_featured = get_field( 'company_featured_company' );		
				$agents_array = get_field( 'company_agents' );
								
				$agent_args = array(
					'post_type' => 'agent',
					'post__in' => $agents_array,
					'posts_per_page' => -1
				);
				
				$agents = new WP_Query( $agent_args );
				
				if ( $agents->have_posts() ) :	
					while ( $agents->have_posts() ) : $agents->the_post();
					
						$agent_id = get_the_ID();
						update_post_meta( $agent_id, 'brk_office_is_featured', $company_featured );
					
					endwhile;
				endif;
			
				wp_reset_query();
									
			endwhile;
		endif;
		
		return;
		
		wp_reset_query();
	}
	
}

new AgentsSettingsPage;
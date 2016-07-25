<?php
// Settings page under Agent Post Type

class AgentSettingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_agent_files' ) );
		add_action( 'admin_action_agents_created', array( $this, 'agents_created_admin_action' ) );
	}

	function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=agent',
			'Agent Settings',
			'Settings',
			'manage_options',
			'agent-settings',
			array(
				$this,
				'agent_settings_do_page'
			)
		);
	}
	
	function enqueue_agent_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . 'css/company-settings.css' );
	}
	
	function agents_created_admin_action() {
		// Do posting function here that sets agents to featured.
		$this->create_agent_posts();
	
		wp_redirect( $_SERVER['HTTP_REFERER'] .'&agents-created=true' );
		//print_r($_POST);
		exit();
	}

	function agent_settings_do_page() { ?>
		
        <div class="wrap tbb-company-page agents-settings">
        	<h1>Agent Settings</h1>
            
            <?php if ( $_GET['agents-created'] == 'true' ) { ?>
                <div class="updated">
                    <p>Featured agents generated successfully.</p>
                </div>
            <?php } ?> 
            
            <div class="company-wrap">
            
                	<h2 class="nav-tab-wrapper" id="tbb-agent-tabs">
                    	<a class="nav-tab nav-tab-active" id="tbb-agent-tab" href="#top#agent">Featured Agents</a>
                        <a class="nav-tab" id="tbb-shortcodes-tab" href="#top#shortcodes">Shortcodes</a>
                    </h2>
                    
                    <div id="sections">
                        <section id="agent" class="tbb-tab active">
                            <h3>Featured Agents</h3>
                            <p>This button will make Agents featured if their company is featured.</p>
                            <form id="create-agents" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
                            <p>
                                <input type="hidden" name="action" value="agents_created" />
                                <input id="agent-submit" class="button-primary" type="submit" value="<?php _e( 'Generate Featured Agents', 'tbb_agent' ); ?>" />
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
				$('#agent-submit').click(function(e) {	
					e.preventDefault();
					$(this).attr("disabled","disabled");
					if( confirm("If you're sure, click OK to continue. This will take several minutes.") ) {
						$("#create-agents").submit();
						$("#agent-submit").after('<span class="holdon">Please hold, we\'re generating your featured agents.</span>');
					} else {
						$(this).removeAttr("disabled");	
					}
				});
			})( jQuery );
		</script> */ ?>
        <script type="text/javascript">
		!function(a){a(document).on("click",".nav-tab-wrapper a",function(){return a("section").hide().removeClass("active"),a(".nav-tab-wrapper a").removeClass("nav-tab-active"),a(".nav-tab-wrapper a").eq(a(this).index()).addClass("nav-tab-active"),a("section").eq(a(this).index()).show().addClass("active"),!1}),a("#agent-submit").click(function(e){e.preventDefault(),a(this).attr("disabled","disabled"),confirm("If you're sure, click OK to continue. This may take a few minutes.")?(a("#create-agents").submit(),a("#agent-submit").after('<span class="holdon">Please hold on, we\'re generating your featured agents.</span>')):a(this).removeAttr("disabled")})}(jQuery);
		</script>
	<?php }
	
	function create_agent_posts() {
		$args = array(
			'post_type' => 'company',
			'posts_per_page' => '-1'
		);
		
		$companies = new WP_Query( $args );
		
		if ( $companies->have_posts() ) :	
			while ( $companies->have_posts() ) : $companies->the_post();
			
				$company_id = get_the_ID();
							
				$company_featured = get_field( 'company_featured_company' );		
				$agents_array = array_diff( get_field( 'company_agents' ), array('') );
							
				$agent_args = array(
					'post_type' => 'agent',
					'post__in' => $agents_array,
					'posts_per_page' => -1,
					'ignore_sticky_posts' => true
				);
				
				$agents = new WP_Query( $agent_args );
								
				if( $agents->have_posts() ) :
					 while( $agents->have_posts() ) : $agents->the_post(); 
					 	
						$agent_id = get_the_ID();
						
						$agent_categories = array();
						$categories = get_categories();
						foreach( $categories as $category ) {
							$agent_categories[] = $category->cat_ID;
						}
						$agent_categories = array_map( 'intval', $agent_categories );
						$agent_categories = array_unique( $agent_categories );
						
						$agent_types = wp_get_post_terms( $agent_id, 'agent_types', array("fields" => "all"));
						$agent_type = $agent_types[0]->slug;
						
						if( $company_featured == '1' && $agent_type != 'featured-agent' && $agent_type != 'unpublished-agent' ) {
							
							//update_post_meta( $agent_id, 'brk_office_is_featured', $company_featured );
							wp_set_object_terms( 254, $agent_categories, 'agent_types', true );
							wp_update_post( array('post_category' => array() ) );
						
						} else {
							
							wp_set_object_terms( 241, $agent_categories, 'agent_types', true );
							
						}
						
					 endwhile;
				endif;
				
				wp_reset_query();
			
			endwhile;
		endif;
		
		return;
		
		wp_reset_query();
	}
	
}

new AgentSettingsPage;

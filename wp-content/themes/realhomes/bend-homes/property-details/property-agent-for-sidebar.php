<?php
function bhAgentRender($page_position) {
	global $post;
  
  
  
  	/*$property_ID = $post->ID;
	$property_agents = get_field( 'REAL_HOMES_agents' );
	
	wp_reset_query();
	
	$agent_post = new WP_Query( array(
		'post_type' => 'agent',
		'p' => $property_agents 
	) );
	
	if( $agent_post->have_posts() ) :
		while( $agent_post->have_posts() ) : $agent_post->the_post();
	
			$agent_brokerage_office = sanitize_title( get_field( 'brk_office_name' ) );
			//echo '<p>Brokerage Office: '. $agent_brokerage_office .'</p>';
		
		endwhile;
	endif;
	
	wp_reset_query();
	
	$company_post = new WP_Query( array(
		'post_type' => 'company',
		'name' => $agent_brokerage_office
	) );
	
	if( $company_post->have_posts() ) :
		while( $company_post->have_posts() ) : $company_post->the_post();
			
			$company_is_featured = get_field( 'company_featured_company' );
			//$is_featured = $company_is_featured == true ? 'Yes' : 'No';
			//echo '<p>Is Featured: '. $is_featured .'</p>';
			
		endwhile;
	endif;*/
  
  
  

	/**
	* Logic behind displaying agents / author information
	*/
	$display_agent_info = get_option( 'theme_display_agent_info' );
	$agent_display_option = get_post_meta( $post->ID, 'REAL_HOMES_agent_display_option', true );

	if ( ( $display_agent_info == 'true' ) && ( $agent_display_option != "none" ) ) {

		$property_agents = get_post_meta( $post->ID, 'REAL_HOMES_agents' );
		
		// remove invalid ids
		$property_agents = array_filter( $property_agents, function($v){
			return ( $v > 0 );
		});
		// remove duplicated ids
		$property_agents = array_unique( $property_agents );
		
		if ( ! empty( $property_agents ) ) {
			$agents_count = count( $property_agents );
			$i = 0;
			foreach ( $property_agents as $agent ) {
				if ( 0 < intval( $agent ) ) {
					
					// Set up agent array of data to use for Featured Agent in sidebar
					$agent_args = array();
					$agent_args[ 'agent_id' ] = intval( $agent );
					$agent_args[ 'agent_display_type' ] = bhLookupTaxonomy($agent,'agent_types');
					$agent_args[ 'agents_count' ] = $agents_count;
					$agent_args[ 'agent_title_text' ] = __( 'Agent', 'framework' ) . " " . get_the_title( $agent_args[ 'agent_id' ] );
					$agent_args[ 'agent_mobile' ] = get_post_meta( $agent_args[ 'agent_id' ], 'REAL_HOMES_mobile_number', true );
					$agent_args[ 'agent_office_phone' ] = get_post_meta( $agent_args[ 'agent_id' ], 'REAL_HOMES_office_number', true );
					$agent_args[ 'agent_office_fax' ] = get_post_meta( $agent_args[ 'agent_id' ], 'REAL_HOMES_fax_number', true );
					$agent_args[ 'agent_email' ] = get_post_meta( $agent_args[ 'agent_id' ], 'REAL_HOMES_agent_email', true );
					$agent_args[ 'agent_excerpt' ] = get_post_field( 'post_content', $agent_args[ 'agent_id' ] );
					$agent_args[ 'agent_description' ] = get_framework_custom_excerpt( $agent_args[ 'agent_excerpt' ], 20 );
					$agent_args[ 'agent_brokerage' ] = get_post_meta( $agent_args[ 'agent_id' ], 'brk_office_name', true );
					
					wp_reset_query();
					
					// Query the Company of this Agent and see if the company is featured
					$company_post = new WP_Query( array(
						'post_type' => 'company',
						'name' => sanitize_title( $agent_args[ 'agent_brokerage' ] )
					) );
					
					if( $company_post->have_posts() ) :
						while( $company_post->have_posts() ) : $company_post->the_post();
							
							$company_is_featured = get_field( 'company_featured_company' );
							if($company_is_featured) {
								$company_featured = 'yes';
							} else {
								$company_featured = 'no';
							}
							
						endwhile;
					endif;
					
					wp_reset_query();
					
					// If the Agent or Company is featured and in the sidebar do this
					if( ($agent_args[ 'agent_display_type' ] == 'featured-agent') || $company_featured = 'yes' && ($page_position == 'sidebar') ) {
							
						echo '<div class="agent-'. $agent_args[ 'agent_display_type' ] .' company-featured-'. $company_featured .' position-'. $page_position .'">';
							display_sidebar_agent_box( $agent_args );
						echo '</div>';
					  
					// If the Agent is "not" featured and in the sidebar do this
					} elseif( ($agent_args[ 'agent_display_type' ] != 'featured-agent') && ($page_position == 'sidebar') ) {
					  
					  	echo '<div class="agent-'. $agent_args[ 'agent_display_type' ] .' company-featured-'. $company_featured .' position-'. $page_position .'">';
							echo '<div class="rail-button-agent-wrapper"><a href="/agents/" class="button">Find an Agent</a></div>';
						echo '</div>';
					  
					// If the Agent is "not" featured and "not" in the sidebar do this
					} elseif( ($agent_args[ 'agent_display_type' ] != 'featured-agent') && ($page_position == 'body') ) {
						
						sprintf( '<div class="agent-%s company-featured-%s position-%s">
								  <div class="rail-standard-agent-wrapper">
									<p class="listing-agent"><strong>Listing Agent: </strong><br/>%s</p>
									%s<br style="clear: both;"/>
								  </div>
								  </div>',
								  $agent_args[ 'agent_display_type' ], $company_featured, $page_position,
								  $agent_args[ 'agent_title_text' ], brokerageBlock( $agent_args[ 'agent_id' ], 'large' ) 
						);
					  
					}
				}
			}
		}
		
	}
}

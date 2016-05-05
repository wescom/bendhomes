<?php
function bhAgentRender($page_position) {
  global $post;

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

            if($agent_args[ 'agent_display_type' ] == 'featured-agent') {
              // we only want to show featured agents in right rail JTG 1777
              display_sidebar_agent_box( $agent_args );
            } elseif( ($agent_args[ 'agent_display_type' ] != 'featured-agent') && ($page_position == 'sidebar') ) {
              echo '<div class="rail-button-agent-wrapper">';
              echo '<a href="/agents/" class="button">Find an Agent</a>';
              echo '</div>';
            } elseif( ($agent_args[ 'agent_display_type' ] != 'featured-agent') && ($page_position == 'body') ) {
              echo '<div class="rail-standard-agent-wrapper">';
              echo '<p>';
              echo '<strong>Listing Agent:</strong><br/>'."\n";
              echo $agent_args[ 'agent_title_text' ]."\n";
              brokerageBlock($agent_args[ 'agent_id' ]);
              echo '</div>';
            }
            
  				}
  			}
  		}
  }
}

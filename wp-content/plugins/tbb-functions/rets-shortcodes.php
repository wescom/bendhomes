<?php
// Shortcodes built pulling directly from the RETS database instead of wordpress.
// Uses the Rets_DB class found in rets-connect.class.php

class Rets_Agents {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_agents', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'limit' => 20,
				'order' => '',
				'orderby' => '',
				'class' => '',
				'columns' => 3,
				'show_search' => true,
			), $args
		);

		extract( $defaults );
		
		switch( $columns ) {
			case "6":
				$cols_per_row = 6;
				$cols = "six";
				break;
			case "5":
				$cols_per_row = 5;
				$cols = "five";
				break;
			case "4":
				$cols_per_row = 4;
				$cols = "four";
				break;
			case "3":
				$cols_per_row = 3;
				$cols = "three";
				break;
			case "2":
				$cols_per_row = 2;
				$cols = "two";
				break;
			case "1":
				$cols_per_row = 1;
				$cols = "one";
				break;
		}
		
		$agents_query = new Rets_DB();
		//$agents = $agents_query -> select("select * from ActiveAgent_MEMB");
		
		$agents = $agents_query -> select("
			SELECT ActiveAgent_MEMB.FullName,
			ActiveAgent_MEMB.MemberNumber,
			ActiveAgent_MEMB.IsActive,
			ActiveAgent_MEMB.images,
			Agent_MEMB.ContactAddlPhoneType1 as 'ContactAddlPhoneType_1',
			Agent_MEMB.ContactPhoneAreaCode1 as 'ContactPhoneAreaCode_1',
			Agent_MEMB.ContactPhoneNumber1 as 'ContactPhoneNumber_1',
			Agent_MEMB.ContactAddlPhoneType2 as 'ContactAddlPhoneType_2',
			Agent_MEMB.ContactPhoneAreaCode2 as 'ContactPhoneAreaCode_2',
			Agent_MEMB.ContactPhoneNumber2 as 'ContactPhoneNumber_2',
			Agent_MEMB.ContactAddlPhoneType3 as 'ContactAddlPhoneType_3',
			Agent_MEMB.ContactPhoneAreaCode3 as 'ContactPhoneAreaCode_3',
			Agent_MEMB.ContactPhoneNumber3 as 'ContactPhoneNumber_3',
			# Agent_MEMB.IsActive,
			Office_OFFI.OfficeName,
			Office_OFFI.OfficePhoneComplete,
			Office_OFFI.StreetAddress,
			Office_OFFI.StreetCity,
			Office_OFFI.StreetState,
			Office_OFFI.StreetZipCode
			FROM ActiveAgent_MEMB
			LEFT JOIN Agent_MEMB on ActiveAgent_MEMB.MemberNumber = Agent_MEMB.MemberNumber
			LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber
		");
		
		//print_r( $agents );
		
		if( $agents ) {
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-agent"><div class="custom-posts-container clearfix">';
			
				foreach( $agents as $agent ) {
					
					$has_image_class = !empty( $agent[''] ) ? 'with-image' : 'without-image';
					
					$category_classes = $agent['featured'] == 1 ? 'featured' : 'not-featured';
					
					/*$image_url = !empty( $agent['images'] ) ? 
						home_url() .'/_retsapi/imagesAgents/'. $agent['images'] : 
						get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';*/
					
					$office_address = $agent['StreetAddress'] .'<br>'. $agent['StreetCity'] .', '. $agent['StreetState'] .' '. $agent['StreetZipCode'];
					
					// Begin item output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $class, $has_image_class, $category_classes );
					
						//$html .= sprintf( '<figure class="custom-post-image image-agent-image-%s"><a href="%s"><img src="%s" width="" height="" alt="%s, for %s" /></a></figure>', 
								//$count, '#', $image_url, $agent['FullName'], $agent['OfficeName'] );
					
						$html .= sprintf( '<h4 class="custom-post-title"><a href="">%s</a></h4>', $agent['FullName'] );
					
						$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
									$agent['OfficeName'], $office_address, $agent['OfficePhoneComplete'] );
					
						$html .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', '' );
					
						/*$html .= 
							'<p>
							Name: ' .$agent['FullName'] .'<br>
							Is Active: '. $agent['IsActive'] .'<br>
							Member Number: '. $agent['MemberNumber'] .'<br>
							MLS ID: '. $agent['MLSID'] .'<br>
							Office MLS ID: '. $agent['OfficeMLSID'] .'<br>
							Office Name: '. $agent['OfficeName'] .'<br>
							Office Number: '. $agent['OfficeNumber'] .'<br>
							Is Featured: '. $agent['featured'] .
							'</p>';*/
					
					$html .= '</div></div>';
					// End item ouput
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
			
			$html .= sprintf( '</div>%s</div>', 'Pagination goes here' );
			
		}
		
		return $html;
		
    }
	
}
 
new Rets_Agents();
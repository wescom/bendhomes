<?php
// Shortcodes built pulling directly from the RETS database instead of wordpress.
// Uses the Rets_DB class found in rets-connect.class.php


// Creates agents list on /agents page
class Rets_Agents {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_agents', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'limit' => 50,
				'order' => '',
				'orderby' => '',
				'class' => '',
				'columns' => 3,
				'show_search' => '',
				'linkto' => 'agent'
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
		
		$sort_order = '';
		
		if( !empty( $order ) && !empty( $orderby ) ) {
			$sort_order = 'ORDER BY '. $orderby .' '. $order;
		}
		
		// Enable order A-Z & Z-A select field if url contains ?sort= param
		$url_sort = '';
		$url_sort = $_GET['sort'];

		if( $url_sort == 'a-z' ) {
			$sort_order = 'ORDER BY FullName ASC';
		}
		if( $url_sort == 'z-a' ) {
			$sort_order = 'ORDER BY FullName DESC';
		}
		
		$query = "
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
			WHERE ActiveAgent_MEMB.OfficeNumber <> 99999 
			{$sort_order}
		";
		
		$agents_query = new Rets_DB();
		
		$agents = $agents_query->select( $query );
		
		//print_r( $agents );
		
		if( $agents ) {
			
			$total_agents = count( $agents );
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-agent"><div class="custom-posts-container clearfix">';
			
				$html .= '<div style="padding: 0 10px; color: #999;">'. number_format( $total_agents ) .' Total Agents</div>';
			
				if( empty( $show_search ) ) {
			
					$html .= '<div class="custom-search-wrap">';
						$html .= '
							<form role="search" action="'. site_url('/') .'" method="get" id="searchform">
								<input type="text" class="search-field" name="s" placeholder="Find an agent"/>
								<input type="hidden" name="post_type" value="agent" />
								<input type="submit" class="btn real-btn" alt="Search" value="Search" />
							</form>
						';
					$html .= '</div>';

				}

				$current_url = home_url() .''. $_SERVER['PHP_SELF'];
				$html .= '<div class="order-box option-bar small clearfix">';
					$html .= '<span class="selectwrap"><select id="sort-order" class="sort-order search-select">';

						$option_values = '';
						if( $url_sort == 'a-z' ) {
							$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
							$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
							$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
						} elseif( $url_sort == 'z-a' ) {
							$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
							$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
							$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
						} else {
							$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
							$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
							$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
						}
						$html .= $option_values;

					$html .= '</select></span>';
				$html .= '</div>';
				$html .= '<script>
							document.getElementById("sort-order").onchange = function() { if (this.selectedIndex!==0) { window.location.href = this.value; } };
							</script>';
			
				foreach( $agents as $agent ) {
										
					$category_classes = $agent['featured'] == 1 ? 'featured' : 'not-featured';
					
					if( !empty( $agent['images'] ) ) {
						$has_image_class = 'width-image';
						$image_url = home_url() .'/_retsapi/imagesAgents/'. $agent['images'];
					} else {
						$has_image_class = 'without-image';
						$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
					}
					
					$office_address = $agent['StreetAddress'] .'<br>'. $agent['StreetCity'] .', '. $agent['StreetState'] .' '. $agent['StreetZipCode'];
					
					$permalink = home_url() .'/'. $linkto .'/?agent='. $this->create_slug( $agent['FullName'] ) .'&id='. $agent['MemberNumber'];
					
					// Begin agent output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $class, $has_image_class, $category_classes );
					
						$html .= sprintf( '<figure class="custom-post-image image-agent-image-%s"><a href="%s"><img src="%s" width="" height="" alt="%s, for %s" /></a></figure>', 
								$count, $permalink, $image_url, $agent['FullName'], $agent['OfficeName'] );
					
						$html .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', $permalink, $agent['FullName'] );
					
						$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
									$agent['OfficeName'], $office_address, $agent['OfficePhoneComplete'] );
					
						$html .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', $permalink );
					
					$html .= '</div></div>';
					// End agent ouput
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
			
			
			
			$html .= sprintf( '</div>%s</div>', 'Pagination goes here' );
			
		}
		
		return $html;
		
    } // end render
	
	public function create_slug( $string ) {
				
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
	   
		return $slug;
		
	} // end create_slug
	
} 
new Rets_Agents();



// Creates single agent page content linked from agents list shortcode above
class Rets_Agent {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_agent', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'class' => '',
				'linkto' => 'agents',
				'member_number' => ''
			), $args
		);

		extract( $defaults );
		
		$id = !empty( $_GET['id'] ) ? $_GET['id'] : $member_number;
		$id = mysql_real_escape_string( floatval( $id ) );
	
		$query = "
			SELECT * FROM ActiveAgent_MEMB 
			LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber
			WHERE ActiveAgent_MEMB.MemberNumber = {$id}
		";
		
		$agent_query = new Rets_DB();
		
		$agent = $agent_query->select( $query )[0];
		
		if( $agent ) {
			
			print_r( $agent );
			
			$category_classes = $agent['featured'] == 1 ? 'featured' : 'not-featured';
					
			if( !empty( $agent['images'] ) ) {
				$has_image_class = 'width-image';
				$image_url = home_url() .'/_retsapi/imagesAgents/'. $agent['images'];
			} else {
				$has_image_class = 'without-image';
				$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
			}

			$office_address = $agent['StreetAddress'] .'<br>'. $agent['StreetCity'] .', '. $agent['StreetState'] .' '. $agent['StreetZipCode'];
			
			$html .= sprintf( '<div class="post-agent agent-%s agent-%s">', $id, $category_classes );
						
				$html .= '<div class="row-fluid"><div class="span12"><div class="agent-info-wrap">';

					$html .= sprintf('<img src="%s" alt="%s" width="" height="" class="alignleft" />', $image_url, $agent['FullName'] );

					$html .= sprintf('<h1 class="agent-name">%s</h1>', $agent['FullName'] );

					$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
								$agent['OfficeName'], $office_address, $agent['OfficePhoneComplete'] );
			
				$html .= '</div></div></div>';
			
				if( $agent['featured'] == 1 ) {
						
				$html .= '<div class="row-fluid"><div class="span12"><div class="agent-properties">';
					
					$html .= '<p>Property List Here...</p>';

				$html .= '</div></div></div>';
				
				}
			
			$html .= '</div>';
			
		}
		
		return $html;
		
	}
	
	public function get_agent_properties( $id ) {
		
	}
	
}
new Rets_Agent();


// Creates companies list on /companies page
class Rets_Companies {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_companies', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'limit' => 50,
				'order' => '',
				'orderby' => '',
				'class' => '',
				'columns' => 3,
				'show_search' => '',
				'linkto' => 'company'
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
		
		$sort_order = '';
		
		if( !empty( $order ) && !empty( $orderby ) ) {
			$sort_order = 'ORDER BY '. $orderby .' '. $order;
		}
		
		// Enable order A-Z & Z-A select field if url contains ?sort= param
		$url_sort = '';
		$url_sort = $_GET['sort'];

		if( $url_sort == 'a-z' ) {
			$sort_order = 'ORDER BY OfficeName ASC';
		}
		if( $url_sort == 'z-a' ) {
			$sort_order = 'ORDER BY OfficeName DESC';
		}
		
		$query = "
			SELECT Office_OFFI.IsActive,
			Office_OFFI.MLSID,
			Office_OFFI.OfficeName,
			Office_OFFI.OfficeNumber,
			Office_OFFI.OfficePhone,
			Office_OFFI.OfficePhoneComplete,
			Office_OFFI.StreetAddress,
			Office_OFFI.StreetCity,
			Office_OFFI.StreetState,
			Office_OFFI.StreetZipCode,
			Office_OFFI.OfficeDescription,
			Office_OFFI.DisplayName,
			Office_OFFI.featured
			FROM Office_OFFI
			{$sort_order}
		";
		
		$companies_query = new Rets_DB();
		
		$companies = $companies_query->select( $query );
		
		//print_r( $agents );
		
		if( $companies ) {
			
			$total_companies = count( $companies );
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-agent"><div class="custom-posts-container clearfix">';
			
				$html .= '<div style="padding: 0 10px; color: #999;">'. number_format( $total_companies ) .' Total Companies</div>';
			
				if( empty( $show_search ) ) {
			
					$html .= '<div class="custom-search-wrap">';
						$html .= '
							<form role="search" action="'. site_url('/') .'" method="get" id="searchform">
								<input type="text" class="search-field" name="s" placeholder="Find an agent"/>
								<input type="hidden" name="post_type" value="agent" />
								<input type="submit" class="btn real-btn" alt="Search" value="Search" />
							</form>
						';
					$html .= '</div>';

				}

				$current_url = home_url() .''. $_SERVER['PHP_SELF'];
				$html .= '<div class="order-box option-bar small clearfix">';
					$html .= '<span class="selectwrap"><select id="sort-order" class="sort-order search-select">';

						$option_values = '';
						if( $url_sort == 'a-z' ) {
							$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
							$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
							$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
						} elseif( $url_sort == 'z-a' ) {
							$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
							$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
							$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
						} else {
							$option_values .= '<option value="'. $current_url .'">Order: Random</option>';
							$option_values .= '<option value="'. $current_url .'?sort=a-z">Order: A - Z</option>';
							$option_values .= '<option value="'. $current_url .'?sort=z-a">Order: Z - A</option>';
						}
						$html .= $option_values;

					$html .= '</select></span>';
				$html .= '</div>';
				$html .= '<script>
							document.getElementById("sort-order").onchange = function() { if (this.selectedIndex!==0) { window.location.href = this.value; } };
							</script>';
			
				foreach( $companies as $company ) {
										
					$category_classes = $company['featured'] == 1 ? 'featured' : 'not-featured';
					
					/*if( !empty( $company['images'] ) ) {
						$has_image_class = 'width-image';
						$image_url = home_url() .'/_retsapi/imagesAgents/'. $company['images'];
					} else {
						$has_image_class = 'without-image';
						$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
					}*/
					
					$office_address = $company['StreetAddress'] .'<br>'. $company['StreetCity'] .', '. $company['StreetState'] .' '. $agent['StreetZipCode'];
					
					$permalink = home_url() .'/'. $linkto .'/?company='. $this->create_slug( $company['OfficeName'] ) .'&id='. $company['MemberNumber'];
					
					// Begin agent output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $class, $has_image_class, $category_classes );
					
						$html .= sprintf( '<figure class="custom-post-image image-agent-image-%s"><a href="%s"><img src="%s" width="" height="" alt="%s, for %s" /></a></figure>', 
								$count, $permalink, $image_url, $company['OfficeName'] );

					
						$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
									$company['OfficeName'], $office_address, $company['OfficePhoneComplete'] );
					
						$html .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', $permalink );
					
					$html .= '</div></div>';
					// End agent ouput
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
			
			
			
			$html .= sprintf( '</div>%s</div>', 'Pagination goes here' );
			
		}
		
		return $html;
		
    } // end render
	
	public function create_slug( $string ) {
				
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
	   
		return $slug;
		
	} // end create_slug
	
} 
new Rets_Companies();

// Creates single company page content linked from agents list shortcode above
class Rets_Company {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_company', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'class' => '',
				'linkto' => 'companies',
				'member_number' => ''
			), $args
		);

		extract( $defaults );
		
		$id = !empty( $_GET['id'] ) ? $_GET['id'] : $member_number;
		$id = mysql_real_escape_string( floatval( $id ) );
	
		$query = "
			SELECT * FROM Offices_OFFI 
			WHERE AOfficeNumber = {$id}
		";
		
		$company_query = new Rets_DB();
		
		$company = $company_query->select( $query )[0];
		
		if( $company ) {
			
			print_r( $company );
			
			$category_classes = $company['featured'] == 1 ? 'featured' : 'not-featured';

			$office_address = $company['StreetAddress'] .'<br>'. $company['StreetCity'] .', '. $company['StreetState'] .' '. $company['StreetZipCode'];
			
			$html .= sprintf( '<div class="post-agent agent-%s agent-%s">', $id, $category_classes );
						
				$html .= '<div class="row-fluid"><div class="span12"><div class="agent-info-wrap">';

					$html .= sprintf('<img src="%s" alt="%s" width="" height="" class="alignleft" />', $image_url, $company['OfficeName'] );

					$html .= sprintf('<h1 class="agent-name">%s</h1>', $company['OfficeName'] );

					$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
								$company['OfficeName'], $office_address, $company['OfficePhoneComplete'] );
			
				$html .= '</div></div></div>';
			
				if( $company['featured'] == 1 ) {
						
				$html .= '<div class="row-fluid"><div class="span12"><div class="agent-properties">';
					
					$html .= '<p>Property List Here...</p>';

				$html .= '</div></div></div>';
				
				}
			
			$html .= '</div>';
			
		}
		
		return $html;
		
	}
	
	public function get_company_properties( $id ) {
		
	}
	
}
new Rets_Company();

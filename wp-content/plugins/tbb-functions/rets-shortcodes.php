<?php // test
// Shortcodes built pulling directly from the RETS database instead of wordpress.
// Uses the Rets_DB class found in rets-connect.class.php


// Returns an abbreviated street direction. i.e. Northeast => NE
function rets_get_short_direction( $name ) {
	switch( $name ) {
		case 'North' :
			$name = 'N';
			break;
		case 'East' :
			$name = 'E';
			break;
		case 'South' :
			$name = 'S';
			break;
		case 'West' :
			$name = 'W';
			break;
		case 'Northeast' :
			$name = 'NE';
			break;
		case 'Northwest' :
			$name = 'NW';
			break;
		case 'Southeast' :
			$name = 'SE';
			break;
		case 'Southwest' :
			$name = 'SW';
			break;
		default:
			return $name;
	}
	return $name;
}


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
				'limit' => 500,
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
		} if( $url_sort == 'z-a' ) {
			$sort_order = 'ORDER BY FullName DESC';
		}

		$searchString = '';
		$searchString = $_GET['search'];
		
		if ($searchString == '') {
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
				AND (Office_OFFI.featured = 1 OR ActiveAgent_MEMB.featured = 1)
				{$sort_order}
				LIMIT {$limit}
			";
		} else {
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
				Office_OFFI.OfficeName,
				Office_OFFI.OfficePhoneComplete,
				Office_OFFI.StreetAddress,
				Office_OFFI.StreetCity,
				Office_OFFI.StreetState,
				Office_OFFI.StreetZipCode
				FROM ActiveAgent_MEMB
				LEFT JOIN Agent_MEMB on ActiveAgent_MEMB.MemberNumber = Agent_MEMB.MemberNumber
				LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber
				WHERE ActiveAgent_MEMB.OfficeNumber <> 99999 AND ActiveAgent_MEMB.FullName LIKE '%{$searchString}%'
				{$sort_order}
				LIMIT {$limit}
			";
		}
		
		$agents_query = new Rets_DB();
		
		$agents = $agents_query->select( $query );

		if ($sort_order == '') {
			shuffle($agents);
		}
		
		//print_r( $agents );
		
		if( $agents ) {
			
			$total_agents = count( $agents );
			
			$html .= '<div style="padding: 0 10px; color: #999;">'. number_format( $total_agents) .' Total Agents</div>';
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-agent rets-agents"><div class="custom-posts-container clearfix">';
			
				//$html .= '<div style="padding: 0 10px; color: #999;">'. number_format( $total_agents ) .' Total Agents</div>';
			
				if( empty( $show_search ) ) {
			
					$html .= '<div class="custom-search-wrap">';
						$html .= '
							<form role="search" action="'. site_url('/') .'agents" method="get" id="searchform">
								<input type="text" class="search-field" name="search" placeholder="Find an agent"/>
								<input type="hidden" name="post_type" value="agent" />
								<input type="submit" class="btn real-btn" alt="Search" value="Search" />
							</form>
						';
					$html .= '</div>';

				}

				$current_url = $this->get_current_url();
			
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
			
			
			
			//$html .= sprintf( '</div>%s</div>', $this->pagination( $limit, $total_agents, '3' ) );
			
		}
		
		return $html;
		
    } // end render
	
	public function create_slug( $string ) {
				
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
	   
		return $slug;
		
	}
	
	public function get_current_url() {
		
		$base_url = explode('/', $_SERVER['REQUEST_URI']);
		
		$url = home_url() .'/'. $base_url[1];
		
		return $url;
	}
	
} 
new Rets_Agents();



// Display a single agent
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
			SELECT ActiveAgent_MEMB.FullName,
			ActiveAgent_MEMB.MemberNumber,
			ActiveAgent_MEMB.IsActive,
			ActiveAgent_MEMB.images as 'theImage',
			Agent_MEMB.ContactAddlPhoneType1 as 'ContactAddlPhoneType_1',
			Agent_MEMB.ContactPhoneAreaCode1 as 'ContactPhoneAreaCode_1',
			Agent_MEMB.ContactPhoneNumber1 as 'ContactPhoneNumber_1',
			Agent_MEMB.ContactAddlPhoneType2 as 'ContactAddlPhoneType_2',
			Agent_MEMB.ContactPhoneAreaCode2 as 'ContactPhoneAreaCode_2',
			Agent_MEMB.ContactPhoneNumber2 as 'ContactPhoneNumber_2',
			Agent_MEMB.ContactAddlPhoneType3 as 'ContactAddlPhoneType_3',
			Agent_MEMB.ContactPhoneAreaCode3 as 'ContactPhoneAreaCode_3',
			Agent_MEMB.ContactPhoneNumber3 as 'ContactPhoneNumber_3',
			Office_OFFI.OfficeName,
			Office_OFFI.OfficePhoneComplete,
			Office_OFFI.StreetAddress,
			Office_OFFI.StreetCity,
			Office_OFFI.StreetState,
			Office_OFFI.StreetZipCode
			FROM ActiveAgent_MEMB
			LEFT JOIN Agent_MEMB on ActiveAgent_MEMB.MemberNumber = Agent_MEMB.MemberNumber
			LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber
			WHERE ActiveAgent_MEMB.MemberNumber = {$id}
		";
						//echo 'query '.$query.'<br>';  
		$agent_query = new Rets_DB();

		$agent = $agent_query->select( $query )[0];

		if( $agent ) {

			//print_r( $agent );

			$category_classes = 'not_featured';
			if ($agent['ActiveAgent_MEMB.featured'] == 1 || $agent['Office_OFFI.featured']) {
					$category_classes = 'featured';
			}
			//$category_classes = $agent['ActiveAgent_MEMB.featured'] == 1 ? 'featured' : 'not-featured';
			if( !empty( $agent['theImage'] ) ) {
				$has_image_class = 'width-image';
				$image_url = home_url() .'/_retsapi/imagesAgents/'. $agent['theImage'];
			} else {
				$has_image_class = 'without-image';
				$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
			}

			$office_address = $agent['StreetAddress'] .'<br>'. $agent['StreetCity'] .', '. $agent['StreetState'] .' '. $agent['StreetZipCode'];
			
			$office_phone = $agent['OfficePhoneComplete'];
			if ($agent['ContactAddlPhoneType_1'] == 'Cellular'){
					$agent_cell = $agent['ContactPhoneAreaCode_1']."-".$agent['ContactPhoneNumber_1'];
			} elseif ($agent['ContactAddlPhoneType_2'] == 'Cellular'){
					$agent_cell = $agent['ContactPhoneAreaCode_2']."-".$agent['ContactPhoneNumber_2'];
			} elseif ($agent['ContactAddlPhoneType_1'] == 'Cellular'){
					$agent_cell = $agent['ContactPhoneAreaCode_3']."-".$agent['ContactPhoneNumber_3'];
			}
			if ($agent['ContactAddlPhoneType_1'] == 'Fax'){
					$agent_fax = $agent['ContactPhoneAreaCode_1']."-".$agent['ContactPhoneNumber_1'];
			} elseif ($agent['ContactAddlPhoneType_2'] == 'Fax'){
					$agent_fax = $agent['ContactPhoneAreaCode_2']."-".$agent['ContactPhoneNumber_2'];
			} elseif ($agent['ContactAddlPhoneType_1'] == 'Fax'){
					$agent_fax = $agent['ContactPhoneAreaCode_3']."-".$agent['ContactPhoneNumber_3'];
			}
			
			$html .= sprintf( '<div class="post-agent rets-agent agent-%s agent-%s">', $id, $category_classes );

				$html .= '<div class="agent-info-wrap"><div class="row-fluid">';

					
					$html .= sprintf('<div class="span4"><img src="%s" alt="%s" width="" height="" class="alignleft" /></div>', 
									 $image_url, $agent['FullName'] );

					$html .= sprintf('<div class="span8"><h1 class="agent-name">%s</h1>', $agent['FullName'] );

					$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div>',
											$agent['OfficeName'], $office_address );
			
					$html .=  '<div class="contacts-list">';
					
						if ( !empty($office_phone) )
							$html .= sprintf( '<div class="office"><a href="tel:%s">%s</a> <small>(Office)</small></div>', 
											$this->phone_link($office_phone), $office_phone );
			
						if ( !empty($agent_cell) )
							$html .= sprintf( '<div class="office"><a href="tel:%s">%s</a> <small>(Cell)</small></div>', 
											$this->phone_link($agent_cell), $agent_cell );
			
						if ( !empty($agent_fax) )
							$html .= sprintf( '<div class="office">%s <small>(Fax)</small></div>', 
											$agent_fax );
			
					$html .=  '</div></div>';

				$html .= '</div></div>';

				// Used for testing agent properties. Shortcode is the next function class below.
				// Remove administrator check when ready to go live.
				if( current_user_can('administrator') ) {
					
					$html .= '<div class="row-fluid"><div class="span12"><div class="agent-properties-wrap">';

						// Output property listings for agent via next shortcode built below
						$html .= do_shortcode(' [rets_agent_listings agent_id="'. $id .'" class="agent-properties"] ');

					$html .= '</div></div></div>';

				}

			$html .= '</div>';

		}

		return $html;

	}
	
	public function phone_link( $string ) {
		
		$slug = preg_replace( '/\D/', '', $string );
		
		return $slug;
		
	} // end phone_link

}
new Rets_Agent();



// Creates property listings for a single agent
// This can also display all listings for a single agent by passing their agent ID number into the "agent_id" shortcode parameter.
class Rets_Agent_Listings {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_agent_listings', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'agent_id' => '',
				'class' => '',
				'columns' => 3,
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
		
		$query = "
			SELECT Property_RESI.MLNumber,
			Property_RESI.ListingPrice,
			Property_RESI.imagepref,
			Property_RESI.StreetNumber,
			Property_RESI.StreetDirection,
			Property_RESI.StreetName,
			Property_RESI.StreetSuffix,
			Property_RESI.City,
			Property_RESI.State,
			Property_RESI.ZipCode,
			Property_RESI.Bedrooms,
			Property_RESI.Bathrooms
			FROM Property_RESI
			WHERE Status = 'Active'
			AND ShowAddressToPublic = 1
			AND PublishToInternet = 1
			AND ListingAgentNumber = {$agent_id}
		";
		
		$listings_query = new Rets_DB();
		
		$listings = $listings_query->select( $query );
		
		//print_r( $listings );
		
		if( $listings ) {
			
			$total_listings = count( $listings );
			$total_text = $total_listings == 1 ? 'Listing' : 'Listings';
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-listings rets-agent-listings '. $class .'"><div class="custom-posts-container clearfix">';
			
				$html .= '<div class="total-listings">'. number_format( $total_listings ) .' '. $total_text .'</div>';
			
				foreach( $listings as $listing ) {
					
					if( !empty( $listing['imagepref'] ) ) {
						$has_image_class = 'with-image';
						$image_url = home_url() .'/_retsapi/imagesProperties/'. $listing['imagepref'];
					} else {
						$has_image_class = 'without-image';
						$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
					}
					
					$address1 = $listing['StreetNumber'] .' '. rets_get_short_direction( $listing['StreetDirection'] ) .' '. $listing['StreetName'] .' '. $listing['StreetSuffix'];
					
					$address2 = $listing['City'] .', '. $listing['State'] .' '. $listing['ZipCode'];
					
					$full_address = $address1 .' '. $address2;
					
					$permalink = 'http://bendhomes.idxbroker.com/idx/details/listing/a098/'. $listing['MLNumber'] .'/'. sanitize_title( $full_address );
					
					// Begin agent output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $has_image_class );
					
						$html .= sprintf( '<figure class="custom-post-image image-listing-image-%s"><a href="%s"><img src="%s" width="" height="" alt="" /></a></figure>', 
								$count, $permalink, $image_url );

						$html .= sprintf( '<h4 class="custom-post-title"><a href="%s"><div>%s</div><div>%s</div></a></h4>', 
								$permalink, $address1, $address2 );
					
						$html .= sprintf( '<h5 class="property-price">%s</h5>', number_format($listing['ListingPrice']) );

						$html .= sprintf( '<div class="listing-meta listing-beds">%s Bedrooms</div><div class="listing-meta listing-baths">%s Bathrooms</div>', 
								floatval($listing['Bedrooms']), floatval($listing['Bathrooms']) );
					
					$html .= '</div></div>';
					// End agent ouput
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
			
			$html .= '</div></div>';
			
		}
		
		return $html;
		
	}
	
}
new Rets_Agent_Listings();



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

		$searchString = '';
		$searchString = $_GET['search'];
		//$html .= 'serach: '.$searchString;

		if ($searchString == '') {
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
				Office_OFFI.featured,
				Office_OFFI.images
				FROM Office_OFFI
				WHERE IsActive = 'T' AND featured = 1
				{$sort_order}
			";
		} else {
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
				Office_OFFI.featured,
				Office_OFFI.images
				FROM Office_OFFI
				WHERE IsActive = 'T' AND Office_OFFI.OfficeName LIKE '%{$searchString}%'
				{$sort_order}
			";
		}

		$companies_query = new Rets_DB();
		
		$companies = $companies_query->select( $query );
		
		//print_r( $companies);
		
		if( $companies ) {
			
			$total_companies = count( $companies );
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-companies rets-companies"><div class="custom-posts-container clearfix">';
			
				
			
				if( empty( $show_search ) ) {
			
					$html .= '<div class="custom-search-wrap">';
						$html .= '
							<form role="search" action="'. site_url('/') .'/companies" method="get" id="searchform">
								<input type="text" class="search-field" name="search" placeholder="Find an office"/>
								<!--<input type="hidden" name="post_type" value="offices" />-->
								<input type="submit" class="btn real-btn" alt="Search" value="Search" />
							</form>
						';
					$html .= '</div>';

				}

				$current_url = home_url() .''. $_SERVER['PHP_SELF'];
				$html .= '<div style="padding: 0 10px; color: #999;">'. number_format( $total_companies ) .' Total Companies</div>';
			
				foreach( $companies as $company ) {
										
					$category_classes = $company['featured'] == 1 ? 'featured' : 'not-featured';
					
					if( !empty( $company['images'] ) ) {
						$has_image_class = 'with-image';
						$image_url = home_url() .'/_retsapi/imagesOffices/'. $company['images'];
					} else {
						$has_image_class = 'without-image';
						$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
					}
					
					$office_address = $company['StreetAddress'] .'<br>'. $company['StreetCity'] .', '. $company['StreetState'] .' '. $agent['StreetZipCode'];
					
					$permalink = home_url() .'/'. $linkto .'/?company='. $this->create_slug( $company['OfficeName'] ) .'&id='. $company['OfficeNumber'];
					
					// Begin agent output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $class, $has_image_class, $category_classes );
					
						$html .= sprintf( '<figure class="custom-post-image image-company-image-%s"><a href="%s"><img src="%s" width="" height="" alt="%s" /></a></figure>', 
								$count, $permalink, $image_url, $company['OfficeName'] );

					
						$html .= sprintf( '<div class="extra-meta company-meta"><div><h3>%s</h3><div>%s</div></div><a href="tel:%s">%s</a></div>', 
									$company['OfficeName'], $office_address, $this->phone_link( $company['OfficePhoneComplete'] ), $company['OfficePhoneComplete'] );
					
						$html .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', $permalink );
					
					$html .= '</div></div>';
					// End agent ouput
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
			
			$html .= '</div></div>';
			
		}
		
		return $html;
		
    } // end render
	
	public function create_slug( $string ) {
				
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
	   
		return $slug;
		
	} // end create_slug
	
	public function phone_link( $string ) {
		
		$slug = preg_replace( '/\D/', '', $string );
		
		return $slug;
		
	} // end phone_link
	
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
				'office_number' => ''
			), $args
		);

		extract( $defaults );
		
		$id = !empty( $_GET['id'] ) ? $_GET['id'] : $office_number;
		$id = mysql_real_escape_string( floatval( $id ) );
	
		$query = "
			SELECT * FROM Office_OFFI 
			WHERE OfficeNumber = {$id}
		";

		$company_query = new Rets_DB();
		
		$company = $company_query->select( $query )[0];
		
		if( $company ) {
			
			//print_r( $company );
			
			$company_featured = $company['featured'];
			$category_classes = $company['featured'] == 1 ? 'featured' : 'not-featured';

			$office_address = $company['StreetAddress'] .'<br>'. $company['StreetCity'] .', '. $company['StreetState'] .' '. $company['StreetZipCode'];
			
			$html .= '<article class="about-company company-single clearfix"><div class="detail">';

			$html .= '<div class="row-fluid">';

			if( !empty( $company['images'] ) ) {
				$image_url = home_url() .'/_retsapi/imagesOffices/'. $company['images'];
				$html .= '<div class="span3"><figure class="company-pic">';
                $html .= '<a title="" href="">';
                $html .= '<img src="'.$image_url.'"/>';
                $html .= '</a></figure></div>';

                $html .= '<div class="span9">';
			} else {
				$html .= '<div class="span12">';
			}
			// Company Contact Info
            $company_office_phone = $company['OfficePhoneComplete'];
            $company_office_fax = $company['OfficeFax'];
            $company_office_address = $company['StreetAddress'] .' '. $company['StreetCity'] .', '. $company['StreetState'] .' '. $company['StreetZipCode'];

            if( !empty( $company_office_phone ) || !empty( $company_office_fax ) ) {

                $html .= '<h1 class="company-featured-'.$company_featured.'">'.$company['OfficeName'].'</h1>';
                                                
                if(!empty($company_office_address) && $company_featured == 1){
                    $html .= do_shortcode('<p>[MAP_LINK address="'. $company_office_address .'"]'. $company_office_address .'[/MAP_LINK]</p>');
                } else {
					$html .= '<p>'. $company_office_address .'</p>';
				}

                $html .= '<ul class="contacts-list">';
                if(!empty($company_office_phone)){

                    $html .= '<li class="office">';
					$html .= 'Office: '; 
					if( $company_featured == 1 ) {
						$html .= '<a href="tel:'. $this->phone_link( $company_office_phone ) .'">'. $company_office_phone .'</a>';
					} else {
						$html .= $company_office_phone;
					} 
                    $html .= '</li>';
                }
                if(!empty($company_office_fax)){
                    $html .= '<li class="fax">';
                    $html .= 'Fax: ';
                    $html .= $company_office_fax;
                    $html .= '</li>';
                }

                $html .= '</ul>';
            }

			$html .= '</div><!-- end span9 or span12 -->';
			$html .= '</div><!-- end .row-fluid -->';

			$html .= do_shortcode('[rets_company_agents]');

			$html .= '</div></article>';			
			
		}
		
		return $html;
		
	}
	
	public function phone_link( $string ) {
		
		$slug = preg_replace( '/\D/', '', $string );
		
		return $slug;
		
	} // end phone_link
	
}
new Rets_Company();



// Creates agents list on /agents page
class Rets_Company_Agents {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_company_agents', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'class' => '',
				'columns' => 4,
				'linkto' => 'agent'
			), $args
		);

		$id = !empty( $_GET['id'] ) ? $_GET['id'] : $office_number;
		$id = mysql_real_escape_string( floatval( $id ) );

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
			Office_OFFI.OfficeName,
			Office_OFFI.OfficePhoneComplete,
			Office_OFFI.StreetAddress,
			Office_OFFI.StreetCity,
			Office_OFFI.StreetState,
			Office_OFFI.StreetZipCode
			FROM ActiveAgent_MEMB
			LEFT JOIN Agent_MEMB on ActiveAgent_MEMB.MemberNumber = Agent_MEMB.MemberNumber
			LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber
			WHERE Office_OFFI.OfficeNumber = {$id}
		";
		
		$agents_query = new Rets_DB();
		
		$agents = $agents_query->select( $query );

		shuffle($agents);
				
		//print_r( $agents );
		
		if( $agents ) {
			
			$total_agents = count( $agents );
			
			$count = 1;

				$html .= '<div style="padding: 0 10px 10px; color: #999;">'. number_format( $total_agents ) .' Total Agents</div>';
			
			
				$html .= '<div class="agents-list-wrap clearfix">';
			
				foreach( $agents as $agent ) {
										
					$category_classes = $agent['featured'] == 1 ? 'featured' : 'not-featured';
					
					if( !empty( $agent['images'] ) ) {
						$has_image_class = 'width-image';
						$image_url = home_url() .'/_retsapi/imagesAgents/'. $agent['images'];
					} else {
						$has_image_class = 'without-image';
						$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
					}
										
					$permalink = home_url() .'/'. $linkto .'/?agent='. $this->create_slug( $agent['FullName'] ) .'&id='. $agent['MemberNumber'];
														
						/*$html .= '<div class="company-agent">';
						$html .= '<a class="company-agent-inner" href="'.$permalink.'">';
						$html .= '<figure class="agent-image">';
						$html .= '<img src="'.$image_url.'" alt="'.$agent['FullName'].'" width="" height="" />';
						$html .= '</figure>';                                                        
						$html .= '<div class="agent-name">'.$agent['FullName'].'</div>';
						$html .= '</a></div>';*/

					// Begin agent output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $class, $has_image_class, $category_classes );
					
						$html .= sprintf( '<figure class="custom-post-image image-agent-image-%s"><a href="%s"><img src="%s" width="" height="" alt="%s, for %s" /></a></figure>', 
								$count, $permalink, $image_url, $agent['FullName'], $agent['OfficeName'] );
					
						$html .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', $permalink, $agent['FullName'] );
					
						$html .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', $permalink );
					
					$html .= '</div></div>';
					// End agent ouput
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
				$html .= '</div>';
			
		}
		
		return $html;
		
    } // end render
	
	public function create_slug( $string ) {
				
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
	   
		return $slug;
		
	} // end create_slug
	
} 
new Rets_Company_Agents();



// Displays list of Open Houses linked to IDX page.
class Rets_Open_Houses {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_open_houses', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'class' => '',
				'columns' => 1,
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
		
		/*$query = "
			SELECT OpenHouse_OPEN.AgentFirstName,
			OpenHouse_OPEN.AgentLastName,
			OpenHouse_OPEN.StartDateTime,
			OpenHouse_OPEN.TimeComments,
			OpenHouse_OPEN.MLNumber,
			
			Property_RESI.MLNumber,
			Property_RESI.ListingPrice,
			Property_RESI.imagepref,
			Property_RESI.StreetNumber,
			Property_RESI.StreetDirection,
			Property_RESI.StreetName,
			Property_RESI.StreetSuffix,
			Property_RESI.City,
			Property_RESI.State,
			Property_RESI.ZipCode,
			Property_RESI.ShowAddressToPublic,
			Property_RESI.PublishToInternet
			
			FROM OpenHouse_OPEN
			LEFT JOIN Property_RESI on OpenHouse_OPEN.MLNumber = Property_RESI.MLNumber
			WHERE OpenHouse_OPEN.MLNumber = Property_RESI.MLNumber
			AND ShowAddressToPublic = 1
			AND PublishToInternet = 1
		";*/
		
		$query = "
			SELECT OpenHouse_OPEN.AgentFirstName,
			OpenHouse_OPEN.AgentLastName,
			OpenHouse_OPEN.StartDateTime,
			OpenHouse_OPEN.TimeComments,
			OpenHouse_OPEN.MLNumber,
			
			Property_RESI.MLNumber,
			Property_RESI.ListingPrice,
			Property_RESI.imagepref,
			Property_RESI.StreetNumber,
			Property_RESI.StreetDirection,
			Property_RESI.StreetName,
			Property_RESI.StreetSuffix,
			Property_RESI.City,
			Property_RESI.State,
			Property_RESI.ZipCode,
			Property_RESI.Bedrooms,
			Property_RESI.Bathrooms,
			Property_RESI.ShowAddressToPublic,
			Property_RESI.PublishToInternet
			
			FROM OpenHouse_OPEN
			LEFT OUTER JOIN Property_RESI on OpenHouse_OPEN.MLNumber = Property_RESI.MLNumber
			WHERE OpenHouse_OPEN.MLNumber = Property_RESI.MLNumber
			AND ShowAddressToPublic = 1
			AND PublishToInternet = 1
		";
		
		$openhouses_query = new Rets_DB();
		
		$openhouses = $openhouses_query->select( $query );
		
			//print_r( $query );
			//print_r( $openhouses );
		
		
		$openhouses_array = $this->format_rets_query( $openhouses );
		
			//print_r($openhouses_array);
		
		
		if( $openhouses_array ) {
			
			$total_listings = count( $openhouses_array );
			$total_text = $total_listings == 1 ? 'Open House' : 'Open Houses';
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper rets-open-houses '. $class .'"><div class="custom-posts-container clearfix">';
			
				$html .= '<div class="total-listings">'. number_format( $total_listings ) .' '. $total_text .'</div>';
			
				foreach( $openhouses_array as $key => $openhouse ) {
					
					if( !empty( $openhouse['imagepref'] ) ) {
						$has_image_class = 'with-image';
						$image_url = home_url() .'/_retsapi/imagesProperties/'. $openhouse['imagepref'];
					} else {
						$has_image_class = 'without-image';
						$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
					}
					
					$address1 = $openhouse['StreetNumber'] .' '. rets_get_short_direction( $openhouse['StreetDirection'] ) .' '. $openhouse['StreetName'] .' '. $openhouse['StreetSuffix'];
					
					$address2 = $openhouse['City'] .', '. $openhouse['State'] .' '. $openhouse['ZipCode'];
					
					$full_address = $address1 .' '. $address2;
					
					$permalink = 'http://bendhomes.idxbroker.com/idx/details/listing/a098/'. $openhouse['MLNumber'] .'/'. sanitize_title( $full_address );
					
					// Begin agent output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $has_image_class );
					
						$html .= sprintf( '<figure class="custom-post-image image-listing-image-%s"><a href="%s"><img src="%s" width="" height="" alt="" /></a></figure>', 
								$count, $permalink, $image_url );

						$html .= sprintf( '<h4 class="custom-post-title"><a href="%s"><div>%s</div><div>%s</div></a></h4>', 
								$permalink, $address1, $address2 );
					
						$html .= sprintf( '<h5 class="property-price">%s</h5>', number_format($openhouse['ListingPrice']) );

						$html .= sprintf( '<div class="listing-meta listing-beds">%s Bedrooms</div><div class="listing-meta listing-baths">%s Bathrooms</div>', 
								floatval($openhouse['Bedrooms']), floatval($openhouse['Bathrooms']) );
					
						$html .= '<div class="open-house-meta">';
					
							$timecount = 0;
					
							foreach ( $openhouse['Time'. $timecount] as $key2 => $time ) {
						
								$date = new DateTime( $time['Date'] );
								$date_format = $date->format('M, jS');

								$html .= sprintf( '<span class="time time-'. $timecount .'">%s, %s</span>', $date_format, $time['Time'] );

								$timecount++;
							}
						
						$html .= '</div>';
					
					$html .= '</div></div>';
					// End agent ouput
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
			
			$html .= '</div></div>';
			
		}
		
		return $html;
		
	}
	
	// Format $query into better array to handle multiple dates/times for same property
	public function format_rets_query( $query_array ) {
			
		$result = [];
		
		foreach( $query_array as $value ) {
						
			$mls_num = $value['MLNumber'];
			
			if( isset( $result[$mls_num] ) )
				//$index = ( ( count( $result[$mls_num] ) - 1 ) / 2 ) + 1;
				$index = count( $result[$mls_num] ) - 13;
			else
				$index = 0;

			$result[$mls_num]['MLNumber'] = $mls_num;
			$result[$mls_num]['AgentName'] = $value['AgentFirstName'] .' '. $value['AgentLastName'];
			$result[$mls_num]['ListingPrice'] = $value['ListingPrice'];
			$result[$mls_num]['imagepref'] = $value['imagepref'];
			$result[$mls_num]['StreetNumber'] = $value['StreetNumber'];
			$result[$mls_num]['StreetDirection'] = $value['StreetDirection'];
			$result[$mls_num]['StreetName'] = $value['StreetName'];
			$result[$mls_num]['StreetSuffix'] = $value['StreetSuffix'];
			$result[$mls_num]['City'] = $value['City'];
			$result[$mls_num]['State'] = $value['State'];
			$result[$mls_num]['ZipCode'] = $value['ZipCode'];
			$result[$mls_num]['Bedrooms'] = $value['Bedrooms'];
			$result[$mls_num]['Bathrooms'] = $value['Bathrooms']; // 13th array item in list so this total goes above in $index as 13
			$result[$mls_num]['Time' . $index] = [
				'Date' => $value['StartDateTime'],
				'Time' => $value['TimeComments']
			];
			
		}
		
		return array_values( $result );
		
	}
	
}
new Rets_Open_Houses();

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
		$searchString = trim( $_GET['search'] );
		
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
				Office_OFFI.StreetZipCode,
				Office_OFFI.featured
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
				Office_OFFI.StreetZipCode,
				Office_OFFI.featured
				FROM ActiveAgent_MEMB
				LEFT JOIN Agent_MEMB on ActiveAgent_MEMB.MemberNumber = Agent_MEMB.MemberNumber
				LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber
				WHERE ActiveAgent_MEMB.OfficeNumber <> 99999
				AND ActiveAgent_MEMB.FullName LIKE '%{$searchString}%'
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
		
		if( $agents ) {
			
			$total_agents = count( $agents );
			
			$html .= '<div style="padding: 0 10px; color: #999;">'. number_format( $total_agents) .' Total Agents</div>';
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-agent rets-agents"><div class="custom-posts-container clearfix">';

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
					
					if( !empty( $agent['images'] ) && $agent['featured'] == 1 ) {
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
						
		} else {
			
			$html .= '<div>Sorry, your search returned 0 results. Please try modifying your search and try again.</div>';
			
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
		$id = trim( floatval( $id ) );

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
			Office_OFFI.DisplayName,
			Office_OFFI.OfficePhoneComplete,
			Office_OFFI.StreetAddress,
			Office_OFFI.StreetCity,
			Office_OFFI.StreetState,
			Office_OFFI.StreetZipCode,
			Office_OFFI.featured
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

			$category_classes = $agent['featured'] == 1 ? 'featured' : 'not-featured';
			
			if( !empty( $agent['theImage'] ) ) {
				$has_image_class = 'width-image';
				$image_url = home_url() .'/_retsapi/imagesAgents/'. $agent['theImage'];
			} else {
				$has_image_class = 'without-image';
				$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
			}

			$office_name = $agent['DisplayName'] != '' ? $agent['DisplayName'] : $agent['OfficeName'];
			
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

					if( $agent['featured'] == 1 ) {
						$html .= sprintf('<div class="span4"><img src="%s" alt="%s" width="" height="" class="alignleft" /></div>', 
										 $image_url, $agent['FullName'] );
					}

					$html .= sprintf('<div class="span8"><h1 class="agent-name">%s</h1>', $agent['FullName'] );

					$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div>',
											$office_name, $office_address );
			
					$html .=  '<div class="contacts-list">';
					
						if ( !empty($office_phone) )
							$html .= sprintf( '<div class="office"><a href="tel:%s">%s</a> <small>(Office)</small></div>', 
											$this->phone_link($office_phone), $office_phone );
			
						if( $agent['featured'] == 1 ) {
							if ( !empty($agent_cell) )
								$html .= sprintf( '<div class="office"><a href="tel:%s">%s</a> <small>(Cell)</small></div>', 
												$this->phone_link($agent_cell), $agent_cell );

							if ( !empty($agent_fax) )
								$html .= sprintf( '<div class="office">%s <small>(Fax)</small></div>', 
												$agent_fax );
						}
			
					$html .=  '</div></div>';

				$html .= '</div></div>';
			
				if( $agent['featured'] == 1 ) {		
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
		
		$agent_id = trim( floatval($agent_id) );
		
		$query = "
			SELECT 
			RESI.MLNumber,
			RESI.ListingPrice,
			RESI.imagepref,
			RESI.StreetNumber,
			RESI.StreetDirection,
			RESI.StreetName,
			RESI.StreetSuffix,
			RESI.City,
			RESI.State,
			RESI.ZipCode,
			RESI.Bedrooms,
			RESI.Bathrooms
			
			FROM Property_RESI RESI
			WHERE Status = 'Active'
			AND ShowAddressToPublic = 1
			AND PublishToInternet = 1
			AND ListingAgentNumber = {$agent_id}
		";
		
		$listings_query = new Rets_DB();
		
		$listings = $listings_query->select( $query );
		
		if(current_user_can('administrator')) {
			print_r( $listings );
		}
		
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
		$searchString = trim( $_GET['search'] );
		//$html .= 'serach: '.$searchString;

		if ($searchString == '') {
			$query = "
				SELECT OFFI.IsActive,
				OFFI.MLSID,
				OFFI.OfficeName,
				OFFI.OfficeNumber,
				OFFI.OfficePhone,
				OFFI.OfficePhoneComplete,
				OFFI.StreetAddress,
				OFFI.StreetCity,
				OFFI.StreetState,
				OFFI.StreetZipCode,
				OFFI.OfficeDescription,
				OFFI.DisplayName,
				OFFI.featured,
				OFFI.images
				FROM Office_OFFI OFFI
				WHERE IsActive = 'T' AND featured = 1
				{$sort_order}
			";
		} else {
			$query = "
				SELECT OFFI.IsActive,
				OFFI.MLSID,
				OFFI.OfficeName,
				OFFI.OfficeNumber,
				OFFI.OfficePhone,
				OFFI.OfficePhoneComplete,
				OFFI.StreetAddress,
				OFFI.StreetCity,
				OFFI.StreetState,
				OFFI.StreetZipCode,
				OFFI.OfficeDescription,
				OFFI.DisplayName,
				OFFI.featured,
				OFFI.images
				FROM Office_OFFI OFFI
				WHERE IsActive = 'T' AND OfficeName LIKE '%{$searchString}%'
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
					
					$office_name = $company['DisplayName'] == NULL ? $company['OfficeName'] : $company['DisplayName'];
					
					if( !empty( $company['images'] ) ) {
						$has_image_class = 'with-image';
						// If image has full url, i.e. from wordpress media gallery, use it
						if( filter_var( $company['images'], FILTER_VALIDATE_URL) ) {
							$image_url = $company['images'];
						} else {
							// Otherwise use the image in /_retsapi folder
							$image_url = home_url() .'/_retsapi/imagesOffices/'. $company['images'];
						}
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
									$office_name, $office_address, $this->phone_link( $company['OfficePhoneComplete'] ), $company['OfficePhoneComplete'] );
					
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
		$id = trim( floatval( $id ) );
	
		$query = "
			SELECT * FROM Office_OFFI 
			WHERE OfficeNumber = {$id}
		";

		$company_query = new Rets_DB();
		
		$company = $company_query->select( $query )[0];
		
		if( $company ) {
			
			//print_r( $company );
			
			// Is company featured
			$company_featured = $company['featured'];
			$category_classes = $company['featured'] == 1 ? 'featured' : 'not-featured';
			
			// Office name
			$office_name = $company['DisplayName'] == NULL ? $company['OfficeName'] : $company['DisplayName'];

			// Office address
			$office_address = $company['StreetAddress'] .'<br>'. $company['StreetCity'] .', '. $company['StreetState'] .' '. $company['StreetZipCode'];
			
			// Office Contact Info
            $company_office_phone = $company['OfficePhoneComplete'];
            $company_office_fax = $company['OfficeFax'];
            $company_office_address = $company['StreetAddress'] .' '. $company['StreetCity'] .', '. $company['StreetState'] .' '. $company['StreetZipCode'];
			
			
			// Start HTML output
			$html .= '<article class="about-company company-single clearfix"><div class="detail">';

				$html .= '<div class="row-fluid">';

					if( !empty( $company['images'] ) ) {
						
						// If image comes from wordpress media gallery, i.e. has a full url, use it
						if( filter_var( $company['images'], FILTER_VALIDATE_URL) ) {
							$image_url = $company['images'];
						} else {
							// Otherwise use the image from /_retsapi folder
							$image_url = home_url() .'/_retsapi/imagesOffices/'. $company['images'];
						}
						
						$html .= '<div class="span3">';
							$html .= '<figure class="company-pic"><img src="'.$image_url.'"/></figure>';
						$html .= '</div>';

						$html .= '<div class="span9">';
					} else {
						$html .= '<div class="span12">';
					}

					$html .= '<h1 class="company-featured-'.$company_featured.'">'.$office_name.'</h1>';

					if(!empty($company_office_address) && $company_featured == 1){
						$html .= do_shortcode('<p>[MAP_LINK address="'. $company_office_address .'"]'. $company_office_address .'[/MAP_LINK]</p>');
					} else {
						$html .= '<p>'. $company_office_address .'</p>';
					}

					if( !empty( $company_office_phone ) || !empty( $company_office_fax ) ) {

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
			
				// Show office description if office is featured
				if( $company_featured == 1 && !empty( $company['OfficeDescription'] ) ) {
					$html .= sprintf( '<div class="row-fluid clearfix office-description">%s</div>', 
									 wpautop( $company['OfficeDescription'] ) );
				}

				// Show agents list if office is featured
				if( $company_featured == 1 ) {
					$html .= do_shortcode('[rets_company_agents]');
				}

			$html .= '</div></article>';
			// End HTML output
			
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
		$id = trim( floatval( $id ) );

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
				'limit' => '',
				'show_total' => '',
				'show_companies' => '',
				'company_page' => 'company',
				'agent_page' => 'agent'
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
			SELECT 
			OPEN.AgentFirstName,
			OPEN.AgentLastName,
			OPEN.StartDateTime,
			OPEN.TimeComments,
			OPEN.MLNumber,
			OPEN.ListingAgentNumber,
			
			RESI.MLNumber,
			RESI.ListingPrice,
			RESI.imagepref,
			RESI.StreetNumber,
			RESI.StreetDirection,
			RESI.StreetName,
			RESI.StreetSuffix,
			RESI.City,
			RESI.State,
			RESI.ZipCode,
			RESI.Bedrooms,
			RESI.Bathrooms,
			RESI.ShowAddressToPublic,
			RESI.PublishToInternet,
			
			OFFI.OfficeNumber,
			OFFI.OfficeName,
			OFFI.featured,
			OFFI.images
			
			FROM OpenHouse_OPEN OPEN, Property_RESI RESI, Office_OFFI OFFI
			WHERE OPEN.MLNumber = RESI.MLNumber
			AND RESI.Status = 'Active'
			AND OPEN.ListingOfficeNumber = OFFI.OfficeNumber
			AND ShowAddressToPublic = 1
			AND PublishToInternet = 1
		";
		
		$openhouses_query = new Rets_DB();
		
		$openhouses = $openhouses_query->select( $query );
		
		//print_r($openhouses);
		
		$openhouses_array = $this->format_rets_query( $openhouses );
		
		//print_r($openhouses_array);
		
		if( !empty( $limit ) ) {
			$openhouses_array = array_slice( $openhouses_array, 0, $limit );
		}
				
		if( $openhouses_array ) {
			
			$total_listings = count( $openhouses_array );
			$total_text = $total_listings == 1 ? 'Open House Available' : 'Open Houses Available';
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper rets-open-houses '. $class .'"><div class="custom-posts-container clearfix">';
			
				if( empty( $show_total ) )
					$html .= sprintf( '<div class="total-listings" style="margin-top:20px;margin-bottom:10px;">%s %s</div>', 
								 number_format( $total_listings ), $total_text );
			
				foreach( $openhouses_array as $openhouse ) {
					
					// Get Property Image
					if( !empty( $openhouse['PropertyImage'] ) ) {
						$has_image_class = 'with-image';
						$image_url = home_url() .'/_retsapi/imagesProperties/'. $openhouse['PropertyImage'];
					} else {
						$has_image_class = 'without-image';
						$image_url = get_stylesheet_directory_uri(). '/images/blank-profile-placeholder.jpg';
					}
					
					// Get Address
					$address1 = $openhouse['StreetNumber'] .' '. rets_get_short_direction( $openhouse['StreetDirection'] ) .' '. $openhouse['StreetName'] .' '. $openhouse['StreetSuffix'];
					
					$address2 = $openhouse['City'] .', '. $openhouse['State'] .' '. $openhouse['ZipCode'];
					
					$full_address = $address1 .' '. $address2;
					
					// Get Date and Times
					$timecount_end = sizeof( $openhouse ) - 19; // Use same number in format_rets_query function below.
					$dates_times_html = '';
					$date_times_url = '';
					for( $i = 0; $i < $timecount_end; $i++ ) {

						$date = new DateTime( $openhouse['DateAndTime'. $i]['Date'] );
						$date_format = $date->format('M jS');
						$time = $openhouse['DateAndTime'. $i]['Time'];

						$dates_times_html .= sprintf('<div class="datetime datetime-%s">%s, %s</div>', $i, $date_format, $time );
					}
					
					// Get Office Name
					$office_name = $openhouse['DisplayName'] != NULL ? $openhouse['DisplayName'] : $openhouse['OfficeName'];
					
					// Get Link to Property
					$permalink = sprintf( 'http://bendhomes.idxbroker.com/idx/details/listing/a098/%s/%s/',
										 $openhouse['MLNumber'], sanitize_title( $full_address ) ) ;
					
					// Get Company/Agent Info Box for Featured and Non-Featured
					$office_meta = '';
					$company_image = '';
					$office_featured_class = $openhouse['featured'] == 1 ? 'featured' : '';
					if( !empty( $openhouse['OfficeImage'] ) ) {
						// If image comes from wordpress media gallery, i.e. has a full url, use it
						if( filter_var( $openhouse['OfficeImage'], FILTER_VALIDATE_URL) ) {
							$company_image_url = $openhouse['OfficeImage'];
						} else {
							// Otherwise use the image from /_retsapi folder
							$company_image_url = home_url() .'/_retsapi/imagesOffices/'. $openhouse['OfficeImage'];
						}						
						$company_image = sprintf( '<img src="%s" alt="%s" class="company-image" />', 
												 $company_image_url, $office_name );
					}
					
					$company_url = sprintf( '%s/%s/?company=%s&id=%s', 
											home_url(), $company_page, $this->create_slug( $office_name ), $openhouse['OfficeNumber'] );
					
					$company_full_link = sprintf( '<a href="%s">%s</a>', 
												 $company_url, $office_name );
					
					$agent_url = sprintf( '%s/%s/?agent=%s&id=%s', 
											home_url(), $agent_page, $this->create_slug( $openhouse['AgentName'] ), $openhouse['AgentNumber'] );
					
					$agent_full_link = sprintf( '<a href="%s">%s</a>', 
											   $agent_url, $openhouse['AgentName'] );
					
					$office_meta .= '<div class="office '. $office_featured_class .'">';
						if( $openhouse['featured'] == 1 ) {
							$office_meta .= sprintf( '%s<div class="office-info">Listing Courtesy of %s<div>Agent: %s</div></div>', 
													$company_image, $company_full_link, $agent_full_link );
						} else {
							$office_meta .= sprintf( '<div class="office-info">Listing Courtesy of %s</div>', $office_name );
						}
					$office_meta .= '</div>';
					
					
					// Begin Open House Output
					$html .= sprintf( '<div class="custom-post custom-post-%s open-house %s %s"><div class="custom-post-item row-fluid">', 
							$count, $cols, $has_image_class );
					
						if( $columns == 1 ) $html .= '<div class="span6">';
					
						$html .= sprintf( '<figure class="custom-post-image image-listing-image-%s"><a href="%s"><img src="%s" width="" height="" alt="" /></a></figure>', 
								$count, $permalink, $image_url );
					
						if( $columns == 1 ) $html .= '</div><div class="span6">';
					
						$html .= sprintf( '<h4 class="custom-post-title"><a href="%s"><div class="adr1">%s</div><div class="adr2">%s</div></a></h4>', 
								$permalink, $address1, $address2 );

						$html .= sprintf( '<h5 class="property-price">%s</h5>', number_format($openhouse['ListingPrice']) );

						$html .= sprintf( '<div class="listing-meta listing-beds">%s Bedrooms</div><div class="listing-meta listing-baths">%s Bathrooms</div>', 
								floatval($openhouse['Bedrooms']), floatval($openhouse['Bathrooms']) );

						if( $columns == 1 ) {
							$html .= sprintf( '<div id="openhousemeta-%s" class="open-house-meta collapse">%s</div>', 
											 $count, $dates_times_html );
							if( $openhouse['DateAndTime3'] )
								$html .= sprintf( '<button type="button" class="openhouse-btn collapsed" data-toggle="collapse" data-target="#openhousemeta-%s">View More Times</button>', $count );
						}
						
						if( $columns == 1 ) $html .= '</div>';
					
						$html .= sprintf( '<div class="clearfix"></div><div class="office-meta-wrap">%s</div>', $office_meta );
					
					$html .= '</div></div>';
					// End open house ouput
					
					
					$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;
					
				}
			
			$html .= '</div></div>';
						
		}
		
		return $html; // Finally display results here.
		
	}
	
	// Supporting functions below
	
	// Format $query into better array to handle multiple dates/times for same property
	private function format_rets_query( $query_array ) {
			
		$result = [];
		
		foreach( $query_array as $value ) {
						
			$mls_num = $value['MLNumber'];
			
			if( isset( $result[$mls_num] ) )
				//$index = ( ( count( $result[$mls_num] ) - 1 ) / 2 ) + 1;
				$index = count( $result[$mls_num] ) - 19;
			else
				$index = 0;

			$result[$mls_num]['MLNumber'] = $mls_num;
			$result[$mls_num]['AgentName'] = $value['AgentFirstName'] .' '. $value['AgentLastName'];
			$result[$mls_num]['AgentNumber'] = $value['ListingAgentNumber'];
			$result[$mls_num]['ListingPrice'] = $value['ListingPrice'];
			$result[$mls_num]['PropertyImage'] = $value['imagepref'];
			$result[$mls_num]['StreetNumber'] = $value['StreetNumber'];
			$result[$mls_num]['StreetDirection'] = $value['StreetDirection'];
			$result[$mls_num]['StreetName'] = $value['StreetName'];
			$result[$mls_num]['StreetSuffix'] = $value['StreetSuffix'];
			$result[$mls_num]['City'] = $value['City'];
			$result[$mls_num]['State'] = $value['State'];
			$result[$mls_num]['ZipCode'] = $value['ZipCode'];
			$result[$mls_num]['Bedrooms'] = $value['Bedrooms'];
			$result[$mls_num]['Bathrooms'] = $value['Bathrooms'];
			$result[$mls_num]['OfficeNumber'] = $value['OfficeNumber'];
			$result[$mls_num]['OfficeName'] = $value['OfficeName'];
			$result[$mls_num]['DisplayName'] = $value['DisplayName'];
			$result[$mls_num]['featured'] = $value['featured'];
			$result[$mls_num]['OfficeImage'] = $value['images'];  // 19th item in array so enter this number above
			$result[$mls_num]['DateAndTime'. $index] = [
				'Date' => $value['StartDateTime'],
				'Time' => $value['TimeComments']
			];
			
		}
		
		return array_values( $result );
		
	}
	
	private function create_slug( $string ) {
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
		return $slug;
	}
	
}
new Rets_Open_Houses();


// Display Sold/Pending listings with search form
class Rets_Sold_Pending {
	
	public static $args;
	
    public function __construct() {
		
        add_shortcode('rets_sold_pending', array($this, 'render'));
		
    }
     
    public function render( $args ) {
		
		$html = '';
		$defaults = shortcode_atts(
			array(
				'class' => '',
				'columns' => 1,
				'limit' => '50',
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
			SELECT RESI.MLNumber,
			RESI.ListingPrice,
			RESI.imagepref,
			RESI.StreetNumber,
			RESI.StreetDirection,
			RESI.StreetName,
			RESI.StreetSuffix,
			RESI.City,
			RESI.State,
			RESI.ZipCode
			FROM Property_RESI RESI
			WHERE (Status = 'Sold' OR Status = 'Pending)
			AND ShowAddressToPublic = 1
			AND PublishToInternet = 1
		";
		
		$sp_query = new Rets_DB();
		
		$sp_array = $sp_query->select( $query );
		
		if( !empty( $limit ) ) {
			$sp_array = array_slice( $sp_array, 0, $limit );
		}
		
		if( $sp_array ) {
			
			$total = count( $sp_array );
			$total_text = $total == 1 ? 'Sold/Pending Property' : 'Sold/Pending Properties';
		}
		
		return $html;
		
	}

}
new Rets_Sold_Pending();

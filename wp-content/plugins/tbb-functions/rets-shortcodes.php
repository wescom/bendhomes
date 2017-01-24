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
			
			$html .= '<div class="custom-posts-wrapper post-agent"><div class="custom-posts-container clearfix">';
			
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
			
			
			
			$html .= sprintf( '</div>%s</div>', $this->pagination( $limit, $total_agents, '3' ) );
			
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
	
	// http://www.phpfreaks.com/tutorial/basic-pagination
	public function pagination( $per_page, $total_pages, $range ) {
		
		$html = '';
		$current_url = $this->get_current_url();
		
		if (isset($_GET['page']) && is_numeric($_GET['page'])) {
		   $page = (int) $_GET['page'];
		} else {
		   $page = 1;
		}
		
		if( $page > $total_pages ) {
			$page = $total_pages;
		}
		
		if( $page < 1 ) {
			$page = 1;
		}
		
		$offset = ( $page - 1 ) * $per_page;
		
		if( $page > 1 ) {
			$html .= '<a href="'. $current_url .'?page=1"><<</a>';
			$prev_page = $page - 1;
			$html .= '<a href="'. $current_url .'?page=$prev_page"><</a>';
		}
		
		for ($x = ($page - $range); $x < (($page + $range) + 1); $x++) {
		   // if it's a valid page number...
		   if (($x > 0) && ($x <= $total_pages)) {
			  // if we're on current page...
			  if ($x == $page) {
				 // 'highlight' it but don't make a link
				 $html .= ' [<b>'. $x .'</b>] ';
			  // if not current page...
			  } else {
				 // make it a link
				 $html .= ' <a href="'. $current_url .'?page='. $x .'">'. $x .'</a> ';
			  } // end else
		   } // end if 
		}
		
		if ($page != $total_pages) {
		   $next_page = $page + 1;
			// echo forward link for next page 
		   $html .= ' <a href="'. $current_url .'?page='. $next_page .'">></a> ';
		   // echo forward link for lastpage
		   $html .= '<a href="'. $current_url .'?page='. $total_pages .'">>></a> ';
		}
		
		return $html;
		
	}
	
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
						
				$html .= '<div class="row-fluid"><div class="span12"><div class="agent-properties-wrap">';
					
					$html .= sprintf('<h3>Properties Listed By </h3>', $agent['FullName'] );

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
				Office_OFFI.featured
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
				Office_OFFI.featured
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
			
			$html .= '<div class="custom-posts-wrapper post-agent"><div class="custom-posts-container clearfix">';
			
				
			
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
						$has_image_class = 'width-image';
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
			
			
			
			//$html .= sprintf( '</div>%s</div>', 'Pagination goes here' );
			
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
				$html .= '<div class="span3"><figure class="agent-pic">';
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

                $html .= '<h5 class="company-featured-'.$company_featured.'">'.$company['OfficeName'].'</h5>';
                                                
                if(!empty($company_office_address) && $company_featured == 1){
                    $html .= do_shortcode('<p>[MAP_LINK address="'. $company_office_address .'"]'. $company_office_address .'[/MAP_LINK]</p>');
                } else {
					$html .= '<p>'. $company_office_address .'</p>';
				}

                $html .= '<ul class="contacts-list">';
                if(!empty($company_office_phone)){

                    $html .= '<li class="office">';
					$html .= include( get_template_directory() . '/images/icon-phone.svg' ); _e('Office', 'framework');
					$html .= ':'; 
					if( $company_featured == 1 ) {
						$html .= '<a href="tel:'. str_replace("-", '', $company_office_phone) .'">'. $company_office_phone .'</a>';
					} else {
						$html .= $company_office_phone;
					} 
                    $html .= '</li>';
                }
                if(!empty($company_office_fax)){
                    $html .= '<li class="fax">';
                    $html .=  include( get_template_directory() . '/images/icon-printer.svg' ); _e('Fax', 'framework');
                    $html .= ':';
                    $html .= $company_office_fax;
                    $html .= '</li>';
                }

                $html .= '</ul>';
            }

			$html .= '</div><!-- end span9 or span12 -->';
			$html .= '</div><!-- end .row-fluid -->';

			$html .= do_shortcode('[rets_company_agents][/rets_company_agents]');

			$html .= '</div></article>';

			/*$html .= sprintf( '<div class="post-agent agent-%s agent-%s">', $id, $category_classes );
						
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
			
			$html .= '</div>';*/

			
			
		}
		
		return $html;
		
	}
	
	public function get_company_properties( $id ) {
		
	}
	
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
				'limit' => 50,
				'order' => '',
				'orderby' => '',
				'class' => '',
				'columns' => 3,
				'show_search' => '',
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

		if ($sort_order == '') {
			shuffle($agents);
		}
		
		//print_r( $agents );
		
		if( $agents ) {
			
			$total_agents = count( $agents );
			
			$count = 1;

				$html .= '<div style="padding: 0 10px; color: #999;">'. number_format( $total_agents ) .' Total Agents</div>';
			
			
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
					
					$office_address = $agent['StreetAddress'] .'<br>'. $agent['StreetCity'] .', '. $agent['StreetState'] .' '. $agent['StreetZipCode'];
					
					$permalink = home_url() .'/'. $linkto .'/?agent='. $this->create_slug( $agent['FullName'] ) .'&id='. $agent['MemberNumber'];
														
						$html .= '<div class="company-agent">';
						$html .= '<a class="company-agent-inner" href="'.$permalink.'">';
						$html .= '<figure class="agent-image">';
						$html .= '<img src="'.$image_url.'" alt="'.$agent['FullName'].'" width="" height="" />';
						$html .= '</figure>';                                                        
						$html .= '<div class="agent-name">'.$agent['FullName'].'</div>';
						$html .= '</a></div>';

					// Begin agent output
					/*$html .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $class, $has_image_class, $category_classes );
					
						$html .= sprintf( '<figure class="custom-post-image image-agent-image-%s"><a href="%s"><img src="%s" width="" height="" alt="%s, for %s" /></a></figure>', 
								$count, $permalink, $image_url, $agent['FullName'], $agent['OfficeName'] );
					
						$html .= sprintf( '<h4 class="custom-post-title"><a href="%s">%s</a></h4>', $permalink, $agent['FullName'] );
					
						$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
									$agent['OfficeName'], $office_address, $agent['OfficePhoneComplete'] );
					
						$html .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', $permalink );
					
					$html .= '</div></div>';*/
					// End agent ouput
					
					/*$clearfix_test = $count / $cols_per_row;
					if( is_int( $clearfix_test ) ) {
						$html .= '<div class="clearfix"></div>';
					}

					$count++;*/
					
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

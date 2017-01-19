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
				'limit' => 50,
				'order' => '',
				'orderby' => '',
				'class' => '',
				'columns' => 3,
				'show_search' => '',
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
		
		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		$startAt = $limit * ($page - 1);
		
		$agents_query = new Rets_DB();
		//$agents = $agents_query -> select("select * from ActiveAgent_MEMB");
		
		$agents = $agents_query->select("
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
		");
		
		//print_r( $agents );
		
		if( $agents ) {
			
			$total_agents = $agents->num_rows;
			
			$count = 1;
			
			$html .= '<div class="custom-posts-wrapper post-agent"><div class="custom-posts-container clearfix">';
			
				$html .= '<h4>'. $total_agents .'Total Agents</h4>';
			
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

				$current_url = home_url() .'/rets-agents/';
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
					
					// Begin agent output
					$html .= sprintf( '<div class="custom-post custom-post-%s %s %s %s %s"><div class="custom-post-item clearfix">', 
							$count, $cols, $class, $has_image_class, $category_classes );
					
						$html .= sprintf( '<figure class="custom-post-image image-agent-image-%s"><a href="%s"><img src="%s" width="" height="" alt="%s, for %s" /></a></figure>', 
								$count, '#', $image_url, $agent['FullName'], $agent['OfficeName'] );
					
						$html .= sprintf( '<h4 class="custom-post-title"><a href="">%s</a></h4>', $agent['FullName'] );
					
						$html .= sprintf( '<div class="extra-meta agent-meta"><div>%s<div>%s</div></div>%s</div>', 
									$agent['OfficeName'], $office_address, $agent['OfficePhoneComplete'] );
					
						$html .= sprintf( '<a class="more-details" href="%s">More Details <i class="fa fa-caret-right"></i></a>', '' );
					
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
		
    }
	
} 
new Rets_Agents();


class Paginator {
 
    private $_conn;
	private $_limit;
	private $_page;
	private $_query;
	private $_total;
	
	public function __construct( $conn, $query ) {
     
		$this->_conn = $conn;
		$this->_query = $query;

		$rs= $this->_conn->query( $this->_query );
		$this->_total = $rs->num_rows;

	}
	
	public function getData( $limit = 10, $page = 1 ) {
     
		$this->_limit   = $limit;
		$this->_page    = $page;

		if ( $this->_limit == 'all' ) {
			$query      = $this->_query;
		} else {
			$query      = $this->_query . " LIMIT " . ( ( $this->_page - 1 ) * $this->_limit ) . ", $this->_limit";
		}
		$rs             = $this->_conn->query( $query );

		while ( $row = $rs->fetch_assoc() ) {
			$results[]  = $row;
		}

		$result         = new stdClass();
		$result->page   = $this->_page;
		$result->limit  = $this->_limit;
		$result->total  = $this->_total;
		$result->data   = $results;

		return $result;
	}
	
	public function createLinks( $links, $list_class ) {
		if ( $this->_limit == 'all' ) {
			return '';
		}

		$last       = ceil( $this->_total / $this->_limit );

		$start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
		$end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

		$html       = '<ul class="' . $list_class . '">';

		$class      = ( $this->_page == 1 ) ? "disabled" : "";
		$html       .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';

		if ( $start > 1 ) {
			$html   .= '<li><a href="?limit=' . $this->_limit . '&page=1">1</a></li>';
			$html   .= '<li class="disabled"><span>...</span></li>';
		}

		for ( $i = $start ; $i <= $end; $i++ ) {
			$class  = ( $this->_page == $i ) ? "active" : "";
			$html   .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
		}

		if ( $end < $last ) {
			$html   .= '<li class="disabled"><span>...</span></li>';
			$html   .= '<li><a href="?limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>';
		}

		$class      = ( $this->_page == $last ) ? "disabled" : "";
		$html       .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';

		$html       .= '</ul>';

		return $html;
	}
 
}
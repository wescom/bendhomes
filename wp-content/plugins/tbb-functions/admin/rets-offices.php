<?php
// Offices admin page


/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display offices on a page, we first need to instantiate the class,
 * then call $ourInstance->prepare_items() to handle any data manipulation, then
 * finally call $ourInstance->display() to render the table to the page.
 * 
 */
class Office_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'office',     //singular name of the listed records
            'plural'    => 'offices',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'DisplayName':
			case 'featured' :
				return $item[$column_name];
			case 'images' :
				return $item[$column_name];
			case 'OfficeDescription' :
				return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

	function create_slug( $string ) {
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
		return $slug;
	}
	
    function column_title($item){
		$company_url = home_url(). '/company/?company='. $this->create_slug( $item['OfficeName'] );
        
        //Build row actions
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&office=%s">Edit</a>',$_REQUEST['page'],'edit',$item['OfficeNumber']),
			'view' => sprintf('<a href="%s&id=%s" target="_blank">View</a>', $company_url, $item['OfficeNumber'])
        );
		
		$star_icon = $item['featured'] == 1 ? '<i class="dashicons dashicons-star-filled"></i>' : '';
        
        //Return the title contents
        return sprintf('%1$s %2$s <span style="color:silver">(id:%3$s)</span>%4$s',
			/*%1$s*/$star_icon,
            /*%2$s*/ $item['OfficeName'],
            /*%3$s*/ $item['OfficeNumber'],
            /*%4$s*/ $this->row_actions($actions)
        );
    }
	
	function column_featured($item) {
		$featured_icon = !empty( $item['featured'] ) ? 'Featured: <span>Yes</span>' : '';
		return $featured_icon;
	}
	
	function column_OfficeDescription($item) {
		$has_desc = !empty( $item['OfficeDescription'] ) ? 'Description: <span>Yes</span>' : '';
		return $has_desc;
	}
	
	function column_images($item) {
		$image = '';
		if( !empty( $item['images'] ) ) {
			// First: See if the image is from the media gallery
			if( filter_var( $item['images'], FILTER_VALIDATE_URL) ) {
				$image_url = $item['images'];
			} else {
			// Second: If not use the image from /_retsapi folder
				$image_url = home_url() .'/_retsapi/imagesOffices/'. $item['images'];
			}
			
			// Return the logo image if there's one set
			return sprintf('<img src="%s" class="logo" alt="" width="" height="" />', $image_url );
		}
	}

	// Not used. This is if we wanted to create bulk actions
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("office")
            /*$2%s*/ $item['OfficeNumber']                //The value of the checkbox should be the record's id
        );
    }

    function get_columns(){
        $columns = array(
            //'cb' => '<input type="checkbox" />', // Not used. Only needed for bulk actions
            'title' => 'RETS Office Name',
            'DisplayName' => 'Display Name',
			'featured' => 'Featured',
			'OfficeDescription' => 'Description',
			'images' => 'Logo'
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('OfficeName',true), //true means it's already sorted
            'DisplayName'    => array('DisplayName',false),
            'featured'  => array('featured',false)
        );
        return $sortable_columns;
    }

    // Not used. This is where the bulk actions would be defined IF we used them.
    /*function get_bulk_actions() {
        $actions = array(
            'featured'    => 'Featured'
        );
        return $actions;
    }*/

    // Not used. This is where the bulk actions would be processed if we used them.
    /*function process_bulk_action() {  
        //Detect when a bulk action is being triggered...
        if( 'featured'===$this->current_action() ) {
            //wp_die('Items deleted (or they would be if we had items to delete)!');
        } 
    }*/


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        
		// First, lets decide how many records per page to show
        $per_page = 50;
        
        // REQUIRED. Now we need to define our column headers.
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        // REQUIRED. Finally, we build an array to be used by the class for column 
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // This is where the bulk actions would run if we used them.
        //$this->process_bulk_action();
		
		$offices_query = new Rets_DB();
		
        // If the search box is used display matching offices in List Table
		$search = ( isset( $_REQUEST['s'] ) ) ? "AND Office_OFFI.OfficeName LIKE '%". trim($_REQUEST['s']) ."%'" : "";
		
		$query = "
			SELECT Office_OFFI.IsActive,
			Office_OFFI.OfficeNumber,
			Office_OFFI.OfficeName,
			Office_OFFI.OfficeDescription,
			Office_OFFI.DisplayName,
			Office_OFFI.featured,
			Office_OFFI.images
			FROM Office_OFFI
			WHERE IsActive = 'T'
			{$search}
		";
		
		// Builds our array of Offices
		$data = $offices_query->select( $query );       
        
        // This checks for sorting input and sorts the data in our array accordingly.
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'OfficeName'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        // REQUIRED for pagination. Let's figure out what page the user is currently looking at.
        $current_page = $this->get_pagenum();
        
        // REQUIRED for pagination. Let's check how many items are in our data array.
        $total_items = count($data);
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() for this
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
           
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}


/** ************************ EDIT SINGLE OFFICE FORM *****************************
*********************************************************************************/
class Edit_Rets_Office {
	
	protected $id;
	
	protected $office;
				
	function __construct() {
		// Get office ID from url
		$this->id = isset($_GET['office']) ? floatval($_GET['office']) : 0;
				
		// Enqueue up additional files for Media Manager and TinyMCE
		if ( !did_action('wp_enqueue_media') ) wp_enqueue_media();
		wp_enqueue_script('tiny_mce');
		wp_enqueue_script('editor');
		wp_enqueue_script('editor-functions');
		add_thickbox();
				
		// Post action using save_office() function
		if ( !empty($_POST['action']) && $_POST['action'] === 'office_update' ) {
			// Do save function
			$this->save_office();
		}
	}
	
	// Get the office ID from the url
	private function get_office_array() {
		$offices_query = new Rets_DB();
		
		$id = $offices_query->quote( $this->id );
		
		$query = "
			SELECT IsActive,
			OfficeNumber,
			OfficeName,
			OfficeDescription,
			DisplayName,
			featured,
			images
			FROM Office_OFFI
			WHERE OfficeNumber = {$id}
		";

		// Get the office
		$office_array = $offices_query->select( $query );
		
		return $office_array[0];
	}
	
	public function rets_name() {
		return $this->get_office_array()['OfficeName'];
	}
	
	public function rets_id() {
		return $this->get_office_array()['OfficeNumber'];
	}
	
	public function rets_displayname() {
		return $this->get_office_array()['DisplayName'];
	}
	
	public function rets_isfeatured() {
		return $this->get_office_array()['featured'];
	}
	
	public function rets_image() {
		return $this->get_office_array()['images'];
	}
	
	public function rets_description() {
		return $this->get_office_array()['OfficeDescription'];
	}
	
	private function create_slug( $string ) {
		$slug = strtolower( preg_replace( '/[^A-Za-z0-9-]+/', '-', $string ) );
		return $slug;
	}
	
	private function css() {
		$css = '';
		$css .= '<style type="text/css">';
			$css .= '.edit-office-wrap h3 { color: #888; font-weight: normal; }';
			$css .= '.edit-office-wrap h3 span { color: #333; font-weight: bold; }';
			$css .= 'a.view-office { margin-left: 10px !important; }';
			$css .= '.image-wrap { width: 100px; height: 100px; float: left; margin-right: 10px; }';
			$css .= '.widefat th, .widefat td { padding-top: 15px; padding-bottom: 15px; }';
		$css .= '</style>';
		echo $css;
	}
	
	// Loads the media modal when selecting a logo, 
	// then puts image url into hidden input field and placeholder image src
	private function js() {
		$js = '';
		$js .= '<script type="text/javascript">';
			$js .= "jQuery(document).ready(function($) {
					$('#office-logo-upload').click(function(e) {
						e.preventDefault();

						var custom_uploader = wp.media({
							title: 'Custom Image',
							button: {
								text: 'Select Image'
							},
							multiple: false  // Set this to true to allow multiple files to be selected
						})
						.on('select', function() {
							var attachment = custom_uploader.state().get('selection').first().toJSON();
							$('#office-image-placeholder').attr('src', attachment.url);
							$('#office-images').val(attachment.url);

						})
						.open();
					});
				});";
		$js .= '</script>';
		echo $js;
	}
	
	// Sets the checkbox to checked or not depending if the database value is 1 or 0
	private function is_checked( $input ) {
		$is_checked = $input == 1 ? 'checked' : '';
		return $is_checked;
	}
	
	// Loads wordpress TinyMCE for textarea
	private function wysiwyg_editor( $input ) {
		ob_start();
		wp_editor( $input, 'officedescription', array('textarea_name' => 'OfficeDescription', 'media_buttons' => false, 'textarea_rows' => 10) );
		$textarea = ob_get_clean();
		return $textarea;
	}
	
	// Get the correct logo. Either the one in _retsapi, wordpress media, or placeholder logo.
	private function placeholder_image( $input ) {
		$image = '';
		if( !empty( $input ) ) {
			// First: See if the image is from the media gallery
			if( filter_var( $input, FILTER_VALIDATE_URL) ) {
				return $input;
			} else {
			// Second: If not use the image from /_retsapi folder
				$image = home_url() .'/_retsapi/imagesOffices/'. $input;
				return $image;
			}
		} else {
			// Third: If neither exist just use a placeholder image
			$image = TBB_FUNCTIONS_URL .'/images/placeholder.jpg';
			return $image;
		}
	}
	
	private function get_office_url( $name, $id ) {
		$url = home_url(). '/company/?company='. $this->create_slug( $name ).'&id='. $id;
		return $url;
	}
	
	// Return the nonce instead of straight echoing it by default.
	private function get_nonce() {
		ob_start();
		wp_nonce_field('office_update', 'office_nonce');
		$nonce = ob_get_clean();
		return $nonce;
	}
	
	// Render the Edit Office form.
	public function display_form() {
		
		//$office = $this->get_office_array();	// All office fields	
		//print_r($office);
		
		$html = '';
		$html .= $this->css();
		$html = sprintf( '<div class="edit-office-wrap"><p><a href="%s/wp-admin/admin.php?page=rets-offices">&lsaquo; All Offices</a></p>', 
						home_url() );
		$html .= sprintf( '<h3>Editing Office: <span>%s</span> <small>(id: %s)</small></h3>', $this->rets_name(), $this->rets_id() );
		
		$html .= sprintf( '<form method="post" action="%s">', '' );
			$html .= '<table class="widefat">';
		
				$html .= sprintf( '<tr valign="top" class="alternate"><th scope="row"><label>Display Name:</label></th>
						<td>
							<input id="office-DisplayName" class="regular-text wide" type="text" name="DisplayName" value="%s" /> 
						</td>
					</tr>', $this->rets_displayname() );
		
				
				$html .= sprintf( '<tr valign="top"><th scope="row"><label>Featured:</label></th>
						<td>
							<input id="office-featured" type="checkbox" name="featured" value="%s" %s /> 
						</td>
					</tr>', $this->rets_isfeatured(), $this->is_checked( $this->rets_isfeatured() ) );
		
				$html .= sprintf( '<tr valign="top" class="alternate"><th scope="row"><label>Logo:</label></th>
					<td>
                        <div class="image-wrap"><img id="office-image-placeholder" class="office-logo" src="%s" width="100" height="100"/></div>
                        <input id="office-images" class="regular-text office_logo_url top-align" type="text" name="images" size="60" value="%s" />
                        <a href="#" id="office-logo-upload" class="button-secondary">Select Image</a>
					</td>
				</tr>', $this->placeholder_image( $this->rets_image() ), $this->rets_image() );
		
				$html .= sprintf( '<tr valign="top"><th scope="row"><label>Office Description:</label></th>
						<td>
							%s 
						</td>
					</tr>', $this->wysiwyg_editor( $this->rets_description() ) );
		
			$html .= '</table>';
			$html .= sprintf( '<p><input type="hidden" name="action" value="office_update" />
								<input type="hidden" name="OfficeNumber" value="%s" />
								%s
								<input class="button-primary" type="submit" value="Update Office" />
								<a class="view-office button" href="%s" target="_blank">View Office</a></p>', 
							 	$this->rets_id(), 
							 	$this->get_nonce(),
							 	$this->get_office_url( $this->rets_name(), $this->rets_id() ) 
					);
		$html .= '</form></div>';
		$html .= $this->js();
		
		echo $html;
		
	}
	
	// Update the office on save.
	public function save_office() {
		if(!isset( $_POST['office_nonce']) || ! wp_verify_nonce( $_POST['office_nonce'], 'office_update')) :
            wp_die(new WP_Error(
                'invalid_nonce', __('Sorry, I\'m afraid you\'re not authorized to do this.')
            ));
            exit;
        endif;
		
		$message = '';
		
		$db_query = new Rets_DB();
				
		// Quote and escape post values to get ready to insert into DB.
		$OfficeNumber = $db_query->quote( $_POST['OfficeNumber'] );
		
		$DisplayName = $db_query->quote( $_POST['DisplayName'] );
		
		$featured = isset($_POST["featured"]) ? 1 : 0;
		
		$images = $db_query->quote( $_POST['images'] );
		
		$OfficeDescription = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $_POST['OfficeDescription'] );
		$OfficeDescription = "'". strip_tags( $OfficeDescription, '<p><a><br><br/><br /><em><div><ul><ol><li><b><strong><blockquote>') ."'";
		//$OfficeDescription = str_replace('\&quot;', '', $OfficeDescription);
		
		$update_query = "
			UPDATE Office_OFFI
			SET DisplayName={$DisplayName},
			featured={$featured},
			images={$images},
			OfficeDescription={$OfficeDescription}
			WHERE OfficeNumber={$OfficeNumber}
		";

		// Update the office
		$update_office = $db_query->query( $update_query );
				
		if( $update_office === false ) {
			$message .= sprintf( '<div class="notice notice-error"><p>Something went wrong. Office not saved. %s</p></div>',
								$db_query->error($update_query) );
			//print_r($update_office);
		} else {
			$message .= sprintf( '<div class="notice notice-success is-dismissible"><p>%s updated successfully.</p></div>', 
								$this->rets_name() );
			//print_r($update_office);
		}
		
		echo $message;		
	}
	
}


/** ************************ REGISTER THE OFFICE PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this, we'll add a top-level
 * menu item called Offices after the Pages menu.
 */
add_action('admin_menu', 'rets_add_menu_items');
function rets_add_menu_items(){
    add_menu_page(
		'Offices', 
		'Offices', 
		'edit_posts', 
		'rets-offices', 
		'rets_render_office_page', 
		'dashicons-building', 
		'20'
	);
}


/** *************************** RENDER OFFICE PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the office list table.
 */
function rets_render_office_page() { ?>
	
	<style>
		h2 i:before { vertical-align: baseline !important; color: #02888f; }
		.widefat td, .widefat td p, .widefat td ol, .widefat td ul { font-size: 14px; }
		.column-title i.dashicons { font-size: 16px; color: green; margin-top: 2px; }
		.widefat td.column-featured, .widefat td.column-OfficeDescription { color: silver; }
		.widefat td.column-featured span, .widefat td.column-OfficeDescription span { color: green; }
		.column-images img { width: 50px; max-height: 50px; }
	</style>
	<div class="wrap">
		<h2><i class="dashicons-before dashicons-building"></i> RETS Offices</h2>
		
		<?php if( isset ( $_GET["action"] ) && trim ( $_GET["action"] ) == 'edit' ) {
			
			// Render the Single Office Edit Page
			$edit_office_form = new Edit_Rets_Office(); 
			$edit_office_form->display_form();

		} else {
		
			// Render the Office List Table
			$officeListTable = new Office_List_Table();
			$officeListTable->prepare_items( $_POST['s'] );
			?>

			<form id="offices-filter" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $officeListTable->search_box( 'Search', 'office-search' ); ?>
				<?php $officeListTable->display() ?>
			</form>

		<?php } ?>
	
	</div><!-- end wrap -->
	
	<?php
}
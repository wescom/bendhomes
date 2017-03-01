<?php
// Offices admin page

/*class RETS_Featured_Offices {
	
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_office_files' ) );
		//add_action( 'admin_action_offices', array( $this, 'admin_action' ) );
	}

	function admin_menu() {
		add_menu_page(
			'Featured Offices',
			'Offices',
			'manage_options',
			'rets-offices',
			array( $this, 'render_page' ),
			'dashicons-building',
			'20'
		);
	}
	
	function enqueue_office_files() {
		wp_enqueue_style( 'company', TBB_FUNCTIONS_URL . 'css/company-settings.css' );
	}
	
	/*function admin_action() {
		//print_r($_POST);
		exit();
	}
	
	public function render_page() {
		
		$query = "
			SELECT OF.OfficeName, OF.OfficeDescription, OF.DisplayName, OF.featured,
			FROM Office_OFFI OF
			WHERE IsActive = 'T'
			ORDER BY OfficeName ASC
		";

		$companies_query = new Rets_DB();

		$companies = $companies_query->select( $query );
		
		print_r($companies);
		
        $html = '<h1>Test Admin Page</h1>'.$query.'<div class="wrap tbb-company-page">';
			/*ob_start();
			include_once( TBB_FUNCTIONS_DIR .'/admin/rets-offices-page.php' );
			$html .= ob_get_contents();
			ob_end_clean();
        $html .= '</div>';
		
		echo $html;
        
	}
	
}

new RETS_Featured_Offices;*/




/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
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
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
class TT_Example_List_Table extends WP_List_Table {
    
    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query()
     * 
     * In a real-world scenario, you would make your own custom query inside
     * this class' prepare_items() method.
     * 
     * @var array 
     **************************************************************************/


    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'office',     //singular name of the listed records
            'plural'    => 'offices',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }


    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'DisplayName':
            case 'featured':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
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
            //'delete'    => sprintf('<a href="?page=%s&action=%s&office=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['OfficeName'],
            /*$2%s*/ $item['OfficeNumber'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("office")
            /*$2%s*/ $item['OfficeNumber']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'RETS Office Name',
            'DisplayName'    => 'Display Name',
            'featured'  => 'Featured'
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('OfficeName',false),     //true means it's already sorted
            'DisplayName'    => array('DisplayName',false),
            'featured'  => array('featured',false)
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'featured'    => 'Featured'
        );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'featured'===$this->current_action() ) {
            //wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }


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
        //global $wpdb; //This is used only if making any database queries


        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 50;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
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
		
		$offices_query = new Rets_DB();
		
		// Builds our array of Offices
		$data = $offices_query->select( $query );
                
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'OfficeName'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
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
		$this->id = isset($_GET['office']) ? mysql_real_escape_string( floatval($_GET['office']) ) : 0;
	}
	
	public function get_office() {
		$id = $this->id;
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

		$offices_query = new Rets_DB();

		// Get the office
		$office_array = $offices_query->select( $query );
		
		return $office_array;
	}
	
	public function display_form() {
		
		$office = $this->get_office();
		print_r($office);
		
		$html = '';
		$html .= sprintf( '<h3>Editing Office: %s</h3>', $office['OfficeName'] );
		
		return $html;
		
	}
	
}




/** ************************ REGISTER THE OFFICE PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */
add_action('admin_menu', 'tt_add_menu_items');
function tt_add_menu_items(){
    add_menu_page(
		'Offices', 
		'Offices', 
		'activate_plugins', 
		'rets-offices', 
		'rets_render_list_page', 
		'dashicons-building', 
		'20'
	);
}


/** *************************** RENDER OFFICE PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function rets_render_list_page(){
	
	$action = ( isset ( $_GET["action"] ) && trim ( $_GET["action"] ) == 'edit' ) ? trim ( $_GET["action"] ) : '';
	
	?>
	<style>
		h2 i:before { vertical-align: baseline !important; color: #02888f; }	
	</style>
	<div class="wrap">
		<h2><i class="dashicons-before dashicons-building"></i> Featured Offices</h2>
		
		<?php if( isset ( $_GET["action"] ) && trim ( $_GET["action"] ) == 'edit' ) { ?>

			<h3>Edit Office Page</h3>
			
			<?php $edit_office = new Edit_Rets_Office(); 
			$edit_office->display_form();
			?>

		<?php } else {

			//Create an instance of our package class...
			$officeListTable = new TT_Example_List_Table();
			//Fetch, prepare, sort, and filter our data...
			$officeListTable->prepare_items( $_POST['s'] );
			?>

			<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
			<form id="offices-filter" method="get">
				<!-- For plugins, we also need to ensure that the form posts back to our current page -->
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $officeListTable->search_box( 'Search', 'office-search' ); ?>
				<!-- Now we can render the completed list table -->
				<?php $officeListTable->display() ?>
			</form>

		<?php } ?>
	
	</div><!-- end wrap -->
	
	<?php
}
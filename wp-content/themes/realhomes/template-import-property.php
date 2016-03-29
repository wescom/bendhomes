<?php
/*
*  Template Name: Import Property Template
*/
/*
*  Author: Justin Grady
*/

ini_set('max_execution_time', 0);
date_default_timezone_set('America/Los_Angeles');

/* #### INCLUDES ##### */
include_once ABSPATH . 'wp-admin/includes/media.php';
include_once ABSPATH . 'wp-admin/includes/file.php';
include_once ABSPATH . 'wp-admin/includes/image.php';

/* ################################# */
/* #### DATA TYPES - SCENARIOS ##### */
/* ################################# */
$scenarios = array(
  /* 'OpenHouse_OPEN'=> array(
    'count' => 999999,
    'fotos' => 'no',
    'resource' => 'OpenHouse',
    'class' => 'OPEN',
    'name' => 'OpenHouse_OPEN'
  ), */
  'Property_BUSI' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'BUSI',
    'name' => 'Property_BUSI'
  ),
  'Property_COMM' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'COMM',
    'name' => 'Property_COMM'
  ),
  'Property_FARM' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'FARM',
    'name' => 'Property_FARM'
  ),
  'Property_LAND' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'LAND',
    'name' => 'Property_LAND'
  ),
  'Property_MULT' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'MULT',
    'name' => 'Property_MULT'
  ),
  'Property_RESI' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'RESI',
    'name' => 'Property_RESI'
  )
);

// for now, manually set the data to pull
$scenarioset = $scenarios['Property_RESI'];
echo '<p style="background-color: brown; color: #ffffff; padding: 0.25em;">'.$scenarioset['name'].'</p>';

/* #### FUNCTIONS ##### */
function formatprice($price) {
  $pricearr = explode('.',$price);
  $newprice = $pricearr[0];
  $newprice = (int) $newprice;
  return $newprice;
}

function bhLookupAgent($guid) {
  if($guid != NULL) {
    global $wpdb;
    $guid = "'http://".$guid."'";
    $sqlquery = "SELECT ID FROM $wpdb->posts WHERE guid = ".$guid;
    // echo $sqlquery;
    $result = $wpdb->get_results( $sqlquery );
  } else {
    $result = NULL;
  }
  // echo '<pre> test222';
  // print_r($result);
  // echo '</pre>';
  return $result;
}

function bhLookupPropertyType($typestring) {
  // this taked the RESIPropertySubtype var from rets
  // and does a like compare to property types in the Wordpress database
  // it then supplies the property type as an integer for feed ingestion
  global $wpdb;

  // multiple types can come in comma delim from RETS feed
  $types = explode(',',$typestring);

  $output = array();
  foreach($types as $type) {

    // remove any spaces
    $type = trim($type);

    // Yeah, I know this is sorta hacky
    if($type == 'Residential') {
      $type = 'Single Family Home';
    }

    // need single quotes around string for correct mysql syntax
    $type = "'".$type."'";
    $result = $wpdb->get_results( "SELECT term_id FROM wp_terms WHERE name LIKE ".$type);

    // echo '<pre> test322db -- ';
    // print_r($result);
    // echo '</pre>';

    // there is usually only one result, but if more, take the first key
    $myid = $result[0]->{term_id};
    $myid = (int) $myid;

    $output[] = $myid;
  }
  return $output;
}

function bhLookupFeatures($featlist_interior,$featlist_exterior) {
  // this taked the
  // RESIINTE, RESIEXTE,
  // and does a like compare to property types in the Wordpress database
  // it then supplies the property type as an integer for feed ingestion
  global $wpdb;

  $fint = explode(',',$featlist_interior);
  $fext = explode(',',$featlist_exterior);
  $features = array_merge($fint,$fext);

  foreach($features as $feature) {
    $feature = "'".$feature."'";
    $results[] = $wpdb->get_results( "SELECT term_id FROM wp_terms WHERE name LIKE ".$feature, OBJECT);
  }

  $output = array();
  foreach($results as $result) {
    $output[] = $result[0]->{term_id};
  }

  // strip empty keys from array
  $output = array_filter($output);
  // print_r($output);
  return $output;
}

function bhLookupPostByMLS($mlsnum) {
  if($mlsnum != NULL) {
    // only do this lookup if $mlsnum is not empty/NULL
    // look up Wordpress property posts by MLS id
    $mls_ids = array();
    $args = array(
          'post_type'     => 'property',
          // 'post_status'   => 'publish',
          'posts_per_page'=> -1, // want all items in query, not just default 10
          'meta_query' => array(
              array(
                  'key' => 'REAL_HOMES_property_id',
                  'value' => $mlsnum
              )
          )
      );
      $getPosts = new WP_Query($args);

      if( $getPosts->have_posts() ) {
        while( $getPosts->have_posts() ) {
          $getPosts->the_post();
          $mls_ids[] = get_the_ID();
        } // end while
    } else {
      $mls_ids = NULL;
    }
    wp_reset_postdata();
    // sort ids from lowest to highest integer value, lowest number is the first entry in db for a given property
    if($mls_ids == NULL) {
      return NULL;
    } else {
      sort($mls_ids,SORT_NUMERIC);
      return $mls_ids;
    }
  } else {
    return NULL;
  }
}

/* ############################### */
/* #### POST ACTIONS - CASES ##### */
/* ############################### */
function bhPostActions($status,$mlsid=NULL) {
  // // end use cases
  // add_property
  // skip_property
  // update_property
  // delete_property

  // // From feeds statuses
  // 'Active'
  // 'Contingent Bumpable'
  // 'ShrtSale-BringBckUps'
  // 'Leased'
  // 'Pending'
  // 'Sold'
  // 'Terminated'
  // 'Withdrawn'
  // 'Expired'

  // if the property already exists in db, then update it
  // works with feed statuses, see scenarios
  if($mlsid != NULL) {
    $mlsaction = 'update';
  } else {
    $mlsaction = 'insert';
  }

  // insert from API use cases
  if($mlsaction == 'insert') {
    if($status == 'Active') {
      $apiaction = 'add_property'; // only add if insert/Active are true, skip everywhere else
    }
    else
    {
      $apiaction = 'skip_property'; // skip prop if insert, but is on list of prohibited statuses
    }
  }
  // update from API use cases
  elseif($mlsaction == 'update') {
    if($status == 'Active' ||
        $status == 'Pending' ||
        $status == 'ShrtSale-BringBckUps')
    {
      $apiaction = 'update_property';
    }
    else
    {
      $apiaction = 'delete_property';
    }
  }

  // echo '<p style="color: darkgreen">apiaction: '.$apiaction.' <br/>mlsid: '.$mlsid.' <br/>apifeedstat: '.$status.'</p>';
  return $apiaction;
}

/* ############################## */
/* #### DATA SETUP and PULL ##### */
/* ############################## */
function dbresult($sset) {

  $data = array();
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );

  $mysqli = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);
  /* check connection */
  if ($mysqli->connect_errno) {
      printf("Connect failed: %s\n", $mysqli->connect_error);
      exit();
  }

  $resource = $sset['resource'];
  $class = $sset['class'];
  // $resource = 'Property';
  // $class = 'RESI';
  $rc = $resource.'_'.$class;

  $fnamerecent = ABSPATH.'/_retsapi/pulldates/'.$rc.'.txt';
  if(file_exists($fnamerecent)) {
    $pulldate = file_get_contents($fnamerecent);
  } else {
    $pulldate = strtotime('-30 days'); //'-6 hours' '-1 days'
  }

  $querydate = date('Y-m-d H:i:s',$pulldate);
  // echo $pulldate;
  /* AND images IS NOT NULL */
  // AND Status = 'Active'

  $sqlquery = "SELECT * FROM ".$rc." WHERE
              PublishToInternet = 1
              AND lastPullTime >= '".$querydate."'
              LIMIT ".$sset['count']."
              ;";

  // echo '<pre>';
  // print_r($sqlquery);
  // echo '</pre>';

  /* Select queries return a resultset */
  if ($result = $mysqli->query($sqlquery)) {
      // printf("Select returned %d rows.\n", $result->num_rows);
      while($row = $result->fetch_assoc()) {
          $data[] = $row;
      }
      // Frees the memory associated with a result
      $result->free();
  }

  $mysqli->close();
  return $data;

}

/* ############################ */
/* #### IMAGES PROCESSING ##### */
/* ############################ */
if ( ! function_exists( 'bendhomes_image_upload' ) ) {

 function bendhomes_image_upload($imagebase) {

   $imagedir = ABSPATH.'/_retsapi/images/property/';
   $imagepull = $imagedir.$imagebase;
   $tmp = $imagepull;
   $file_array = array(
       'name' => basename( $imagebase ),
       'tmp_name' => $tmp
   );
   if ( is_wp_error( $tmp ) ) {
       // @unlink( $file_array[ 'tmp_name' ] );
       return $tmp;
       echo '<p style="background-color: red; color: #fff;">'.$tmp.'</p>';
   }

   $uploaded_image = media_handle_sideload( $file_array, array( 'test_form' => false ) );
   // echo '<pre>';
   // print_r($uploaded_image);
   // echo '</pre>';

   // this returns the image id from WP that is used for property data import
   return $uploaded_image;

 }
 add_filter( 'bendhomes_img_upload', 'bendhomes_image_upload', 10, 1 );
}

function bhImageSet($item) {
  $imagesdir['source'] = ABSPATH.'/_retsapi/imagesbackup/property/';
  $imagesdir['tmpdest'] = ABSPATH.'/_retsapi/images/property/';
  $bhimgids = NULL;
  if($item['images'] != '') {
    $tmpimages = explode('|',$item['images']);
    $bhimgids = array(); // predeclare wp images id array for use
    // let's upload our images and get our wp image ids for use later in array
    foreach($tmpimages as $img) {
      // copies image from backup dir, to images dir, file is unlinked/deleted
      // upon processing. This will enable images to update and scripts to be rerun
      if(!file_exists($imagesdir['tmpdest'].'/'.$img)) {
        copy($imagesdir['source'].'/'.$img,$imagesdir['tmpdest'].'/'.$img);
      }
      $tf = apply_filters( 'bendhomes_img_upload', $img );
      $bhimgids[] = $tf;
    }
    unset($tmpimages,$tf); // we only need $tmpimages & $tf for this loop
  }
  return($bhimgids);
}






$proparr = dbresult($scenarioset);

/* #### PROPERTY DATA LOOP ##### */
$retsproperties = array(); // first declaration
foreach($proparr as $propitem) {

  // status use cases
  // DECIDE what to do with pre-existing records
  // update, delete
  $mlsposts = bhLookupPostByMLS($propitem['MLNumber']);
  $bhpropertyid = $mlsposts[0];
  $postaction = bhPostActions($propitem['Status'],$bhpropertyid);

  // print_r($propitem);

  // // end use cases
  // add_property
  // skip_property
  // update_property
  // delete_property
  if($postaction == 'delete_property' || $postaction == 'skip_property') {
    $retsproperties[$propitem['ListingRid']]['action'] = $postaction;
    $retsproperties[$propitem['ListingRid']]['property_id'] = $bhpropertyid;
  } elseif ($postaction == 'add_property' || $postaction == 'update_property') {
    $propname = $propitem['StreetNumber'].' '.$propitem['StreetNumberModifier'].' '.$propitem['StreetName'].' '.$propitem['StreetSuffix'].', '.$propitem['City'].', '.$propitem['State'].' '.$propitem['ZipCode'];
    $propname = trim($propname);
    $propprice = formatprice($propitem['ListingPrice']);

    $agentguid = 'agent_'.$propitem['ListingAgentNumber'];
    $agentposts = bhLookupAgent($agentguid);
    $bhagentid = $agentposts[0];
    $bhagentid = $bhagentid->{ID};

    switch ($scenarioset['name']){
    	case "OpenHouse_OPEN":
    		echo "Bring an extra $500";
    		break;
    	case "Property_BUSI":
        // START Property_BUSI import template
        $retsproperties[$propitem['ListingRid']] = array(
          'inspiry_property_title' => $propname,
          'description' => $propitem['MarketingRemarks'],
          'type' => bhLookupPropertyType($propitem['BUSITYPE']),
          'status' => 34,
          'location' => $propitem['City'],
          'property-id' => $propitem['MLNumber'], // this the the MLS ID
          'property_id' => $bhpropertyid, // this is the WP post id if there's record update
          'price' => $propprice,
          'price-postfix' => '',
          'video-url' => $propitem['VirtualTourURL'],
          'address' => $propname,
          'coordinates' => $propitem['Latitude'].','.$propitem['Longitude'],
          // 'featured' => 0, // 0 == not featured, 1 == featured
          'agent_display_option' => 'agent_info',
          'agent_id' => $bhagentid,
          'action' => $postaction // give api db status, and pre-existing wp id, if exists
        );
        // END Property_BUSI import template
    		break;
    	case "Property_COMM":
        // START Property_COMM import template
        $retsproperties[$propitem['ListingRid']] = array(
          'inspiry_property_title' => $propname,
          'description' => $propitem['MarketingRemarks'],
          'type' => bhLookupPropertyType($propitem['COMMTYPE']),
          'status' => 34,
          'location' => $propitem['City'],
          'property-id' => $propitem['MLNumber'], // this the the MLS ID
          'property_id' => $bhpropertyid, // this is the WP post id if there's record update
          'price' => $propprice,
          'price-postfix' => '',
          'video-url' => $propitem['VirtualTourURL'],
          'address' => $propname,
          'coordinates' => $propitem['Latitude'].','.$propitem['Longitude'],
          // 'featured' => 0, // 0 == not featured, 1 == featured
          'agent_display_option' => 'agent_info',
          'agent_id' => $bhagentid,
          'action' => $postaction // give api db status, and pre-existing wp id, if exists
        );
        // END Property_COMM import template
    		break;
    	case "Property_FARM":
        // START Property_FARM import template
        $retsproperties[$propitem['ListingRid']] = array(
          'inspiry_property_title' => $propname,
          'description' => $propitem['MarketingRemarks'],
          'type' => bhLookupPropertyType($propitem['PropertyType']),
          'status' => 34,
          'location' => $propitem['City'],
          'bedrooms' => $propitem['Bedrooms'],
          'bathrooms' => $propitem['Bathrooms'],
          'garages' => $propitem['FARMGARA'],
          'property-id' => $propitem['MLNumber'], // this the the MLS ID
          'property_id' => $bhpropertyid, // this is the WP post id if there's record update
          'price' => $propprice,
          'price-postfix' => '',
          'size' => $propitem['SquareFootage'],
          'area-postfix' => 'Sq Ft',
          'video-url' => $propitem['VirtualTourURL'],
          'address' => $propname,
          'coordinates' => $propitem['Latitude'].','.$propitem['Longitude'],
          // 'featured' => 0, // 0 == not featured, 1 == featured
          'features' => bhLookupFeatures($propitem['FARMINTE'],$propitem['FARMEXTE']),
          'agent_display_option' => 'agent_info',
          'agent_id' => $bhagentid,
          'action' => $postaction // give api db status, and pre-existing wp id, if exists
        );
        // END Property_FARM import template
    		break;
    	case "Property_LAND":
        // START Property_LAND import template
        $retsproperties[$propitem['ListingRid']] = array(
          'inspiry_property_title' => $propname,
          'description' => $propitem['MarketingRemarks'],
          'type' => bhLookupPropertyType($propitem['PropertySubtype1']),
          'status' => 34,
          'location' => $propitem['City'],
          'property-id' => $propitem['MLNumber'], // this the the MLS ID
          'property_id' => $bhpropertyid, // this is the WP post id if there's record update
          'price' => $propprice,
          'price-postfix' => '',
          'video-url' => $propitem['VirtualTourURL'],
          'address' => $propname,
          'coordinates' => $propitem['Latitude'].','.$propitem['Longitude'],
          // 'featured' => 0, // 0 == not featured, 1 == featured
          'agent_display_option' => 'agent_info',
          'agent_id' => $bhagentid,
          'action' => $postaction // give api db status, and pre-existing wp id, if exists
        );
        // END Property_LAND import template
    		break;
      case "Property_MULT":
        // START Property_MULT import template
        $retsproperties[$propitem['ListingRid']] = array(
          'inspiry_property_title' => $propname,
          'description' => $propitem['MarketingRemarks'],
          'type' => bhLookupPropertyType($propitem['PropertySubtype1']),
          'status' => 34,
          'location' => $propitem['City'],
          'bedrooms' => $propitem['Bedrooms'],
          'bathrooms' => $propitem['Bathrooms'],
          'property-id' => $propitem['MLNumber'], // this the the MLS ID
          'property_id' => $bhpropertyid, // this is the WP post id if there's record update
          'price' => $propprice,
          'price-postfix' => '',
          'size' => $propitem['SquareFootage'],
          'area-postfix' => 'Sq Ft',
          'video-url' => $propitem['VirtualTourURL'],
          'address' => $propname,
          'coordinates' => $propitem['Latitude'].','.$propitem['Longitude'],
          // 'featured' => 0, // 0 == not featured, 1 == featured
          'features' => bhLookupFeatures($propitem['MULTINTE'],$propitem['MULTEXTE']),
          'agent_display_option' => 'agent_info',
          'agent_id' => $bhagentid,
          'action' => $postaction // give api db status, and pre-existing wp id, if exists
        );
        // END Property_MULT import template
    		break;
      case "Property_RESI":
        // START Property_RESI import template
        $retsproperties[$propitem['ListingRid']] = array(
          'inspiry_property_title' => $propname,
          'description' => $propitem['MarketingRemarks'],
          'type' => bhLookupPropertyType($propitem['PropertyType']),
          'status' => 34,
          'location' => $propitem['City'],
          'bedrooms' => $propitem['Bedrooms'],
          'bathrooms' => $propitem['Bathrooms'],
          'garages' => $propitem['RESIGARA'],
          'property-id' => $propitem['MLNumber'], // this the the MLS ID
          'property_id' => $bhpropertyid, // this is the WP post id if there's record update
          'price' => $propprice,
          'price-postfix' => '',
          'size' => $propitem['SquareFootage'],
          'area-postfix' => 'Sq Ft',
          'video-url' => $propitem['VirtualTourURL'],
          'address' => $propname,
          'coordinates' => $propitem['Latitude'].','.$propitem['Longitude'],
          // 'featured' => 0, // 0 == not featured, 1 == featured
          'features' => bhLookupFeatures($propitem['RESIINTE'],$propitem['RESIEXTE']),
          'agent_display_option' => 'agent_info',
          'agent_id' => $bhagentid,
          'action' => $postaction // give api db status, and pre-existing wp id, if exists
        );
        // END Property_RESI import template
    		break;
    } // end swich statement
    if($postaction == 'add_property') {
      $bhimgids = bhImageSet($propitem);
      $retsproperties[$propitem['ListingRid']]['gallery_image_ids'] = $bhimgids;
      $retsproperties[$propitem['ListingRid']]['featured_image_id'] = $bhimgids[0];
    }
    unset($bhimgids,$mlsposts);
  } // end $postaction ifelse
}

$count = 0;
foreach($retsproperties as $myproperty) {

  echo '<h1>'.$count.' - '.$myproperty['action'].'</h1>';
  echo '<pre style="background-color: #ececec; padding: 0.25em; border-radius: 0.25em;">';
  print_r($myproperty);
  echo '</pre>';

  $invalid_nonce = false;
  $submitted_successfully = false;
  $updated_successfully = false;

  /* Check if action field is set  */
  if( isset( $myproperty['action'] ) ) {

      if( $myproperty['action'] != 'skip_property' ) {

              echo '<h3 style="color: blue;">'.$count.' - '.$myproperty['action'].'</h3>';
              echo '<h4 style="color: red;">my property agent_id: '.$myproperty['agent_id'].'</h4>';
              // Start with basic array
              $new_property = array(
                  'post_type'	    =>	'property'
              );

              // Title
              if( isset ( $myproperty['inspiry_property_title'] ) && ! empty ( $myproperty['inspiry_property_title'] ) ) {
                  $new_property['post_title']	= sanitize_text_field( $myproperty['inspiry_property_title'] );
              }

              // Description
              if( isset ( $myproperty['description'] ) && ! empty ( $myproperty['description'] ) ) {
                  $new_property['post_content'] = wp_kses_post( $myproperty['description'] );
              }

              // Author
              global $current_user;
              get_currentuserinfo();
              $new_property['post_author'] = $current_user->ID;


              /* check the type of action */
              $action = $myproperty['action'];
              $property_id = 0;

              if( $action == "add_property" ){
                  // $submitted_property_status = get_option( 'theme_submitted_status' );
                  $submitted_property_status = 'publish';
                  if ( !empty( $submitted_property_status ) ) {
                      $new_property['post_status'] = $submitted_property_status;
                  } else {
                      $new_property['post_status'] = 'pending';
                  }
                  $property_id = wp_insert_post( $new_property ); // Insert Property and get Property ID
                  if( $property_id > 0 ){
                      $submitted_successfully = true;
                      do_action( 'wp_insert_post', 'wp_insert_post' ); // Post the Post
                  }
              } else if( $action == "update_property" ) {
                  $new_property['ID'] = intval( $myproperty['property_id'] );
                  $property_id = wp_update_post( $new_property ); // Update Property and get Property ID
                  if( $property_id > 0 ){
                      $updated_successfully = true;
                      echo '<h1 style="background-color: orange;">'.$updated_successfully.' - '.$property_id.'</h1>';
                  }
              } else if( $action == "delete_property" ) {
                  $del_property['ID'] = intval( $myproperty['property_id'] );
                  delete_post_meta( $del_property['ID'], 'REAL_HOMES_property_images' );
                  delete_post_meta( $del_property['ID'], '_thumbnail_id' );
                  $property_id = wp_delete_post( $del_property['ID'] ); // Delete Property with supplied property ID
              }

              if( $property_id > 0 ){

                  // Attach Property Type with Newly Created Property
                  if( isset( $myproperty['type'] ) && ( $myproperty['type'] != "-1" ) ) {
                      wp_set_object_terms( $property_id, $myproperty['type'], 'property-type' );
                  }

                  // Attach Property City with Newly Created Property
                  // If a city does not exist in the city table, it creates it
                  $location_select_names = inspiry_get_location_select_names();
                  $locations_count = count( $location_select_names );
                  for ( $l = $locations_count - 1; $l >= 0; $l-- ) {
                      if ( isset( $myproperty[ $location_select_names[$l] ] ) ) {
                          $current_location = $myproperty[ $location_select_names[ $l ] ];
                          if( ( ! empty ( $current_location ) ) && ( $current_location != 'any' ) ){
                              wp_set_object_terms( $property_id, $current_location, 'property-city' );
                              break;
                          }
                      }
                  }

                  // Attach Property Status with Newly Created Property
                  if( isset( $myproperty['status'] ) && ( $myproperty['status'] != "-1" ) ) {
                      wp_set_object_terms( $property_id, intval( $myproperty['status'] ), 'property-status' );
                  }

                  // Attach Property Features with Newly Created Property
                  if( isset( $myproperty['features'] ) ) {
                      if( ! empty( $myproperty['features'] ) && is_array( $myproperty['features'] ) ) {
                          $property_features = array();
                          foreach( $myproperty['features'] as $property_feature_id ) {
                              $property_features[] = intval( $property_feature_id );
                          }
                          wp_set_object_terms( $property_id , $property_features, 'property-feature' );
                      }
                  }

                  // Attach Price Post Meta
                  if( isset ( $myproperty['price'] ) && ! empty ( $myproperty['price'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_property_price', sanitize_text_field( $myproperty['price'] ) );

                      if( isset ( $myproperty['price-postfix'] ) && ! empty ( $myproperty['price-postfix'] ) ) {
                          update_post_meta( $property_id, 'REAL_HOMES_property_price_postfix', sanitize_text_field( $myproperty['price-postfix'] ) );
                      }
                  }

                  // Attach Size Post Meta
                  if( isset ( $myproperty['size'] ) && ! empty ( $myproperty['size'] ) ) {
                      update_post_meta($property_id, 'REAL_HOMES_property_size', sanitize_text_field ( $myproperty['size'] ) );

                      if( isset ( $myproperty['area-postfix'] ) && ! empty ( $myproperty['area-postfix'] ) ) {
                          update_post_meta( $property_id, 'REAL_HOMES_property_size_postfix', sanitize_text_field( $myproperty['area-postfix'] ) );
                      }
                  }

                  // Attach Bedrooms Post Meta
                  if( isset ( $myproperty['bedrooms'] ) && ! empty ( $myproperty['bedrooms'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_property_bedrooms', floatval( $myproperty['bedrooms'] ) );
                  }

                  // Attach Bathrooms Post Meta
                  if( isset ( $myproperty['bathrooms'] ) && ! empty ( $myproperty['bathrooms'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_property_bathrooms', floatval( $myproperty['bathrooms'] ) );
                  }

                  // Attach Garages Post Meta
                  if( isset ( $myproperty['garages'] ) && ! empty ( $myproperty['garages'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_property_garage', floatval( $myproperty['garages'] ) );
                  }

                  // Attach Address Post Meta
                  if( isset ( $myproperty['address'] ) && ! empty ( $myproperty['address'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_property_address', sanitize_text_field( $myproperty['address'] ) );
                  }

                  // Attach Address Post Meta
                  if( isset ( $myproperty['coordinates'] ) && ! empty ( $myproperty['coordinates'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_property_location', $myproperty['coordinates'] );
                  }

                  // Agent Display Option
                  if( isset ( $myproperty['agent_display_option'] ) && ! empty ( $myproperty['agent_display_option'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_agent_display_option', $myproperty['agent_display_option']);
                      if( ($myproperty['agent_display_option'] == "agent_info") && isset( $myproperty['agent_id'] ) ){
                          update_post_meta( $property_id, 'REAL_HOMES_agents', $myproperty['agent_id'] );
                      }
                  }

                  // Attach Property ID Post Meta
                  if( isset ( $myproperty['property-id'] ) && ! empty ( $myproperty['property-id'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_property_id', sanitize_text_field( $myproperty['property-id'] ) );
                  }

                  // Attach Virtual Tour Video URL Post Meta
                  if( isset ( $myproperty['video-url'] ) && ! empty ( $myproperty['video-url'] ) ) {
                      update_post_meta( $property_id, 'REAL_HOMES_tour_video_url', esc_url_raw( $myproperty['video-url'] ) );
                  }

                  // Attach additional details with property
                  if( isset( $myproperty['detail-titles'] ) && isset( $myproperty['detail-values'] ) ) {

                      $additional_details_titles = $myproperty['detail-titles'];
                      $additional_details_values = $myproperty['detail-values'];

                      $titles_count = count ( $additional_details_titles );
                      $values_count = count ( $additional_details_values );

                      // to skip empty values on submission
                      if ( $titles_count == 1 && $values_count == 1 && empty ( $additional_details_titles[0] ) && empty ( $additional_details_values[0] ) ) {
                          // do nothing and let it go
                      } else {

                          if( ! empty( $additional_details_titles ) && ! empty( $additional_details_values ) ) {
                              $additional_details = array_combine( $additional_details_titles, $additional_details_values );
                              update_post_meta( $property_id, 'REAL_HOMES_additional_details', $additional_details );
                          }

                      }

                  }

                  // Attach Property as Featured Post Meta
                  $featured = ( isset( $myproperty['featured'] ) ) ? $myproperty['featured'] : 0 ;
                  if ( $featured ) {
                      update_post_meta( $property_id, 'REAL_HOMES_featured', $featured );
                  }

                  // Tour video image - in case of update
                  $tour_video_image = "";
                  $tour_video_image_id = 0;
                  if( $action == "update_property" ) {
                      $tour_video_image_id = get_post_meta( $property_id, 'REAL_HOMES_tour_video_image', true );
                      if ( ! empty ( $tour_video_image_id ) ) {
                          $tour_video_image_src = wp_get_attachment_image_src( $tour_video_image_id, 'property-detail-video-image' );
                          $tour_video_image = $tour_video_image_src[0];
                      }
                  }

                  // if property is being updated, clean up the old meta information related to images
                  /*
                  if( $action == "update_property" ){
                      delete_post_meta( $property_id, 'REAL_HOMES_property_images' );
                      delete_post_meta( $property_id, '_thumbnail_id' );
                  }
                  */

                  // Attach gallery images with newly created property
                  if ( isset( $myproperty['gallery_image_ids'] ) ) {
                      if( ! empty ( $myproperty['gallery_image_ids'] ) && is_array ( $myproperty['gallery_image_ids'] ) ) {
                          $gallery_image_ids = array();
                          foreach ( $myproperty['gallery_image_ids'] as $gallery_image_id ) {
                              $gallery_image_ids[] = intval( $gallery_image_id );
                              add_post_meta( $property_id, 'REAL_HOMES_property_images', $gallery_image_id );
                          }
                          if ( isset( $myproperty['featured_image_id'] ) ) {
                              $featured_image_id = intval( $myproperty['featured_image_id'] );
                              if ( in_array( $featured_image_id, $gallery_image_ids ) ) {     // validate featured image id
                                  update_post_meta ( $property_id, '_thumbnail_id', $featured_image_id );

                                  /* if video url is provided but there is no video image then use featured image as video image */
                                  if ( empty( $tour_video_image ) && !empty( $myproperty['video-url'] ) ) {
                                      update_post_meta( $property_id, 'REAL_HOMES_tour_video_image', $featured_image_id );
                                  }
                              }
                          } else if( ! empty ( $gallery_image_ids ) ) {
                              update_post_meta ( $property_id, '_thumbnail_id', $gallery_image_ids[0] );
                          }
                      }
                  } // end gallery if

              }

      }
  }
  $count++;
}

// get_header();
// get_footer();
?>

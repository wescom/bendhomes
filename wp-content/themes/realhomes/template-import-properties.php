<?php
/*
*  Template Name: Import RETS Properties
*/
/*
*  Author: Justin Grady
*/

ini_set('max_execution_time', 0);
date_default_timezone_set('America/Los_Angeles');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* #### INCLUDES ##### */
include_once ABSPATH . 'wp-admin/includes/media.php';
include_once ABSPATH . 'wp-admin/includes/file.php';
include_once ABSPATH . 'wp-admin/includes/image.php';
include_once WP_PLUGIN_DIR . '/'.'bh-importer/functions.php';

$theTm = time();
bh_write_to_log('Entered template-import-properties.php ','propertiesUpdateEntry'.$theTm."_".$_SERVER['REMOTE_ADDR']);

function dataPreProc($proparr,$scenarioset) {
  $count = 0;

  $raw_property_count = count($proparr);
  // echo '<h1>raw property count: '.$raw_property_count.'</h1>';
  /* #### PROPERTY DATA LOOP ##### */
  $retsproperties = array(); // first declaration

  foreach($proparr as $propitem) {

    /*
    echo '<pre style="border: 1px solid #333; padding: 10px; background-color: #cad446; margin: 0;">';
    echo 'status: ';
    print_r($propitem['Status']);
    echo '<br/>';
    echo 'count: '.$count.'<br/>';
    echo 'ml number: ';
    print_r($propitem['MLNumber']);
    echo '</pre>';
    */

    // status use cases
    // DECIDE what to do with pre-existing records
    // update, delete

    $mlsposts = bhLookupPostByMLS($propitem['MLNumber']);
    $bhpropertyid = $mlsposts[0];
    $postaction = bhPostActions($propitem['Status'],$bhpropertyid);

    $propname = $propitem['StreetNumber'].' '.$propitem['StreetNumberModifier'].' '.$propitem['StreetName'].' '.$propitem['StreetSuffix'].', '.$propitem['City'].', '.$propitem['State'].' '.$propitem['ZipCode'];
    $propname = trim($propname);
    $propname = preg_replace('!\s+!', ' ', $propname); // if multiple spaces, replace with one space

    // // end use cases
    // add_property
    // skip_property
    // update_property
    // delete_property
    echo "\n\r"."postaction: ".$postaction."\n\r";
    if($postaction == 'delete_property' || $postaction == 'skip_property') {
      $retsproperties[$propitem['ListingRid']]['action'] = $postaction;
      $retsproperties[$propitem['ListingRid']]['property_id'] = $bhpropertyid;
      $retsproperties[$propitem['ListingRid']]['property-id'] = $propitem['MLNumber']; // this the the MLS ID
      $retsproperties[$propitem['ListingRid']]['inspiry_property_title'] = $propname;
    } elseif ($postaction == 'add_property' || $postaction == 'update_property') {
      $propprice = formatprice($propitem['ListingPrice']);

      $agentguid = 'agent_'.$propitem['ListingAgentNumber'];
      $agentposts = bhLookupAgent($agentguid);

      $bhagentid = $agentposts[0];
      $bhagentid = $bhagentid->{'ID'};
      $bhagentfullname = $agentposts[0];
      $bhagentfullname = $bhagentfullname->{'post_title'};

      if($propitem['ShowAddressToPublic'] == '0') {
        $bhpublicaddressflag = 'no';
      } else {
        $bhpublicaddressflag = 'yes';
      }

      $bhagentdisplayoption = 'agent_info'; // my_profile_info, agent_info, none
      // $bhmarketingremarks = $propitem['MarketingRemarks'].'<br/><br/><strong>Listing Agent: </strong><br/>'.$bhagentfullname.'<br/>'.$propitem['ListingOfficeName'];
      $bhmarketingremarks = $propitem['MarketingRemarks'];
      // if MLS give no coordinates, they set them to zeroes, trap that. Don't submit to importer
      if(strpos($propitem['Latitude'],'0.0') === true || strpos($propitem['Latitude'],'0.0') === true) {
        $bhcoordinates = NULL;
      } else {
        $bhcoordinates = $propitem['Latitude'].','.$propitem['Longitude'];
      }

      if ($propitem['Status'] == "Active")
          $statusVal = "for-sale";
      else 
          $statusVal = $propitem['Status'];

      switch ($scenarioset['name']) {
      	case "OpenHouse_OPEN":
      		break;
      	case "Property_BUSI":
          $proptype = (!empty($propitem['BUSITYPE']) ? empty($propitem['BUSITYPE']) : $propitem['PropertyType']);
          // START Property_BUSI import template
          $retsproperties[$propitem['ListingRid']] = array(
            'inspiry_property_title' => $propname,
            'show_address_to_public' => $bhpublicaddressflag,
            'description' => $bhmarketingremarks,
            'type' => bhLookupPropertyType($proptype),
            'status' => $statusVal,
            'location' => $propitem['City'],
            'property-id' => $propitem['MLNumber'], // this the the MLS ID
            'property_id' => $bhpropertyid, // this is the WP post id if there's record update
            'price' => $propprice,
            'price-postfix' => '',
            'video-url' => $propitem['VirtualTourURL'],
            'address' => $propname,
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction, // give api db status, and pre-existing wp id, if exists
			
			'listing-date' => $propitem['ListingDate'],
			'county' => $propitem['County'],
			'area' => $propitem['Area'],
			'cross_street' => $propitem['CrossStreetAddress'],
			'construction_type' => $propitem['BUSICONS'],
			'existing_water' => $propitem['BUSIEXIS'],
			'foundation' => $propitem['BUSIFOUN'],
			'sale_inclusions' => $propitem['BUSIFFE1'] .' '. $propitem['BUSIFFE2'] .' '. $propitem['BUSIFFE3'],
			'lease' => $propitem['BUSILEAS'],
			'business_sale' => $propitem['BUSISALE'],
			'sewer' => $propitem['BUSISEWR'],
			'seller_terms' => $propitem['BUSITERM'],
			'zoning' => $propitem['BUSIZONE'],
          );
          // END Property_BUSI import template
      		break;
      	case "Property_COMM":
          // START Property_COMM import template
          $retsproperties[$propitem['ListingRid']] = array(
            'inspiry_property_title' => $propname,
            'show_address_to_public' => $bhpublicaddressflag,
            'description' => $bhmarketingremarks,
            'type' => bhLookupPropertyType($propitem['COMMTYPE']),
            'status' => $statusVal,
            'location' => $propitem['City'],
            'property-id' => $propitem['MLNumber'], // this the the MLS ID
            'property_id' => $bhpropertyid, // this is the WP post id if there's record update
            'price' => $propprice,
            'price-postfix' => '',
			'size' => $propitem['SquareFootage'],
            'video-url' => $propitem['VirtualTourURL'],
            'address' => $propname,
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction, // give api db status, and pre-existing wp id, if exists
			
			'listing-date' => $propitem['ListingDate'],
			'county' => $propitem['County'],
			'area' => $propitem['Area'],
			'acres' => $propitem['Acres'],
			'cross_street' => $propitem['CrossStreetAddress'],
			'levels' => $propitem['COMMNOFL'],
			'ccrs' => $propitem['COMMCCR'],
			'construction_type' => $propitem['COMMCONS'],
			'electric_company' => $propitem['COMMELEC'],
			'existing_water' => $propitem['COMMEXIS'],
			'water_district' => $propitem['COMMWTRD'],
			'flooring' => $propitem['COMMFLOR'],
			'roofing' => $propitem['COMMROOF'],
			'foundation' => $propitem['COMMFOUN'],
			'heating_cooling' => $propitem['COMMHTCO'],
			'lot_number' => $propitem['COMMLOTN'],
			'new_construction' => $propitem['COMMNEWC'],
			'parking' => $propitem['COMMPARK'],
			'subtype' => $propitem['PropertySubtype1'],
			'sewer' => $propitem['COMMSEWR'],
			'tax_amount' => $propitem['COMMTAXE'],
			'tax_year' => $propitem['COMMTAXY'],
			'seller_terms' => $propitem['COMMTERM'],
			'exterior_view' => $propitem['COMMVIEW'],
			'office_type' => $propitem['COMMTYPE'],
			'year_built' => $propitem['YearBuilt'],
          );
          // END Property_COMM import template
      		break;
      	case "Property_FARM":
          // START Property_FARM import template
          $retsproperties[$propitem['ListingRid']] = array(
            'inspiry_property_title' => $propname,
            'show_address_to_public' => $bhpublicaddressflag,
            'description' => $bhmarketingremarks,
            'type' => bhLookupPropertyType($propitem['PropertyType']),
            'status' => $statusVal,
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
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'features' => bhLookupFeatures($propitem['FARMINTE'],$propitem['FARMEXTE']),
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction, // give api db status, and pre-existing wp id, if exists
			
			'listing-date' => $propitem['ListingDate'],
			'county' => $propitem['County'],
			'area' => $propitem['Area'],
			'acres' => $propitem['Acres'],
			'cross_street' => $propitem['CrossStreetAddress'],
			'additions' => $propitem['FARMADDI'],
			'construction_type' => $propitem['FARMCONS'],
			'current_use' => $propitem['FARMCRP1'] .' '. $propitem['FARMCRP2'] .' '. $propitem['FARMCRP3'] .' '. $propitem['FARMCRP4'] .' '. $propitem['FARMAMT'],
			'directions' => $propitem['Directions'],
			'existing_water' => $propitem['FARMEXIS'],
			'water_district' => $propitem['FARMWTRD'],
			'electric_company' => $propitem['FARMELEC'],
			'elem_school' => $propitem['FARMELMS'],
			'mid_school' => $propitem['FARMJRHI'],
			'high_school' => $propitem['FARMSRHI'],
			'sale_exclusions' => $propitem['FARMEXC1'] .' '. $propitem['FARMEXC2'] .' '. $propitem['FARMEXC3'] .' '. $propitem['FARMEXC4'],
			'sale_inclusions' => $propitem['FARMINC1'] .' '. $propitem['FARMINC2'] .' '. $propitem['FARMINC3'] .' '. $propitem['FARMINC4'],
			'exempt' => $propitem['FARMEXEM'],
			'exterior_features' => $propitem['FARMEXTE'],
			'rooms' => $propitem['FARMROOM'],
			'interior' => $propitem['FARMINTE'],
			'flooring' => $propitem['FARMFLOR'],
			'roofing' => $propitem['FARMROOF'],
			'foundation' => $propitem['FARMFOUN'],
			'heating_cooling' => $propitem['FARMHTCO'],
			'irrigation' => $propitem['FARMIRRG'],
			'irrigation_acres' => $propitem['FARMIRRA'],
			'levels' => $propitem['FARMLEVL'],
			'subtype' => $propitem['PropertySubtype1'],
			'seller_disclosure' => $propitem['FARMSELD'],
			'soil' => $propitem['FARMSOIL'],
			'exterior_style' => $propitem['FARMSTYL'],
			'tax_amount' => $propitem['FARMTAXE'],
			'seller_terms' => $propitem['FARMTERM'],
			'topography' => $propitem['FARMTOPO'],
			'exterior_view' => $propitem['FARMVIEW'],
			'year_built' => $propitem['YearBuilt'],
			'zoning' => $propitem['FARMZONI'],
          );
          // END Property_FARM import template
      		break;
      	case "Property_LAND":
          // START Property_LAND import template
          $retsproperties[$propitem['ListingRid']] = array(
            'inspiry_property_title' => $propname,
            'show_address_to_public' => $bhpublicaddressflag,
            'description' => $bhmarketingremarks,
            'type' => bhLookupPropertyType($propitem['PropertySubtype1']),
            'status' => $statusVal,
            'location' => $propitem['City'],
            'property-id' => $propitem['MLNumber'], // this the the MLS ID
            'property_id' => $bhpropertyid, // this is the WP post id if there's record update
            'price' => $propprice,
            'price-postfix' => '',
			'size' => $propitem['LotSquareFootage'],
            'video-url' => $propitem['VirtualTourURL'],
            'address' => $propname,
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction, // give api db status, and pre-existing wp id, if exists
			
			'listing-date' => $propitem['ListingDate'],
			'county' => $propitem['County'],
			'area' => $propitem['Area'],
			'acres' => $propitem['Acres'],
			'ccrs' => $propitem['LANDCCR'],
			'included_two' => $propitem['LANDCOMM'],
			'cross_street' => $propitem['CrossStreetAddress'],
			'electric_company' => $propitem['LANDELEC'],
			'existing_water' => $propitem['LANDEXIS'],
			'elem_school' => $propitem['LANDELMS'],
			'mid_school' => $propitem['LANDJRHI'],
			'high_school' => $propitem['LANDSRHI'],
			'hoa' => $propitem['LANDHOA'],
			'hoa_amount' => $propitem['LANDHOAD'],
			'hoa_per' => $propitem['LANDHOAP'],
			'irrigation' => $propitem['LANDIRRI'],
			'irrigation_acres' => $propitem['LANDIRRA'],
			'lot_number' => $propitem['LANDLOTN'],
			'subtype' => $propitem['PropertySubtype1'],
			'road_type' => $propitem['LANDROAD'],
			'water_district' => $propitem['LANDWTRC'],
			'sewer' => $propitem['LANDSEWR'] .' '. $propitem['LANDSEW1'],
			'subdivision' => $propitem['Subdivision'],
			'tax_amount' => $propitem['LANDTAXE'],
			'seller_terms' => $propitem['LANDTERM'],
			'utilities' => $propitem['LANDUTL1'] .' '. $propitem['LANDUTL2'],
			'zoning' => $propitem['LANDZONE'] .', '. $propitem['LANDZON1'],
			'current_use' => $propitem['LANDCURU']
          );
          // END Property_LAND import template
      		break;
        case "Property_MULT":
          // START Property_MULT import template
          $retsproperties[$propitem['ListingRid']] = array(
            'inspiry_property_title' => $propname,
            'show_address_to_public' => $bhpublicaddressflag,
            'description' => $bhmarketingremarks,
            'type' => bhLookupPropertyType($propitem['PropertySubtype1']),
            'status' => $statusVal,
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
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'features' => bhLookupFeatures($propitem['MULTINTE'],$propitem['MULTEXTE']),
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction, // give api db status, and pre-existing wp id, if exists
			
			'listing-date' => $propitem['ListingDate'],
			'county' => $propitem['County'],
			'area' => $propitem['Area'],
			'acres' => $propitem['Acres'],
			'ccrs' => $propitem['MULTCCR'],
			'lot_number' => $propitem['MULTLOTN'],
			'included_two' => $propitem['MULTCOMM'],
			'construction_type' => $propitem['MULTCONS'],
			'cross_street' => $propitem['CrossStreetAddress'],
			'electric_company' => $propitem['MULTELEC'],
			'exempt' => $propitem['MULTEXEM'],
			'existing_water' => $propitem['MULTEXIS'],
			'water_district' => $propitem['MULTWTRD'],
			'sewer' => $propitem['MULTSEWR'],
			'exterior_features' => $propitem['MULTEXTE'],
			'flooring' => $propitem['MULTFLOR'],
			'roofing' => $propitem['MULTROOF'],
			'foundation' => $propitem['MULTFOUN'],
			'heating_cooling' => $propitem['MULTHTCO'],
			'interior' => $propitem['MULTINTE'],
			'levels' => $propitem['MULTLEVL'],
			'new_construction' => $propitem['MULTNEWC'],
			'subtype' => $propitem['PropertySubtype1'],
			'exterior_style' => $propitem['MULTISTYL'],
			'exterior_view' => $propitem['MULTVIEW'],
			'subdivision' => $propitem['Subdivision'],
			'tax_amount' => $propitem['MULTTAXE'],
			'seller_terms' => $propitem['MULTTERM'],
			'year_built' => $propitem['YearBuilt'],
			'zoning' => $propitem['MULTZONI'],
			'number_units' => $propitem['MULTTONU']
          );
          // END Property_MULT import template
      		break;
        case "Property_RESI":
          // START Property_RESI import template
          $retsproperties[$propitem['ListingRid']] = array(
            'inspiry_property_title' => $propname,
            'show_address_to_public' => $bhpublicaddressflag,
            'description' => $bhmarketingremarks,
            'type' => bhLookupPropertyType($propitem['PropertyType']),
            'status' => $statusVal,
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
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'features' => bhLookupFeatures($propitem['RESIINTE'],$propitem['RESIEXTE']),
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction, // give api db status, and pre-existing wp id, if exists
			
			'listing-date' => $propitem['ListingDate'],
			'county' => $propitem['County'],
			'area' => $propitem['Area'],
			'acres' => $propitem['Acres'],
			'additions' => $propitem['RESIADDI'],
			'year_built' => $propitem['YearBuilt'],
			'zoning' => $propitem['RESIZONE'],
			'included' => $propitem['RESIADDI'] .', '. $propitem['RESIINC1'],
			'included_two' => $propitem['RESICOMM'],
			'ccrs' => $propitem['RESICCR'],
			'construction_type' => $propitem['RESICONS'],
			'cross_street' => $propitem['CrossStreetAddress'],
			'electric_company' => $propitem['RESIELEC'],
			'elem_school' => $propitem['RESIELMS'],
			'mid_school' => $propitem['RESIJRHI'],
			'high_school' => $propitem['RESISRHI'],
			'existing_water' => $propitem['RESIEXIS'],
			'water_district' => $propitem['RESIWTRD'],
			'sewer' => $propitem['RESISEWR'],
			'exterior_features' => $propitem['RESIEXTE'],
			'exterior_view' => $propitem['RESIVIEW'],
			'flooring' => $propitem['RESIFLOR'],
			'foundation' => $propitem['RESIFOUN'],
			'rooms' => $propitem['RESIROOM'],
			'heating_cooling' => $propitem['RESIHTCO'],
			'roofing' => $propitem['RESIROOF'],
			'hoa' => $propitem['RESIHOA'],
			'hoa_amount' => $propitem['RESIHOAD'],
			'hoa_per' => $propitem['RESIHOAP'],
			'interior' => $propitem['RESIINTE'],
			'irrigation' => $propitem['RESIIRRI'],
			'irrigation_acres' => $propitem['RESIIRRA'],
			'levels' => $propitem['RESILEVL'],
			'lot_number' => $propitem['RESILOTN'],
			'new_construction' => $propitem['RESINEWC'],
			'subtype' => $propitem['PropertySubtype1'],
			'exterior_style' => $propitem['RESISTYL'],
			'tax_amount' => $propitem['RESITAXS'],
			'seller_terms' => $propitem['RESITERM'],
			'seller_disclosure' => $propitem['RESISELD'],
			'exempt' => $propitem['RESIEXEM'],
			'shared_interest' => $propitem['RESISHRP'],
			'subdivision' => $propitem['Subdivision']
          );
          // END Property_RESI import template
      		break;
      } // end swich statement

      if($postaction == 'add_property') {
        $bhimgids = bhImageSet($propitem);
        $retsproperties[$propitem['ListingRid']]['gallery_image_ids'] = $bhimgids;
        $retsproperties[$propitem['ListingRid']]['featured_image_id'] = $bhimgids[0];
      }

      unset($bhimgids);
    } // end $postaction ifelse

    $retsproperties[$propitem['ListingRid']]['property-mlstatus'] = $propitem['Status'];
    $data_to_insert = $retsproperties[$propitem['ListingRid']];
    // echo '<h1>'.$data_to_insert['action'].'</h1>';
    // echo '<pre style="background-color: #ececec; padding: 0.25em; border-radius: 0.25em;">';
    // print_r($data_to_insert);
    // echo '</pre>';
    // usleep(500000); // 1/2 second sleep
    //bh_write_to_log('mls: '.$propitem['MLNumber'],'properties');
    dataPropertyWPinsert($data_to_insert);
    // sleep(1);
    unset($data_to_insert);

    $count++;
  } // end $propitem forach
  // $log = $scenarioset['name'].' - '.$count.' properties - '.$postaction;
  // bh_write_to_log("\t".$log,'properties');
  return $retsproperties;
}

function dataPropertyWPinsert($myproperty) {

  $invalid_nonce = false;
  $submitted_successfully = false;
  $updated_successfully = false;


  bh_write_to_log('  dataPropertyWPinsert with: '.$myproperty['property-id'].' status: '.$myproperty['property-mlstatus'],'properties');

  echo '<pre style="border: 1px solid #000; padding: 10px;">';

  echo 'action: '.$myproperty['action']."<br/>\n";
  echo 'title: '.$myproperty['inspiry_property_title']."<br/>\n";
  echo 'MLS number: '.$myproperty['property-id']."<br/>\n";
  echo 'ML status: '.$myproperty['property-mlstatus']."<br/>\n";
  echo '</pre>';

  /* Check if action field is set  */
  if( isset( $myproperty['action'] ) ) {

      if( $myproperty['action'] != 'skip_property' ) {
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
                // echo '<h1 style="background-color: cyan;">'.$updated_successfully.' - '.$property_id.'</h1>';
            }
        } else if( $action == "delete_property" ) {
            $del_property['ID'] = intval( $myproperty['property_id'] );
            echo '<h1>'.$action.' - '.$del_property['ID'].'</h1>';
            // delete (unlink) all images, and remove their posts and metadata
            do_action( 'before_delete_post', $del_property['ID'] );
            // delete all post metadata
            echo '<span style="color: blue;">post id that has postmeta and post deleted: '.$del_property['ID'].'</span><br/>';
            // delete_all_post_meta( $del_property['ID'] );
            // delete the post itself
            $property_id = wp_delete_post( $del_property['ID'] ); // Delete Property with supplied property ID
        }

        echo '<pre>';
        print_r($property_id);
        echo '<br/>';

        if( is_int($property_id) ) {

          if( $property_id > 0 ) {

              echo 'test import is_int';

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
                  wp_set_object_terms( $property_id, $myproperty['status'], 'property-status' );
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
			  
			  // Attach Listing Date Post Meta
              if( isset ( $myproperty['listing-date'] ) && ! empty ( $myproperty['listing-date'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_listing_date', sanitize_text_field( $myproperty['listing-date'] ) );
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
                  update_post_meta( $property_id, 'REAL_HOMES_property_garage', sanitize_text_field( $myproperty['garages'] ) );
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
                  if( isset( $myproperty['agent_id'] ) ){
                      update_post_meta( $property_id, 'REAL_HOMES_agents', $myproperty['agent_id'] );
                  }
              }

              // Show address to public toggle
              if( isset ( $myproperty['show_address_to_public'] ) && ! empty ( $myproperty['show_address_to_public'] ) ) {
                  update_post_meta( $property_id, 'show_address_to_public', $myproperty['show_address_to_public']);
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
              /*
              $featured = ( isset( $myproperty['featured'] ) ) ? $myproperty['featured'] : 0 ;
              if ( $featured ) {
                  update_post_meta( $property_id, 'REAL_HOMES_featured', $featured );
              }
              */

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
			  
			  // Attach County Post Meta
              if( isset( $myproperty['county'] ) && ( $myproperty['county'] != "" ) ) {
                  wp_set_object_terms( $property_id, $myproperty['county'], 'county' );
              }
			  
			  // Attach Area Post Meta
              if( isset( $myproperty['area'] ) && ( $myproperty['area'] != "" ) ) {
                  wp_set_object_terms( $property_id, $myproperty['area'], 'area' );
              }
			  
			  // Attach Acres Post Meta
			  if( isset ( $myproperty['acres'] ) && ! empty ( $myproperty['acres'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_acres', sanitize_text_field( $myproperty['acres'] ) );
              }
			  
			  // Attach Additions Post Meta
			  if( isset ( $myproperty['additions'] ) && ! empty ( $myproperty['additions'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_additions', sanitize_text_field( $myproperty['additions'] ) );
              }
			  
			  // Attach Year Built Post Meta
			  if( isset ( $myproperty['year_built'] ) && ! empty ( $myproperty['year_built'] ) && ( $myproperty['year_built'] != '0' ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_year_built', sanitize_text_field( $myproperty['year_built'] ) );
              }
			  
			  // Attach Zoning Post Meta
			  if( isset ( $myproperty['zoning'] ) && ! empty ( $myproperty['zoning'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_zoning', sanitize_text_field( $myproperty['zoning'] ) );
              }
			  
			  // Attach Included Post Meta
			  if( isset ( $myproperty['included'] ) && ! empty ( $myproperty['included'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_included', sanitize_text_field( $myproperty['included'] ) );
              }
			  
			  // Attach Included 2 Post Meta
			  if( isset ( $myproperty['included_two'] ) && ! empty ( $myproperty['included_two'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_included2', sanitize_text_field( $myproperty['included_two'] ) );
              }
			  
			  // Attach CCRS Post Meta
			  if( isset ( $myproperty['ccrs'] ) && ! empty ( $myproperty['ccrs'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_included', sanitize_text_field( $myproperty['ccrs'] ) );
              }
			  
			  // Attach Construction Post Meta
			  if( isset ( $myproperty['construction_type'] ) && ! empty ( $myproperty['construction_type'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_construction_description', sanitize_text_field( $myproperty['construction_type'] ) );
              }
			  
			  // Attach Cross Street Post Meta
			  if( isset ( $myproperty['cross_street'] ) && ! empty ( $myproperty['cross_street'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_cross_street', sanitize_text_field( $myproperty['cross_street'] ) );
              }
			  
			  // Attach Electric Company Post Meta
			  if( isset ( $myproperty['electric_company'] ) && ! empty ( $myproperty['electric_company'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_electric_company', sanitize_text_field( $myproperty['electric_company'] ) );
              }
			  
			  // Attach Elementry School with Newly Created Property
              if( isset( $myproperty['elem_school'] ) && ( $myproperty['elem_school'] != "" ) ) {
                  wp_set_object_terms( $property_id, $myproperty['elem_school'], 'elementary_school' );
              }
			  
			  // Attach Middle School with Newly Created Property
              if( isset( $myproperty['mid_school'] ) && ( $myproperty['mid_school'] != "" ) ) {
                  wp_set_object_terms( $property_id, $myproperty['mid_school'], 'middle_school' );
              }
			  
			  // Attach High School with Newly Created Property
              if( isset( $myproperty['high_school'] ) && ( $myproperty['high_school'] != "" ) ) {
                  wp_set_object_terms( $property_id, $myproperty['high_school'], 'high_school' );
              }
			  
			  // Attach Existing Water Post Meta
			  if( isset ( $myproperty['existing_water'] ) && ! empty ( $myproperty['existing_water'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_existing_water', sanitize_text_field( $myproperty['existing_water'] ) );
              }
			  
			  // Attach Water District Post Meta
			  if( isset ( $myproperty['water_district'] ) && ! empty ( $myproperty['water_district'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_water_district', sanitize_text_field( $myproperty['water_district'] ) );
              }
			  
			  // Attach Sewer Post Meta
			  if( isset ( $myproperty['sewer'] ) && ! empty ( $myproperty['sewer'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_sewer_septic', sanitize_text_field( $myproperty['sewer'] ) );
              }
			  
			  // Attach Exterior Features Post Meta
			  if( isset ( $myproperty['exterior_features'] ) && ! empty ( $myproperty['exterior_features'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_exterior', sanitize_text_field( $myproperty['exterior_features'] ) );
              }
			  
			  // Attach Exterior View Post Meta
			  if( isset ( $myproperty['exterior_view'] ) && ! empty ( $myproperty['exterior_view'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_view', sanitize_text_field( $myproperty['exterior_view'] ) );
              }
			  
			  // Attach Heating Post Meta
			  if( isset ( $myproperty['flooring'] ) && ! empty ( $myproperty['flooring'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_interior_floors', sanitize_text_field( $myproperty['flooring'] ) );
              }
			  
			  // Attach Foundation Post Meta
			  if( isset ( $myproperty['foundation'] ) && ! empty ( $myproperty['foundation'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_foundation', sanitize_text_field( $myproperty['foundation'] ) );
              }
			  
			  // Attach Rooms Post Meta
			  if( isset ( $myproperty['rooms'] ) && ! empty ( $myproperty['rooms'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_interior_rooms', sanitize_text_field( $myproperty['rooms'] ) );
              }
			  
			  // Attach Heating/Cooling Post Meta
			  if( isset ( $myproperty['heating_cooling'] ) && ! empty ( $myproperty['heating_cooling'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_interior_heat_cool', sanitize_text_field( $myproperty['heating_cooling'] ) );
              }
			  
			  // Attach Roofing Post Meta
			  if( isset ( $myproperty['roofing'] ) && ! empty ( $myproperty['roofing'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_roof', sanitize_text_field( $myproperty['roofing'] ) );
              }
			  
			  // Attach HOA Post Meta
			  if( isset ( $myproperty['hoa'] ) && ! empty ( $myproperty['hoa'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_hoa', sanitize_text_field( $myproperty['hoa'] ) );
              }
			  
			  // Attach HOA Amount Post Meta
			  if( isset ( $myproperty['hoa_amount'] ) && ! empty ( $myproperty['hoa_amount'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_hoa_amount', floatval( $myproperty['hoa_amount'] ) );
              }
			  
			  // Attach HOA Per Post Meta
			  if( isset ( $myproperty['hoa_per'] ) && ! empty ( $myproperty['hoa_per'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_hoa_per', sanitize_text_field( $myproperty['hoa_per'] ) );
              }
			  
			  // Attach Interior Features Post Meta
			  if( isset ( $myproperty['interior'] ) && ! empty ( $myproperty['interior'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_interior_interior', sanitize_text_field( $myproperty['interior'] ) );
              }
			  
			  // Attach Irrigation Post Meta
			  if( isset ( $myproperty['irrigation'] ) && ! empty ( $myproperty['irrigation'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_irrigation', sanitize_text_field( $myproperty['irrigation'] ) );
              }
			  
			  // Attach Irrigation Acres Post Meta
			  if( isset ( $myproperty['irrigation_acres'] ) && ( $myproperty['irrigation_acres'] != '0.00' ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_irrigated_acres', sanitize_text_field( $myproperty['irrigation_acres'] ) );
              }
			  
			  // Attach Levels Post Meta
			  if( isset ( $myproperty['levels'] ) &&  ! empty ( $myproperty['levels'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_interior_levels', sanitize_text_field( $myproperty['levels'] ) );
              }
			  
			  // Attach Lot Number Post Meta
			  if( isset ( $myproperty['lot_number'] ) &&  ! empty ( $myproperty['lot_number'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_lot_number', sanitize_text_field( $myproperty['lot_number'] ) );
              }
			  
			  // Attach New Construction Post Meta
			  if( isset ( $myproperty['new_construction'] ) &&  ! empty ( $myproperty['new_construction'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_new_construction', sanitize_text_field( $myproperty['new_construction'] ) );
              }
			  
			  // Attach Property Subtype Post Meta
			  if( isset ( $myproperty['subtype'] ) &&  ! empty ( $myproperty['subtype'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_subtype', sanitize_text_field( $myproperty['subtype'] ) );
              }
			  
			  // Attach Exterior Style Post Meta
			  if( isset ( $myproperty['exterior_style'] ) &&  ! empty ( $myproperty['exterior_style'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_exterior_style', sanitize_text_field( $myproperty['exterior_style'] ) );
              }
			  
			  // Attach Tax Amount Post Meta
			  if( isset ( $myproperty['tax_amount'] ) &&  ! empty ( $myproperty['tax_amount'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_tax_amount', sanitize_text_field( $myproperty['tax_amount'] ) );
              }
			  
			  // Attach Tax Amount Post Meta
			  if( isset ( $myproperty['tax_year'] ) &&  ! empty ( $myproperty['tax_year'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_tax_year', sanitize_text_field( $myproperty['tax_year'] ) );
              }
			  
			  // Attach Seller Terms Post Meta
			  if( isset ( $myproperty['seller_terms'] ) &&  ! empty ( $myproperty['seller_terms'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_terms', sanitize_text_field( $myproperty['seller_terms'] ) );
              }
			  
			  // Attach Seller Disclosure Post Meta
			  if( isset ( $myproperty['seller_disclosure'] ) &&  ! empty ( $myproperty['seller_disclosure'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_sellers_disclosure', sanitize_text_field( $myproperty['seller_disclosure'] ) );
              }
			  
			  // Attach Exempt Post Meta
			  if( isset ( $myproperty['exempt'] ) &&  ! empty ( $myproperty['exempt'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_exempt', sanitize_text_field( $myproperty['exempt'] ) );
              }
			  
			  // Attach Shared Interest Post Meta
			  if( isset ( $myproperty['shared_interest'] ) &&  ! empty ( $myproperty['shared_interest'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_percent_shared', sanitize_text_field( $myproperty['shared_interest'] ) );
              }
			  
			  // Attach Subdivision Post Meta
			  if( isset ( $myproperty['subdivision'] ) &&  ! empty ( $myproperty['subdivision'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_subdivision', sanitize_text_field( $myproperty['subdivision'] ) );
              }
			  
			  // Attach Number of Units Post Meta
			  if( isset ( $myproperty['number_units'] ) &&  ! empty ( $myproperty['number_units'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_number_units', sanitize_text_field( $myproperty['number_units'] ) );
              }
			  
			  // Attach Land Road Type Post Meta
			  if( isset ( $myproperty['road_type'] ) &&  ! empty ( $myproperty['road_type'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_property_features_road_type', sanitize_text_field( $myproperty['road_type'] ) );
              }
			  
			  // Attach Land Utilities Post Meta
			  if( isset ( $myproperty['utilities'] ) &&  ! empty ( $myproperty['utilities'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_utilities', sanitize_text_field( $myproperty['utilities'] ) );
              }
			  
			  // Attach Land Current Use Post Meta
			  if( isset ( $myproperty['current_use'] ) &&  ! empty ( $myproperty['current_use'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_current_use', sanitize_text_field( $myproperty['current_use'] ) );
              }
			  
			  // Attach Farm Directions Post Meta
			  if( isset ( $myproperty['directions'] ) &&  ! empty ( $myproperty['directions'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_directions', sanitize_text_field( $myproperty['directions'] ) );
              }
			  
			  // Attach Farm Sale Exclusions Post Meta
			  if( isset ( $myproperty['sale_exclusions'] ) &&  ! empty ( $myproperty['sale_exclusions'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_sale_exclusions', sanitize_text_field( $myproperty['sale_exclusions'] ) );
              }
			  
			  // Attach Farm Sale Inclusions Post Meta
			  if( isset ( $myproperty['sale_inclusions'] ) &&  ! empty ( $myproperty['sale_inclusions'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_sale_inclusions', sanitize_text_field( $myproperty['sale_inclusions'] ) );
              }
			  
			  // Attach Farm Soil Post Meta
			  if( isset ( $myproperty['soil'] ) &&  ! empty ( $myproperty['soil'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_soil', sanitize_text_field( $myproperty['soil'] ) );
              }
			  
			  // Attach Farm Topography Post Meta
			  if( isset ( $myproperty['topography'] ) &&  ! empty ( $myproperty['topography'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_topography', sanitize_text_field( $myproperty['topography'] ) );
              }
			  
			  // Attach Commercial Parking Post Meta
			  if( isset ( $myproperty['parking'] ) &&  ! empty ( $myproperty['parking'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_parking', sanitize_text_field( $myproperty['parking'] ) );
              }
			  
			  // Attach Commercial Office Type Post Meta
			  if( isset ( $myproperty['office_type'] ) &&  ! empty ( $myproperty['office_type'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_office_type', sanitize_text_field( $myproperty['office_type'] ) );
              }
			  
			  // Attach Business Lease Type Post Meta
			  if( isset ( $myproperty['lease'] ) &&  ! empty ( $myproperty['lease'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_lease', sanitize_text_field( $myproperty['lease'] ) );
              }
			  
			  // Attach Business Sale Post Meta
			  if( isset ( $myproperty['business_sale'] ) &&  ! empty ( $myproperty['business_sale'] ) ) {
                  update_post_meta( $property_id, 'REAL_HOMES_business_sale', sanitize_text_field( $myproperty['business_sale'] ) );
              }
			  
          }
        }
        echo '</pre>';
      }
  }
} // end wp insert function

bh_write_to_log('import start: '.date(DATE_RSS),'properties');
echo '<h1 style="border: 3px solid orange; padding: 3px;">bh_rets to WP import start - '.date(DATE_RSS).'</h1>';
foreach($scenarios as $scenario) {
  // echo '<p style="background-color: brown; color: #ffffff; padding: 0.25em;">'.$scenario['name'].'</p>';
  // echo '<pre>';
  // echo print_r($scenario);
  // echo '</pre>';
  // harvest raw rets database results, per table

  $retsApiResults = dbresult($scenario);

  /*$mlsArray = array();
  foreach($retsApiResults as $stuff) {
    //echo 'mls: '.$stuff['MLNumber']."\n\r";
    if (in_array($stuff['MLNumber'], $mlsArray)) {
        echo " REPEAT!!! : ".$stuff['MLNumber'];
    }
    else
      array_push($mlsArray, $stuff['MLNumber']);
  }*/
  // print_r($retsApiResults);
  // preprocess results to prep data for WP API inserts
  $retsPreProcResults = dataPreProc($retsApiResults,$scenario);

  // loop again to insert into WP posts
  // $do = dataPropertyWPinsert($retsPreProcResults);
  echo '<hr/>';

}

echo '<h1 style="border: 3px solid orange; padding: 3px;">bh_rets to WP import Complete</h1>';

bh_write_to_log('import end: '.date(DATE_RSS),'properties');
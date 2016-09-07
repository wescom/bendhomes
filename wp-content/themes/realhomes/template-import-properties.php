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

function dataPreProc($proparr,$scenarioset) {
  $count = 0;

  $raw_property_count = count($proparr);
  echo '<h1>raw property count: '.$raw_property_count.'</h1>';
  /* #### PROPERTY DATA LOOP ##### */
  $retsproperties = array(); // first declaration

  foreach($proparr as $propitem) {

    echo '<pre style="border: 1px solid #333; padding: 10px; background-color: #cad446; margin: 0;">';
    echo 'status: ';
    print_r($propitem['Status']);
    echo '<br/>';
    echo 'count: '.$count.'<br/>';
    echo 'ml number: ';
    print_r($propitem['MLNumber']);
    echo '</pre>';

    // status use cases
    // DECIDE what to do with pre-existing records
    // update, delete



    $mlsposts = bhLookupPostByMLS($propitem['MLNumber']);
    $bhpropertyid = $mlsposts[0];
    $postaction = bhPostActions($propitem['Status'],$bhpropertyid);

    $propname = $propitem['StreetNumber'].' '.$propitem['StreetNumberModifier'].' '.$propitem['StreetName'].' '.$propitem['StreetSuffix'].', '.$propitem['City'].', '.$propitem['State'].' '.$propitem['ZipCode'];
    $propname = trim($propname);

    $retsproperties[$propitem['ListingRid']]['property-mlstatus'] = $propitem['Status'];

    // // end use cases
    // add_property
    // skip_property
    // update_property
    // delete_property
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
      $bhagentid = $bhagentid->{ID};
      $bhagentfullname = $agentposts[0];
      $bhagentfullname = $bhagentfullname->{post_title};

      // echo '<h2 style="color: red;">';
      // echo $propitem['ShowAddressToPublic'];
      // echo '</h2>';

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

      switch ($scenarioset['name']) {
      	case "OpenHouse_OPEN":
      		break;
      	case "Property_BUSI":
          // START Property_BUSI import template
          $retsproperties[$propitem['ListingRid']] = array(
            'inspiry_property_title' => $propname,
            'show_address_to_public' => $bhpublicaddressflag,
            'description' => $bhmarketingremarks,
            'type' => bhLookupPropertyType($propitem['BUSITYPE']),
            'status' => 34,
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
            'action' => $postaction // give api db status, and pre-existing wp id, if exists
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
            'status' => 34,
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
            'action' => $postaction // give api db status, and pre-existing wp id, if exists
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
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'features' => bhLookupFeatures($propitem['FARMINTE'],$propitem['FARMEXTE']),
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction // give api db status, and pre-existing wp id, if exists
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
            'status' => 34,
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
            'action' => $postaction // give api db status, and pre-existing wp id, if exists
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
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'features' => bhLookupFeatures($propitem['MULTINTE'],$propitem['MULTEXTE']),
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction // give api db status, and pre-existing wp id, if exists
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
            'coordinates' => $bhcoordinates,
            // 'featured' => 0, // 0 == not featured, 1 == featured
            'features' => bhLookupFeatures($propitem['RESIINTE'],$propitem['RESIEXTE']),
            'agent_display_option' => $bhagentdisplayoption,
            'agent_id' => $bhagentid,
            'action' => $postaction // give api db status, and pre-existing wp id, if exists
          );
          // END Property_RESI import template
      		break;
      } // end swich statement

      if(($postaction == 'add_property') || ($postaction == 'update_property')) {
        $bhimgids = bhImageSet($propitem);
        $retsproperties[$propitem['ListingRid']]['gallery_image_ids'] = $bhimgids;
        $retsproperties[$propitem['ListingRid']]['featured_image_id'] = $bhimgids[0];
      }

      unset($bhimgids);
    } // end $postaction ifelse

    $data_to_insert = $retsproperties[$propitem['ListingRid']];
    // echo '<h1>'.$data_to_insert['action'].'</h1>';
    // echo '<pre style="background-color: #ececec; padding: 0.25em; border-radius: 0.25em;">';
    // print_r($data_to_insert);
    // echo '</pre>';
    // usleep(500000); // 1/2 second sleep
    bh_write_to_log('mls: '.$propitem['MLNumber'],'properties');
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
            do_action( 'before_delete_post', $del_property['ID'] ); // Post th
            // delete all post metadata
            delete_post_meta( $del_property['ID'] );
            // delete the post itself
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
          
            if( $action == "update_property" ){
                delete_post_meta( $property_id, 'REAL_HOMES_property_images' );
                delete_post_meta( $property_id, '_thumbnail_id' );
            }
            

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
} // end wp insert function

bh_write_to_log('import start: '.date(DATE_RSS),'properties');
// echo 'import start: '.date(DATE_RSS)."<br/>\n";
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
// echo 'import complete: '.date(DATE_RSS)."<br/>\n";
bh_write_to_log('import complete: '.date(DATE_RSS),'properties');

?>

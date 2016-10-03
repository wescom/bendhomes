<?php

define('DONOTCACHEPAGE',1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$lastDatePulled = 0;

/* ################################# */
/* #### DATA TYPES - SCENARIOS ##### */
/* ################################# */

$centralcount = 999999;
$scenarios = array(
  'Property_BUSI' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'BUSI',
    'name' => 'Property_BUSI'
  ),
  'Property_COMM' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'COMM',
    'name' => 'Property_COMM'
  ),
  'Property_FARM' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'FARM',
    'name' => 'Property_FARM'
  ),
  'Property_LAND' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'LAND',
    'name' => 'Property_LAND'
  ),
  'Property_MULT' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'MULT',
    'name' => 'Property_MULT'
  ),
  'Property_RESI' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'RESI',
    'name' => 'Property_RESI'
  )
);

if ( ! function_exists( 'delete_associated_media' ) ) {
  function delete_associated_media($post_id) {
    global $wpdb;
    $imgdir = ABSPATH.'wp-content/uploads/';

    echo 'media post id';
    print_r($post_id);

    // get post ids of all images
    $imageids = get_post_meta( $post_id, 'REAL_HOMES_property_images' );

    if (!empty($imageids)) {

      // query the db and get image path and filename
      foreach ($imageids as $imgid) {
          if($imgid != NULL) {
            //$sqlquery = "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = ".$imgid;
            // echo $sqlquery;
            // $results = $wpdb->get_results( $sqlquery, ARRAY_A );
            $results = NULL;
          } else {
            $results = NULL;
          }

          if($results != NULL) {
            foreach($results as $result) {
              if($result['meta_key'] == '_wp_attached_file' ) {
                $deletefile = $imgdir.$result['meta_value'];
                echo '<p style="color:red;">Deleting: '.$deletefile.'</p>';
                $froot = explode('.',$deletefile);
                $froot = $froot[0]; // we want of root of the filename with no extension
                foreach( glob($froot.'*') as $file )
                {
                    // this deletes all files with the orignal images name pattern, deletes WP versions
                    unlink($file);
                }

              }
              // delete the image post
              $delpost = wp_delete_post( $imgid );
            }
          }

          // wp_delete_attachment($attachment->ID);
          // unlink(get_attached_file($file->ID));
      }
    }
  }
  add_action('before_delete_post', 'delete_associated_media', 10, 1);
}

function agent_dbresult($sset) {

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
  $rc = $resource.'_'.$class;

  $fnamerecent = ABSPATH.'/_retsapi/pulldates/'.$rc.'.txt';
  if(file_exists($fnamerecent)) {
    $pulldate = file_get_contents($fnamerecent);
  } else {
    $pulldate = strtotime('-10 years');
  }

  $querydate = date('Y-m-d H:i:s',$pulldate);
  // echo $pulldate;
  /* AND images IS NOT NULL */
  /* AND lastPullTime >= '".$querydate."' */
  /* LIMIT 10 */

  /*
  $sqlquery = "SELECT * FROM ".$rc." WHERE
              IsActive = 'TRUE'
              ;";
  */

  $sqlquery = "SELECT ActiveAgent_MEMB.FullName,
              	ActiveAgent_MEMB.lastPullTime,
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
                WHERE ActiveAgent_MEMB.lastPullTime >= '".$querydate."'
                ;";

  echo '<pre>';
  print_r($sqlquery);
  echo '</pre>';

  /* Select queries return a resultset */

  if ($result = $mysqli->query($sqlquery)) {
      printf("Select returned %d rows.\n", $result->num_rows);
      while($row = $result->fetch_assoc()) {
          $data[] = $row;
      }
      // Frees the memory associated with a result
      $result->free();
  }

  $mysqli->close();
  return $data;

}

/* #### FUNCTIONS ##### */
function formatprice($price) {
  $pricearr = explode('.',$price);
  $newprice = $pricearr[0];
  $newprice = (int) $newprice;
  return $newprice;
}

function bhAgentPostAction($status,$id=NULL) {
  // // end use cases
  // add_agent
  // update_agent
  // delete_agent

  // if the agent already exists in db, then update it
  // works with feed statuses, see scenarios
  if($id != NULL) {
    $mlsaction = 'update';
  } else {
    $mlsaction = 'insert';
  }

  // insert from API use cases
  if($mlsaction == 'insert') {
    if($status == 'TRUE' || $status == 'T') {
      $apiaction = 'add_agent'; // only add if insert/Active are true, skip everywhere else
    }
    else
    {
      $apiaction = 'skip_agent'; // skip prop if insert, but is on list of prohibited statuses
    }
  }
  // update from API use cases
  elseif($mlsaction == 'update') {
    if($status == 'TRUE' || $status == 'T') {
      $apiaction = 'update_agent';
    }
    else
    {
      $apiaction = 'delete_agent';
    }
  }

  // echo '<p style="color: darkgreen">apiaction: '.$apiaction.' <br/>mlsid: '.$mlsid.' <br/>apifeedstat: '.$status.'</p>';
  return $apiaction;
}

function bhLookupAgent($guid) {
  if($guid != NULL) {
    global $wpdb;
    $guid = "'http://".$guid."'";
    // ID == , post_title == agent full name
    $sqlquery = "SELECT ID,post_title FROM $wpdb->posts WHERE guid = ".$guid;
    // echo $sqlquery;
    $result = $wpdb->get_results( $sqlquery );
  } else {
    $result = NULL;
  }
  //. echo '<pre> test222-agent<br/>';
  // print_r($result);
  // echo '</pre>';
  return $result;
}



/* from agent importer */
/*
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
*/

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
    $myid = $result[0]->{'term_id'};
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

  foreach($results as $result) {
    if(!empty($result[0]->{'term_id'})) {
      $output[] = $result[0]->{'term_id'};
    }
  }

  // strip empty keys from array
  $output = array_filter($output);
  // print_r($output);
  return $output;
}

function bhLookupTaxonomy($postid,$taxonomy) {
  $args = array(
    'orderby' => 'name',
    'order' => 'ASC',
    'fields' => 'all'
  );
  $query = wp_get_object_terms($postid, $taxonomy, $args);
  $output = $query[0]->{slug};
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
    if($status == 'Active' || $status == 'Pending' || $status == 'Sold' || $status == 'Contingent Bumpable') {
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
        $status == 'ShrtSale-BringBckUps' ||
        $status == 'Sold' ||
        $status == 'Contingent Bumpable')
    {
      $apiaction = 'update_property';
    }
    else
    {
      $apiaction = 'delete_property';
    }
  }

  // $apiaction = 'delete_property';

  echo '<p style="color: darkgreen">apiaction: '.$apiaction.' <br/>wordpress post id: '.$mlsid.' <br/>apifeedstat: '.$status.'</p>';
  return $apiaction;
}

/* ############################################################ */
/* #### CHECK FILE FOR FLAG - IF ALREADY TAKEN - CANT RUN ##### */
/* ############################################################ */
function bhcheckAndAdjustFlag($type) {
  $flagfname = ABSPATH.'/_retsapi/pulldates/flagAvail.txt';
  if(file_exists($flagfname)) {
    if ($type = "take"){
      $checkFlag = file_get_contents($flagfname);
      if ($checkFlag == 1) {  // taking the flag - write 0 to the file
        echo '<h1 style="color:white; background-color:green; padding 3px;">Took flag - ok to run!</h1>';
        file_put_contents($flagfname,"0");
        return true;
      } else {
        echo '<h1 style="color:white; background-color:red; padding 3px;">Flag not available - cant run!</h1>';
        return false;
      }

    } else {  // 'giveup' give the flag up
      file_put_contents($flagfname,"1");
      echo '<h1 style="color:white; background-color:green; padding 3px;">Gave flag back</h1>';
      return true;
    }
  } else {  // create flile and write 0 - we are taking it
    echo '<h1 style="color:white; background-color:green; padding 3px;">File doesnt exist, create it and take flag - ok to run!</h1>';
    file_put_contents($flagfname,"0");
    return true;
  }
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
  $log = $rc;

  $fnamerecent = ABSPATH.'/_retsapi/pulldates/'.$rc.'.txt';
  if(file_exists($fnamerecent)) {
    $pulldate = file_get_contents($fnamerecent);
  } else {
    // $pulldate = strtotime('-730 days'); //'-6 hours' '-1 day' '-10 years'
    $pulldate = strtotime("-2 days");
  }
  global $lastDatePulled;
  $lastDatePulled = $pulldate;

  $querydate = date('Y-m-d H:i:s',$pulldate);
  // echo $pulldate;
  /* AND images IS NOT NULL */
  // AND Status = 'Active'

  /*
  $sqlquery = "SELECT * FROM ".$rc." WHERE
              PublishToInternet = 1
              AND lastPullTime >= '".$querydate."'
              LIMIT ".$sset['count']."
              ;";
  */

  // daily cron query


  $sqlquery = "SELECT * FROM ".$rc." WHERE
              PublishToInternet = 1
              AND lastPullTime >= '".$querydate."'
              ;";


  // used for initial pull, nuclear option to get all data
  /*
  $sqlquery = "SELECT * FROM ".$rc." WHERE
              PublishToInternet = 1
              AND Status = 'Active'
              ;";
  */

  echo '<pre>';
  print_r($sqlquery);
  echo '</pre>';

  /* Select queries return a resultset */
  if ($result = $mysqli->query($sqlquery)) {
      echo '<pre>';
      $log .= " -> select returned %d rows: ".$result->num_rows.PHP_EOL;
      echo '</pre>';
      while($row = $result->fetch_assoc()) {
          $data[] = $row;
      }
      // Frees the memory associated with a result
      $result->free();
  }

  $mysqli->close();

  return $data;
}

/* ##################################### */
/* #### Get array of IDs to delete ##### */
/* ##################################### */
function dbDeleteOldIdList() {
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
      echo "connect failed!";
      //printf("Connect failed: %s\n", $mysqli->connect_error);
      exit();
  }

  $querydate = date("Y-m-d H:i:s");
  $querydate = date_create($querydate);
  date_sub($querydate, date_interval_create_from_date_string("365 days"));
  $querydate = date_format($querydate,"Y-m-d H:i:s");

  $sqlquery = "SELECT MLNumber, LastModifiedDateTime, Status, images FROM Property_RESI WHERE
              LastModifiedDateTime <= '".$querydate." '
              AND Status = 'Sold'";

  echo "\n\rquery: ".$sqlquery;
  
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

/* ################################################################################## */
/* #### DELETING PROPERTIES AND PHOTOS FROM RHETS SIDE THAT ARE                 ##### */
/* #### OLDER THAN X - SET IN TMPLATE-DELETE-OLD-PROPERTIES                     ##### */
/* ################################################################################## */
function bhDeleteProperty($propItem){
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );

  $mysqli = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);
  /* check connection */
  if ($mysqli->connect_errno) {
      echo "connect failed!";
      //printf("Connect failed: %s\n", $mysqli->connect_error);
      exit();
  }

  $sqlquery = "DELETE FROM Property_RESI WHERE
              MLNumber = ".$propItem['MLNumber'];

  echo "<p>query: ".$sqlquery."</p>";
  echo "<p>Assoc images: ".$propItem['images'];
  $photoArray = explode("|", $propItem['images']);
  $fileStem = explode("-", $photoArray[0]);
  echo " Delte images starting with: ".$fileStem[0]."<p>";
  $mysqli->close();
  return true;
}

/* ############################ */
/* #### IMAGES PROCESSING ##### */
/* ############################ */
if ( ! function_exists( 'bendhomes_image_upload' ) ) {

 function bendhomes_image_upload($imagebase, $needUpdate) {

   //echo "In image upload - update = ".$needUpdate;
   $imagedir = ABSPATH.'_retsapi/images/property/';
   $ptmp = explode('.',$imagebase);
   $post_name = 'property-'.$ptmp[0];
   $imagepull = $imagedir.$imagebase;
   $tmp = $imagepull;
   $file_array = array(
       'name' => 'property-'.basename( $imagebase ),
       'tmp_name' => $tmp
   );

   /* check to see if image already exists in media library, if it does,
   use it instead or creating a new version, this will prevent duplicates */
   $attachment_id = get_page_by_title( $post_name, 'ARRAY_A', 'attachment' );
   $attachment_fname = basename($attachment_id['guid']);

   if( (!empty($attachment_id['ID'])) && ($attachment_fname == $file_array['name'] ) ) {
     $myid = $attachment_id['ID'];
     // the image already exists, but does it need updating?
     if ($needUpdate == 1) {
      echo '<p style="color: orange;">update pre-existing-id: '.$myid.'</p>';
      delete_updated_images($myid);
      wp_delete_attachment($myid, true);
      $myid = media_handle_sideload( $file_array, array( 'test_form' => false ) );
    }
     // echo '<p style="color: orange;">pre-existing-id: '.$myid.'</p>';
   } else {
     if ( is_wp_error( $tmp ) ) {
         // @unlink( $file_array[ 'tmp_name' ] );
         return $tmp;
         // echo '<p style="background-color: red; color: #fff;">'.$tmp.'</p>';
     }
     $myid = media_handle_sideload( $file_array, array( 'test_form' => false ) );
     // echo '<p style="color: green;">new-id: '.$myid.'</p>';
   }

   /* debugging
   echo '<pre style="background-color: cyan;">';
   echo 'post_name'.$post_name.'<br/>';
   echo 'attachment_id:';
   print_r($attachment_id);
   echo '<br/>';
   echo 'myid: '.$myid;
   echo '<br/>';
   print_r($file_array);
   echo '</pre>';
   */

   return $myid;

 }
 add_filter( 'bendhomes_img_upload', 'bendhomes_image_upload', 10, 2 );
}

function bhImageSet($item) {
  global $lastDatePulled;
  $lastPullAdjusted = $lastDatePulled - 3600;
  $imagesdir['source'] = ABSPATH.'/_retsapi/imagesbackup/property/';
  $imagesdir['tmpdest'] = ABSPATH.'/_retsapi/images/property/';
  $bhimgids = NULL;
  if($item['images'] != '') {
    $tmpimages = explode('|',$item['images']);
    $bhimgids = array(); // predeclare wp images id array for use
    // let's upload our images and get our wp image ids for use later in array
    foreach($tmpimages as $img) {
      $updateFlag = 0;
      // copies image from backup dir, to images dir, file is unlinked/deleted
      // upon processing. This will enable images to update and scripts to be rerun
      if(file_exists($imagesdir['source'].'/'.$img)) {
        // if the file exists already in tmpdest, then check the PictureModifiedDateTim to see if more
        // recent than the last pull time.  We only want to do this if photos are updated - not just the
        // listing being modified or updated.
        if(file_exists($imagesdir['tmpdest'].'/'.$img)) {
          echo "picMod: ".$item['PictureModifiedDateTime']."\n\r";
          $modDay = strtotime($item['PictureModifiedDateTime']);
          //echo "last pulled: ".$lastDatePulled." last mod: ".$modDay;
          bh_write_to_log('PicMod: '.$modDay.'  lastPull: '.$lastPullAdjusted ,'properties');
          if ($modDay >= $lastPullAdjusted) {
            bh_write_to_log('Updating photos here... ','properties');
            copy($imagesdir['source'].$img,$imagesdir['tmpdest'].$img);
            $updateFlag = 1; // lets bendhomes_img_upload know it needs updateing.
          }
        } else {  // file didn't exist in tmpdest so put it there
          copy($imagesdir['source'].$img,$imagesdir['tmpdest'].$img);
        }

        $tf = apply_filters( 'bendhomes_img_upload', $img, $updateFlag );
        $bhimgids[] = $tf;
      }
    }
    unset($tmpimages,$tf); // we only need $tmpimages & $tf for this loop
  }
  return($bhimgids);
}

function bh_write_to_log($string,$type) {
  // path from root of server to write logs
  $path = '/var/www/logs/';
  // $path = $_SERVER['DOCUMENT_ROOT'].'/_logs/';
  $fdate = date("j.n.Y");
  $fname = $path.$type.'_'.$fdate.'.txt';
  $logdate = date("F j, Y, g:i a");
  $log  = $string.PHP_EOL;
  //Save string to log, use FILE_APPEND to append.
  file_put_contents($fname, $log, FILE_APPEND);
}

function delete_updated_images($post_id) {
  // presets
  global $wpdb;
  $imgdir = ABSPATH.'wp-content/uploads/';
  // $logpath = $_SERVER['DOCUMENT_ROOT'].'/_logs/';
  $logpath = '/var/www/logs/';
  $logfile = $logpath.'deleted_images_'.date('Y-m-d').'.txt';
  $imagecounter = 0;
  $delpostcount = 0;
  echo '<p style="color: green;">'.$post_id.'</p>';
  if($post_id != NULL) {
    $sqlquery = "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = ".$post_id;
    // echo $sqlquery;
    // echo '<br/>';
    $imgpostmetas = $wpdb->get_results( $sqlquery, ARRAY_A );
  } else {
    $imgpostmetas = NULL;
  }
  unset($sqlquery);
  // print_r($imgpostmetas);
  echo '<pre style="background-color: #ececec; margin: 10px; border: 1px solid #cc0000; padding: 10px;">';
  // echo "\n".'<strong style="color: #cc0000">imgage post id: '.$imgid.'</strong>'."\n";
  print_r($imgpostmetas);
  echo '</pre>';
  foreach($imgpostmetas as $imgpostmeta) {
    if($imgpostmeta['meta_key'] == '_wp_attached_file' ) {
      $deletefile = $imgdir.$imgpostmeta['meta_value'];
      $froot = explode('.',$deletefile);
      $froot = $froot[0]; // we want of root of the filename with no extension
      echo '<pre style="color: red; border: 2px solid red; padding: 5px;">';
      echo $deletefile;
      if(file_exists($deletefile)) {
        unlink($deletefile);
      }
      echo '<br/>'."\n";
      file_put_contents($logfile, $deletefile . PHP_EOL, FILE_APPEND | LOCK_EX);
      // get all files by pattern and delete them
      foreach( glob($froot.'*') as $file ) {
          $segments = explode('/',$file);
          $onlyfilename = array_pop($segments);
          $dash_count = substr_count($onlyfilename, '-');
          // this deletes all files with the orignal images name pattern, deletes WP versions
          // we check for four dashes, as some other files we getting caught and deleted
          if(file_exists($file)) {
            echo $file;
            echo '<br/>'."\n";
            unlink($file);
            $imagecounter++;
            // file_put_contents($logfile, $file . PHP_EOL, FILE_APPEND | LOCK_EX);
          }
      }
      echo '</pre>';
    }
    delete_post_meta($post_id, $imgpostmeta['meta_key']);
  }
  $delpost = wp_delete_post( $post_id );
  echo '<pre style="color: blue;">';
  echo '<strong>deleted post_id: '.$post_id.'</strong><br/>';
  print_r($delpost);
  echo '</pre>';
  if(!empty($delpost)) {
    $delpostcount++;
  }
  echo '<p style="color: green;">deleted images count: '.$imagecounter.'</p>';
  echo '<p style="color: green;">deleted posts count: '.$delpostcount.'</p>';
}

?>

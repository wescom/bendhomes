<?php

/* ################################# */
/* #### DATA TYPES - SCENARIOS ##### */
/* ################################# */

$centralcount = 100;
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

    // get post ids of all images
    $imageids = get_post_meta( $post_id, 'REAL_HOMES_property_images' );

    if (empty($imageids)) return;

    // query the db and get image path and filename
    foreach ($imageids as $imgid) {
        if($imgid != NULL) {
          $sqlquery = "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = ".$imgid;
          // echo $sqlquery;
          $results = $wpdb->get_results( $sqlquery, ARRAY_A );
        } else {
          $results = NULL;
        }

        foreach($results as $result) {
          if($result['meta_key'] == '_wp_attached_file' ) {
            $deletefile = $imgdir.$result['meta_value'];
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

        // wp_delete_attachment($attachment->ID);
        // unlink(get_attached_file($file->ID));
    }
  }
  add_action('before_delete_post', 'delete_associated_media', 10, 1);
}

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

  // $apiaction = 'delete_property';

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
  $log = $rc;

  $fnamerecent = ABSPATH.'/_retsapi/pulldates/'.$rc.'.txt';
  if(file_exists($fnamerecent)) {
    $pulldate = file_get_contents($fnamerecent);
  } else {
    // $pulldate = strtotime('-730 days'); //'-6 hours' '-1 days'
    $pulldate = strtotime("-10 years");
  }

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
  /* $sqlquery = "SELECT * FROM ".$rc." WHERE
              PublishToInternet = 1
              AND lastPullTime >= '".$querydate."'
              ;"; */

  // used for initial pull, nuclear option to get all data

  $sqlquery = "SELECT * FROM ".$rc." WHERE
              PublishToInternet = 1
              AND Status = 'Active'
              LIMIT ".$sset['count']."
              ;";

  // echo '<pre>';
  // print_r($sqlquery);
  // echo '</pre>';

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

/* ############################ */
/* #### IMAGES PROCESSING ##### */
/* ############################ */
if ( ! function_exists( 'bendhomes_image_upload' ) ) {

 function bendhomes_image_upload($imagebase) {

   $imagedir = ABSPATH.'_retsapi/images/property/';
   $imagepull = $imagedir.$imagebase;
   $tmp = $imagepull;
   $file_array = array(
       'name' => 'property-'.basename( $imagebase ),
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

   // if successfully loaded, unlink the originated image
   // unlink($tmp);
   // echo 'unlink this!: ';
   // print_r($tmp);
   // echo '<br/>';

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
        copy($imagesdir['source'].$img,$imagesdir['tmpdest'].$img);
      }
      $tf = apply_filters( 'bendhomes_img_upload', $img );
      $bhimgids[] = $tf;
    }
    unset($tmpimages,$tf); // we only need $tmpimages & $tf for this loop
  }
  return($bhimgids);
}

function bh_write_to_log($string,$type) {
  // path from root of server to write logs
  // $path = '/var/www/logs/';
  $path = $_SERVER['DOCUMENT_ROOT'].'/_logs/';
  $fdate = date("j.n.Y");
  $fname = $path.$type.'_'.$fdate.'.txt';
  $logdate = date("F j, Y, g:i a");
  $log  = $string.PHP_EOL;
  //Save string to log, use FILE_APPEND to append.
  file_put_contents($fname, $log, FILE_APPEND);
}

?>

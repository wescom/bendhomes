<?php
/*
*  Template Name: Import Open Houses
*/
/*
*  Author: Justin Grady
*/
/*
* Logic of this template is to bring in open houses once a day by doing:
* 1. strip current property entries of open flags and data
* 2. match up ListingRid of open house to current property entry.
* 3. loop through open house times in entry, using the nearest/closest one to today
* 4. pop off first entry on time array and insert into property and flag it as Open
* 5. for output, push out properties similar to 'featured properties' on front page and create a separate map page for properties as well

  sample data per open house
  [AgentFirstName] => Tim
  [AgentLastName] => Buccola
  [AgentHomePhone] => 541-312-6900
  [StartDateTime] => 2016-04-17 00:00:00
  [EndDateTime] => 2016-04-17T0
  [TimeComments] => Noon-3pm
  [ListingRid] => 190744
  [MLNumber] => 201600071
  [post_id] => 57267

*/

ini_set('max_execution_time', 0);
date_default_timezone_set('America/Los_Angeles');

/* #### INCLUDES ##### */
// include_once ABSPATH . 'wp-admin/includes/media.php';
// include_once ABSPATH . 'wp-admin/includes/file.php';
// include_once ABSPATH . 'wp-admin/includes/image.php';

/* ################################# */
/* #### DATA TYPES - SCENARIOS ##### */
/* ################################# */

$centralcount = 999999;
$scenarios = array(
  'OpenHouse_OPEN'=> array(
    'count' => $centralcount,
    'fotos' => 'no',
    'resource' => 'OpenHouse',
    'class' => 'OPEN',
    'name' => 'OpenHouse_OPEN'
  )
);

/* ################################### */
/* #### DATA SETUP and FUNCTIONS ##### */
/* ################################### */
function dbresult($sset) {
  // get open house data from intermediary database bh_rets
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

  // time variable for query
  // we only want 7 days out
  $datenow = time(); // that's now/today dude
  $dateend = strtotime("+7 days", $datenow);
  $querydatestart = date('Y-m-d H:i:s',$datenow);
  $querydateend= date('Y-m-d H:i:s',$dateend);

  $sqlquery = "SELECT AgentFirstName, AgentLastName, AgentHomePhone, StartDateTime, EndDateTime, TimeComments, ListingRid, MLNumber FROM ".$rc." WHERE
  StartDateTime > '".$querydatestart."'
  AND
  EndDateTime < '".$querydateend."'
  ;";

  // print_r($sqlquery);

  /* Select queries return a resultset */
  if ($result = $mysqli->query($sqlquery)) {
      while($row = $result->fetch_assoc()) {
          $data[] = $row;
      }
      // Frees the memory associated with a result
      $result->free();
  } else {
    echo 'no data, malformed query';
  }

  $mysqli->close();
  return $data;

}

function findProperty($mlsid) {
  // lookup property in WP database by mlsid
  if($mlsid != NULL) {
    global $wpdb;
    // ID == , post_title == agent full name
    $sqlquery = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'REAL_HOMES_property_id' AND meta_value = ".$mlsid;
    echo '<hr/>';
    echo $sqlquery;
    $result = $wpdb->get_results( $sqlquery, ARRAY_A );
  } else {
    $result = NULL;
  }

  // testing stuff
  if(empty($result)) {
    $color = 'red';
  } else {
    $color = 'green';
  }
  echo '<pre style="border: 3px solid '.$color.';">';
  print_r($result[0]);
  echo '</pre>';

  // this is the returned post_id by MLNumber, returns a one item array
  // we want a string, so set index to 0
  return $result[0];
}

function appendData($mlsids) {
  // we want the open houses data, with actual post ids from wordpress within array
  $i = 0;
  $entries = array();
  foreach($mlsids as $mlsid) {
    $entries[$i] = $mlsid;
    foreach($mlsid as $key => $val) {
      // print_r($val);
      if($key == 'MLNumber') {
        $entries[$i] = array_merge($entries[$i],findProperty($val));
      }
    }
    $i++;
  }
  return $entries;
}

function delOpensMet() {
  global $wpdb;
  $result = array();
  // delete all OPEN_HOUSE meta data
  $sqlquery = "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%OPEN_HOUSE%' ";
  $result['postmeta'] = $wpdb->get_results( $sqlquery, ARRAY_A );
  unset($sqlquery);
  // delete open-house taxomony from all properties | 79 == open-house
  $sqlquery = "DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id = 79";
  $result['term_relationships'] = $wpdb->get_results( $sqlquery, ARRAY_A );
  return $result;
}

function insertWPdata($openhouses) {
  // setup property statuses
  /*
  for-sale    34
  for-rent    33
  open-house  79
  */
  $statusprop = 'open-house';
  $statusappend = true; // append open-house status onto pre-existing status

  // insert open house meta data into wordpress db
  $pm = array();
  $count = 0;
  foreach($openhouses as $openhouse) {
    $post_id = $openhouse['post_id'];
    if($flag != $post_id) {
      // this is if there is a new post_id, the counter is reset to 0 for use in setting meta keynum in $count var
      $count = 0;
    }
    foreach($openhouse as $key => $val) {
      // insert meta data with this line
      $pm[$post_id]['OPEN_HOUSE_'.$key] = add_post_meta( $post_id, 'OPEN_HOUSE_'.$count.'_'.$key, $val );
      // add post status of 'open' to post_id (property)
      wp_set_object_terms( $post_id, $statusprop, 'property-status',$statusappend );
    }
    $flag = $post_id;
    $count++;
  }
  return $pm;
}

/* ################################### */
/* #### EXECUTE DATA FUNCTIONS ####### */
/* ################################### */

// delete that shit, flush all current open house data, we're about to insert new open house data
delOpensMet();

// sleep so to maker sure all data is purged and db is clean
sleep(5);

// get open house data from intermediary database bh_rets
$my_mlsids = dbresult($scenarios['OpenHouse_OPEN']);

// we want the open houses data, with actual pre-existing wordpress post ids within array
$my_openhouses = appendData($my_mlsids);

// now that we have the bh_rets open house data matched to pre-existing posts using MLSID as token
// insert it as meta data into wpdb
$wpdata = insertWPdata($my_openhouses);

echo '<pre style="background-color: #ccc">';
echo 'my_openhouses';
print_r($my_openhouses);
echo '</pre>';

?>

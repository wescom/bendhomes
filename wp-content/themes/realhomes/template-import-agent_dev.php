<?php
/*
*  Template Name: Import Agents Dev Template
*/
/*
*  Author: Justin Grady
*/
?>
<head>
  <style>
    .add_agent {
      color: green;
    }

    .update_agent {
      color: orange;
    }

    .delete_agent {
      color: red;
    }
  </style>
</head>

<?php
ini_set('max_execution_time', 0);
date_default_timezone_set('America/Los_Angeles');

/* #### INCLUDES ##### */
include_once ABSPATH . 'wp-admin/includes/media.php';
include_once ABSPATH . 'wp-admin/includes/file.php';
include_once ABSPATH . 'wp-admin/includes/image.php';

$scenarios = array(
  'ActiveAgent_MEMB' => array(
    'count' => 1,
    'fotos' => 'yes',
    'resource' => 'ActiveAgent',
    'class' => 'MEMB'
  )
);

// for now, manually set the data to pull
$scenarioset = $scenarios['ActiveAgent_MEMB'];
print_r($scenarioset);


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

/* ############################### */
/* #### POST ACTIONS - CASES ##### */
/* ############################### */
function bhPostActions($status,$id=NULL) {
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

$agentarr = dbresult($scenarioset);
echo '<pre> agent_test_199 -- <br/>';
print_r($agentarr);
echo '</pre>';

/* ############################ */
/* #### IMAGES PROCESSING ##### */
/* ############################ */
if ( ! function_exists( 'bendhomes_image_upload' ) ) {

 function bendhomes_image_upload($imagebase) {

   $imagedir = ABSPATH.'/_retsapi/images/activeagent/';
   $imagepull = $imagedir.$imagebase;
   $tmp = $imagepull;
   $file_array = array(
       'name' => 'agent-'.basename( $imagebase ),
       'tmp_name' => $tmp
   );
   if ( is_wp_error( $tmp ) ) {
       // @unlink( $file_array[ 'tmp_name' ] );
       return $tmp;
       echo '<p style="background-color: red; color: #fff;">'.$tmp.'</p>';
   }

   $uploaded_image = media_handle_sideload( $file_array, array( 'test_form' => false ) );

   // this returns the image id from WP that is used for import
   return $uploaded_image;

 }
 add_filter( 'bendhomes_img_upload', 'bendhomes_image_upload', 10, 1 );
}

function bhImageSet($imagelist) {
  echo 'bhImageSet function!';
  $imagesdir['source'] = ABSPATH.'_retsapi/imagesbackup/activeagent/';
  $imagesdir['tmpdest'] = ABSPATH.'_retsapi/images/activeagent/';
  $bhimgids = NULL;

  // echo '<pre>';
  // echo 'imagelist<br/>';
  // print_r($imagelist);
  // echo '</pre>';

  if($imagelist != '') {
    $tmpimages = explode('|',$imagelist);
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

function getMobilePhone($item) {
  // the db results sloppy for phone numbers, organize it
  // the reason I set a mobile_number key, is if MLS give other numbers later
  $output = array();
  $count = 0;
  foreach($item as $key => $val) {
    if($key == 'ContactAddlPhoneType_1' || $key == 'ContactAddlPhoneType_2' || $key == 'ContactAddlPhoneType_3') {
      if($val == 'Cellular') {
        $output['mobile_number'][$count] = NULL; // preset mobile_number key
        $mobileflag = explode('_',$key);
      }
    }
    if(isset($mobileflag) == TRUE && ($key == 'ContactPhoneAreaCode_'.$mobileflag[1])) {
        $output['mobile_number'][$count] .= $val; // get area code
    }
    if(isset($mobileflag) == TRUE && ($key == 'ContactPhoneNumber_'.$mobileflag[1])) {
        $output['mobile_number'][$count] .= $val; // get main 7 digit number
        $count++;
    }
    // unset($mobileflag);
  }

  // foreach number in array, format it with dashes for standard 10 dig output
  $count = 0;
  if(empty($output['mobile_number'])) {
    return NULL;
  } else {
    foreach($output['mobile_number'] as $pn) {
        if(  preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $pn,  $matches ) )
        {
            $output['mobile_number'][$count] = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
            $count++;
        }
    }
  }
  unset($count);
  // I know this seems whack to only return the one number, but data repeats, but want functionality for multiple numbers later
  return $output['mobile_number'][0];
}

/* #### AGENT DATA LOOP ##### */

$retsagents = array(); // first declaration
foreach($agentarr as $agentitem) {
  // remove excess whitepaces within names as they come from RETS
  $fullname = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $agentitem['FullName']);
  $urlslug = $fullname;
  $urlslug = preg_replace('/[^a-zA-Z0-9\s]/', '', $urlslug);
  $urlslug = str_replace(' ', '-', $urlslug);
  $urlslug = strtolower($urlslug);

  // status use cases
  // DECIDE what to do with pre-existing records
  // update, delete,
  // how about multiple records?
  $guid = 'agent_'.$agentitem['MemberNumber'];
  $agentposts = bhLookupAgent($guid);
  $bhagentid = $agentposts[0];
  $bhagentid = $bhagentid->{ID};
  $postaction = bhPostActions($agentitem['IsActive'],$bhagentid);
  $postcontent = $agentitem['OfficeName'].'<br/> '.$agentitem['StreetAddress'].'<br/> '.$agentitem['StreetCity'].', '.$agentitem['StreetState'].' '.$agentitem['StreetZipCode'];
  $mobilephone = getMobilePhone($agentitem);

  // // end use cases
  // add_agent
  // skip_agent
  // update_agent
  // delete_agent
  if($postaction == 'delete_agent' || $postaction == 'skip_agent') {
    $retsagents[$agentitem['MemberNumber']]['action'] = $postaction;
    $retsagents[$agentitem['MemberNumber']]['property_id'] = $bhpropertyid;
  } elseif ($postaction == 'add_agent' || $postaction == 'update_agent') {
    // $bhimgids = bhImageSet($agentitem);
    $retsagents[$agentitem['MemberNumber']] = array(
      'post_author' => '', // left empty on purpose
      'post_date' => date('Y-m-d H:i:s'),
      'post_date_gmt' => date('Y-m-d H:i:s'),
      'post_content' => $postcontent,
      'post_title' => $fullname, // full name of agent goes here, becomes wp post title
      'post_exerpt' => '',
      'post_status' => 'publish',
      'comment_status' => 'closed',
      'ping_status' => 'closed',
      'post_password' => '',
      'post_name' => $urlslug,
      'post_type' => 'agent',
      'images' => $agentitem['images'], // pipe delimited list of rets api images
      'office_number' => $agentitem['OfficeNumber'], // MLS number of office
      'REAL_HOMES_meta' => array(
        'agent_email' => '',
        'mobile_number' => $mobilephone,
        'office_number' => $agentitem['OfficePhoneComplete'],
        'fax_number' => '',
        'facebook_url' => '',
        'twitter_url' => '',
        'google_plus_url' => '',
        'linked_in_url' => '',
        'banner_title' => '',
        'banner_sub_title' => '',
      ),
      'agent_member_number' => $agentitem['MemberNumber'], // unique identifier of agent in wp
      'agent_guid' => $guid, // this must *never* change as is the unique id per agent
      'agent_id' => $bhagentid, // system assigned unique key, empty if new agent, filled if update agent
      // 'agent_img_id' => $bhimgids[0], // bhimgids comes in as array, get first (and should be only) one
      'action' => $postaction  // 'delete_agent'  give api db status, and pre-existing wp id, if exists
    );
  } // end $postaction ifelse
}

$count = 0;

foreach($retsagents as $myagent) {

  echo '<hr/>';
  echo '<h1 class="'.$myagent['action'].'">'.$count.' - '.$myagent['action'].'</h1>';

  echo '<pre>';
  echo 'my agent dev: <br/>';
  print_r($myagent);
  echo '<pre>';

  $invalid_nonce = false;
  $submitted_successfully = false;
  $updated_successfully = false;

  /* Check if action field is set  */
  if( isset( $myagent['action'] ) ) {

      if( $myagent['action'] != 'skip_agent' ) {

        $new_agent = array();

        $new_agent['post_type'] = $myagent['post_type'];

        // Title (Agent's full name)
        $new_agent['post_title']	= sanitize_text_field( $myagent['post_title'] );

        // Description
        $new_agent['post_content'] = wp_kses_post( $myagent['post_content'] );

        // Publish status
        $new_agent['post_status'] = $myagent['post_status'];

        // Agent MLS member number, as guid
        $new_agent['guid'] = $myagent['agent_guid'];

        // get thumbnail/image id, conditional set
        // if( isset ( $myagent['agent_img_id'] ) && ! empty ( $myagent['agent_img_id'] ) ) {
        //   $new_agent['agent_img_id'] = $myagent['agent_img_id'];
        // }

        // Author
        global $current_user;
        get_currentuserinfo();
        $new_agent['post_author'] = $current_user->ID;
        /* check the type of action */
        $action = $myagent['action'];

        if( $action == "add_agent" ){
          $bhimgids = bhImageSet($myagent['images']);
          $myagent['agent_img_id'] = $bhimgids[0];
          unset($bhimgids);
          $agent_id = wp_insert_post( $new_agent ); // Insert Agent and get post ID
          if( $agent_id > 0 ){
              $submitted_successfully = true;
              do_action( 'wp_insert_post', 'wp_insert_post' ); // Post the Post
              if( !empty ( $myagent['agent_img_id'] )) {
                set_post_thumbnail( $agent_id, $myagent['agent_img_id'] );
              }
          }
        } else if( $action == "update_agent" ) {
            $new_agent['ID'] = intval( $myagent['agent_id'] );
            // get post pre-existing thumbnail img id, replaced is from loop above
            $myagent['agent_img_id'] = get_post_thumbnail_id( $new_agent['ID'] );
            // post has not current thumbnail image, and a new one comes in on update, set it
            if( empty ( $myagent['agent_img_id'] )) {
              $bhimgids = bhImageSet($myagent['images']);
              $myagent['agent_img_id'] = $bhimgids[0];
              echo '<pre style="background-color: blue; color: #fff;">';
              echo 'new agent image: <br/>';
              print_r($myagent['agent_img_id']);
              echo '</pre>';
            } else {
              echo '<pre style="background-color: green; color: #fff;">';
              echo 'agent already has image: <br/>';
              print_r($myagent['agent_img_id']);
              echo '</pre>';
            }
            $agent_id = wp_update_post( $new_agent ); // Update Agent and get post ID
            if( $agent_id > 0 ){
                // set_post_thumbnail( $agent_id, 17355 );
                if( !empty ( $myagent['agent_img_id'] )) {
                  // echo '<p style="background-color:brown; color: #fff;">agentid: '.$agent_id.' | imgid:  '.$myagent['agent_img_id'].' | set new agent post thumbnail</p>';
                  update_post_meta( $agent_id, '_thumbnail_id', $myagent['agent_img_id'] );
                }
                $updated_successfully = true;
            }
        } else if( $action == "delete_agent" ) {
            $del_agent['ID'] = intval( $myagent['agent_id'] );
            $agent_id = wp_delete_post( $del_property['ID'] ); // Delete Agent with supplied Agent ID
            $agent_id = 0;
        }

        if($agent_id > 0) {

          if( (isset( $myagent['REAL_HOMES_meta'])) && (!empty($myagent['REAL_HOMES_meta'])) ) {
            foreach($myagent['REAL_HOMES_meta'] as $metaitemkey => $metaitemvalue ) {
              // Attach Bedrooms Post Meta
              if( (isset( $metaitemkey)) && (!empty($metaitemkey)) ) {
                  update_post_meta( $agent_id, 'REAL_HOMES_'.$metaitemkey, $metaitemvalue );
              }
            }
          }

          // if property is being updated, clean up the old meta information related to images
          /*
          if( $action == "update_agent" ){
              delete_post_meta( $agent_id, 'REAL_HOMES_property_images' );
              delete_post_meta( $agent_id, '_thumbnail_id' );
          }
          */

        }
      }
  }
  $count++;
}

// get_header();
// get_footer();
?>

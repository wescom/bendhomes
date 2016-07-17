<?php
/*
*  Template Name: Delete duplicate property images
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
include_once WP_PLUGIN_DIR . '/'.'bh-importer/functions.php';

echo '<h1 style="color: brown;">start time: '.date(DATE_RSS).'</h1>';

// query of duplicate image
/* $dupimgquery = "SELECT ID,post_title,post_name FROM $wpdb->posts wp LEFT JOIN $wpdb->postmeta pm ON wp.ID <> pm.post_id WHERE wp.post_type = 'attachment' and wp.post_title LIKE 'property%-%-%-%' AND pm.meta_id = 1 LIMIT 56000, 3"; */

/* $dupimgquery = "SELECT ID,post_title,post_name FROM $wpdb->posts wp LEFT JOIN $wpdb->postmeta pm ON wp.ID <> pm.meta_value WHERE pm.meta_key = 'REAL_HOMES_property_images' AND wp.post_type = 'attachment' AND wp.post_title LIKE 'property%-%-%-%' LIMIT 10"; */

$dupimgquery = "SELECT DISTINCT(ID) FROM $wpdb->posts wp LEFT JOIN $wpdb->postmeta pm ON wp.ID <> pm.meta_value WHERE pm.meta_key = 'REAL_HOMES_property_images' AND wp.post_type = 'attachment' AND wp.post_title LIKE 'property%-%-%-%' LIMIT 50";

print_r($dupimgquery);
echo '<hr/>';

// get results from duplicate images query
$dupimgresults = $wpdb->get_results( $dupimgquery, ARRAY_A );

function processImages($imagenames) {
  echo '<ol>';
  foreach($imagenames as $imagename) {
    echo '<li>';
    echo 'post id: '.$imagename['ID'];
    echo '<br/><br/>';
    echo '</li>';
    delete_duplicate_images($imagename['ID']);
  }
  echo '</ol>';
}

processImages($dupimgresults);

function delete_duplicate_images($post_id) {
  // presets
  global $wpdb;
  $imgdir = ABSPATH.'wp-content/uploads/';

  $logpath = '/Users/justingrady/web_dev/bendhomes3/_logs/';
  // $logpath = '/var/www/logs/';
  $logfile = $logpath.'deleted_images_'.date(DATE_RSS).'.txt';

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
      echo '<pre style="color: orange;">';
      print_r($froot);
      echo '</pre>';

      foreach( glob($froot.'*') as $file ) {
          // this deletes all files with the orignal images name pattern, deletes WP versions
          if(file_exists($file)) {
            echo $file;
            echo '<br/>'."\n";
            unlink($file);
            $imagecounter++;
            // file_put_contents($logfile, $file . PHP_EOL, FILE_APPEND | LOCK_EX);
          }
      }

    }

    delete_post_meta($post_id, $imgpostmeta['meta_key']);
  }

  $delpost = wp_delete_post( $post_id );
  echo '<pre style="color: blue;">';
  echo '<strong>deleted post_id: '.$post_id.'</strong><br/>';
  print_r($delpost);
  echo '</pre>';
  if($delpost > 0) {
    $delpostcount++;
  }

  // file_put_contents($logfile, '-- deleted images count: '.$imagecounter . PHP_EOL, FILE_APPEND | LOCK_EX);
  // file_put_contents($logfile, '-- deleted posts count : '.$delpostcount . PHP_EOL, FILE_APPEND | LOCK_EX);
  echo '<p style="color: green;">deleted images count: '.$imagecounter.'</p>';
  echo '<p style="color: green;">deleted posts count: '.$delpostcount.'</p>';

}

echo '<h1 style="color: brown;">end time: '.date(DATE_RSS).'</h1>';

?>

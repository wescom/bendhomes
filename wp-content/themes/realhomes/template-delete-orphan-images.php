<?php
/*
*  Template Name: Delete orphan property images
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


if ( ! function_exists( 'delete_orphan_images' ) ) {
  function delete_orphan_images($post_id) {
    // presets
    global $wpdb;
    $imgdir = ABSPATH.'wp-content/uploads/';

    // orphaned images query
    $sqlquery = "SELECT pm.meta_value
      FROM $wpdb->postmeta pm
      LEFT JOIN wp_posts wp ON wp.ID = pm.post_id
      WHERE wp.ID IS NULL
      AND pm.meta_key = 'REAL_HOMES_property_images'
      LIMIT 20
    ";

    echo '<pre style="background-color: yellow;">';
    print_r($sqlquery);
    echo '</pre>';

    // put results into an array
    $results = $wpdb->get_results( $sqlquery, ARRAY_A );
    unset($sqlquery);

    $logfile = '/var/www/logs/deleted_images_'.date(DATERSS).'.txt';
    $imagecounter = 0;
    $delpostcount = 0;

    foreach($results as $result) {
      if(strpos($result['meta_value'],'WP_Error')) {
        // if wp_error, throw it out, continue onto next in loop, no actions
        continue;
      } else {
        $imgid = $result['meta_value'];
      }
      // $imgid = $result['meta_value'];
      // print_r($imgid);
      // echo "\n";

      if($imgid != NULL) {
        $sqlquery = "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = ".$imgid;
        echo $sqlquery;
        $imgpostmetas = $wpdb->get_results( $sqlquery, ARRAY_A );
      } else {
        $imgpostmetas = NULL;
      }
      unset($sqlquery);
      // print_r($imgpostmetas);

      echo '<pre style="background-color: #ececec; margin: 10px; borderL 1px solid #cc0000; padding: 10px;">';
      echo "\n".'<strong style="color: #cc0000">imgage post id: '.$imgid.'</strong>'."\n";

      foreach($imgpostmetas as $imgpostmeta) {
        if($imgpostmeta['meta_key'] == '_wp_attached_file' ) {
          $deletefile = $imgdir.$imgpostmeta['meta_value'];
          $froot = explode('.',$deletefile);
          $froot = $froot[0]; // we want of root of the filename with no extension
          foreach( glob($froot.'*') as $file )
          {
              // this deletes all files with the orignal images name pattern, deletes WP versions
              echo $file;
              echo "\n";
              unlink($file);
              $imagecounter++;
              file_put_contents($logfile, $file . PHP_EOL, FILE_APPEND | LOCK_EX);
          }
          delete_post_meta($imgid, $imgpostmeta['meta_key']);
        }
      }
      $wpdb->delete( 'wp_postmeta', array(
        'meta_key' => 'REAL_HOMES_property_images',
        'meta_value' => $imgid
        )
      );
      // delete_post_meta($imgid, '_wp_attached_file');
      // delete_post_meta($imgid, '_wp_attachment_metadata');
      $delpost = wp_delete_post( $imgid );
      echo '<p style="color: blue;">delete status: ';
      print_r($delpost);
      if($delpost > 0) {
        $delpostcount++;
      }
      echo '</p>';
      // print_r($delpost);
      echo '</pre>';
    }

    file_put_contents($logfile, '-- deleted images count: '.$imagecounter . PHP_EOL, FILE_APPEND | LOCK_EX);
    file_put_contents($logfile, '-- deleted posts count : '.$delpostcount . PHP_EOL, FILE_APPEND | LOCK_EX);
    echo '<p style="color: blue;">deleted images count: '.$imagecounter.'</p>';
    echo '<p style="color: blue;">deleted posts count: '.$delpostcount.'</p>';
  }
  add_action('delete_orphans', 'delete_orphan_images', 10, 1);
}

do_action('delete_orphans');

?>

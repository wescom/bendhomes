<?php
/*
*  Template Name: Delete Old Properties Template
*/
/*
*  Author: Janelle Contreras
*/

ini_set('max_execution_time', 0);
date_default_timezone_set('America/Los_Angeles');

/* #### INCLUDES ##### */
include_once ABSPATH . 'wp-admin/includes/media.php';
include_once ABSPATH . 'wp-admin/includes/file.php';
include_once ABSPATH . 'wp-admin/includes/image.php';

echo "hello world";

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

  $querydate = date('Y-m-d H:i:s -1 year');;
  // echo $pulldate;
  /* AND images IS NOT NULL */
  // AND Status = 'Active'

  $sqlquery = "SELECT * FROM Property_RESI WHERE
              PublishToInternet = 1
              AND lastUpdateTime <= '".$querydate."'
              AND Status = 'Sold'";

  echo "\n\rquery: ".$sqlquery."\n\r"
  /*if ($result = $mysqli->query($sqlquery)) {
      // printf("Select returned %d rows.\n", $result->num_rows);
      while($row = $result->fetch_assoc()) {
          $data[] = $row;
      }
      // Frees the memory associated with a result
      $result->free();
  }*/

  $mysqli->close();

?>
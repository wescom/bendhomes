<?php
/*
*  Template Name: Hide Rets Bad Status
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

/* #### INCLUDES ##### */
/*include_once ABSPATH . 'wp-admin/includes/media.php';
include_once ABSPATH . 'wp-admin/includes/file.php';
include_once ABSPATH . 'wp-admin/includes/image.php';
include_once WP_PLUGIN_DIR . '/'.'bh-importer/functions.php';*/

function hide_item($item) {
	$db = array(
	    'host' => 'localhost',
	    'username' => 'bendhomesuser',
	    'password' => '1Tf1tb7BvmWWgjrU',
	    'database' => 'bendhomes_dev'
	  );

       $conn = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);

        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }

        $query = 'select post_id from wp_postmeta where meta_value = "'.$item.'"';
        $result = $conn->query($query);

        $postId = 0;
        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                    $postId = $row['post_id'];
                }
        }

        if ($postId > 0) {

	        echo " - postid: ".$postId;

	        $query = 'update wp_posts set post_status = "private" where ID = '.$postId; // from wp_postmeta where meta_value = "'.$item.'"';
	        echo " - query: ".$query;
	        //$result = $conn->query($query);
	        //echo '- did it.';

	        $conn->close();
	    } else {
	    	echo " - not in wp database.";
	    }

}

$ourFile = ABSPATH.'/_retsapi/IdTextFiles/badIds.txt';
$ourString = file_get_contents($ourFile);

if ($ourString === FALSE) {
	$error = error_get_last();
	echo '<p style="background-color: brown; color: #ffffff; padding: 0.25em;">ERROR '.$error['message'].'</p>';
} else {
	echo '<p style="background-color: brown; color: #ffffff; padding: 0.25em;">'.$ourString.'</p>';
}

$ourArray = explode(",", $ourString);

foreach($ourArray as $item) {
	echo '<p>'.$item;

	//if ($item == 201502782) {
		hide_item($item);
		//break 2;
	//}

	echo '</p>';
}


?>
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

        echo " - postid: ".$postId;

        $query = 'update wp_posts set post_status = "private" where ID = '.$postId; // from wp_postmeta where meta_value = "'.$item.'"';
        echo " - query: ".$query;

}

echo '<p style="background-color: brown; color: #ffffff; padding: 0.25em;">hello world!</p>';

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

	hide_item($item);

	echo '</p>';

	//$mlsposts = bhLookupPostByMLS($item);

	//$bhpropertyid = $mlsposts[0];

	//echo ' - wp id: '.$bhpropertyid.'</p>';

	//$wasSuccess = bhDeleteProperty($propItem, $rc);

	/*if ($bhpropertyid > 0) { 
	    bhDeleteWPImages($bhpropertyid);
	    wp_delete_post($bhpropertyid);
	    	
	} else {
	    echo "<p>Property was not in wordpress database";
	}*/
}



//$daysBack = 365;

//theTm = time();
//bh_write_to_log('Entered template-delete-old-properties.php ','propertiesDeleteEntry'.$theTm);

/*foreach($scenarios as $scenario) {

	$resource = $scenario['resource'];
  	$class = $scenario['class'];
  	$rc = $resource.'_'.$class;  // ie:  Property_RESI

	$propList = dbDeleteOldIdList($scenario, $rc, $daysBack);
	$log = $rc." Found ".count($propList);
	bh_write_to_log($log,'propertiesDeleteEntry'.$theTm);
	echo "<h1 style='color:green'>".$log."</h3>";
	foreach($propList as $propItem) {

	 	$mlsposts = bhLookupPostByMLS($propItem['MLNumber']);
	 	$bhpropertyid = $mlsposts[0];
	 	$log = $propItem['MLNumber']." wpID: ".$bhpropertyid." status: ".$propItem['Status']." lastMod: ".$propItem["LastModifiedDateTime"];
	    echo "<p>mls: ".$log."</p>";

	    $wasSuccess = bhDeleteProperty($propItem, $rc);

	    if ($bhpropertyid > 0) { 
	    	bhDeleteWPImages($bhpropertyid);
	    	wp_delete_post($bhpropertyid);
	    	
	    } else {
	    	echo "<p>Property was not in wordpress database";
	    }
	}
}*/

//bh_write_to_log('Completed template-delete-old-properties.php ','propertiesDeleteEntry'.$theTm);

?>
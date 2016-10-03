<?php
/*
*  Template Name: Delete Old Properties Template
*/
/*
*  Author: Janelle Contreras
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

//$theTm = time();
//bh_write_to_log('Entered template-import-properties.php ','propertiesUpdateEntry'.$theTm."_".$_SERVER['REMOTE_ADDR']);


echo "hello world";

 /*$propList = dbDeleteOldIdList();

foreach($propList as $propItem) {
 	$mlsposts = bhLookupPostByMLS($propItem['MLNumber']);
 	$bhpropertyid = $mlsposts[0];
    echo "<p>mls: ".$propItem['MLNumber']." wpID: ".$bhpropertyid." status: ".$propItem['Status']." lastMod: ".$propItem["LastModifiedDateTime"]."</p>";

    $wasSuccess = bhDeleteProperty($propItem);

    if ($bhpropertyid > 0) { */
    	bhDeleteWPImages(307810)
    	//delete_updated_images(308003);
    	//wp_delete_post(308003);
    	
    /*} else {
    	echo "<p>Property was not in wordpress database";
    }
}*/

?>
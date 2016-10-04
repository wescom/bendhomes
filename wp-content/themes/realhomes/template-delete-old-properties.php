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

$daysBack = 365;

$theTm = time();
bh_write_to_log('Entered template-delete-old-properties.php ','propertiesDeleteEntry'.$theTm);

foreach($scenarios as $scenario) {

	$resource = $scenario['resource'];
  	$class = $scenario['class'];
  	$rc = $resource.'_'.$class;  // ie:  Property_RESI
	echo "<h2>Starting: ".$scenario['resource'].$scenario['class']."</h2>";

	$propList = dbDeleteOldIdList($scenario, $rc, $daysBack);
	//bh_write_to_log($scenario['resource']."_"$scenario['class']." Found: ".count($propList),'propertiesDeleteEntry'.$theTm);
	echo "<h3 style='color:green'>Found: ".count($propList)."</h3>";
	foreach($propList as $propItem) {

	 	$mlsposts = bhLookupPostByMLS($propItem['MLNumber']);
	 	$bhpropertyid = $mlsposts[0];
	    echo "<p>mls: ".$propItem['MLNumber']." wpID: ".$bhpropertyid." status: ".$propItem['Status']." lastMod: ".$propItem["LastModifiedDateTime"]."</p>";

	    $wasSuccess = bhDeleteProperty($propItem, $rc);

	    if ($bhpropertyid > 0) { 
	    	bhDeleteWPImages($bhpropertyid);
	    	wp_delete_post($bhpropertyid);
	    	
	    } else {
	    	echo "<p>Property was not in wordpress database";
	    }
	}
}

bh_write_to_log('Completed template-delete-old-properties.php ','propertiesDeleteEntry'.$theTm);

?>
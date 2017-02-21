<?php

// File loaded from tbb-functions/tbb-functions.php in add_action('wp_footer', 'rets_footer_code') 
// Displays Open House date & times on single property IDX page

/*include_once '/wp-content/plugins/tbb-functions/rets-connect.clsss.php';

$mls = !empty( $_GET["mls"] ) ? $_GET["mls"] : '';

$query = "
	SELECT MLNumber, StartDateTime, TimeComments
	FROM OpenHouse_OPEN 
	WHERE MLNumber = {$mls}
";

$openhouses_query = new Rets_DB();
		
$openhouses = $openhouses_query->select( $query );

print_r($openhouses);*/


include_once '/var/databaseIncludes/retsDBInfo.php';

$mls = !empty( $_GET["mls"] ) ? $_GET["mls"] : '';
//$mls = 201610228;

$conn = new mysqli(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

$query = "
	SELECT MLNumber, StartDateTime, TimeComments
	FROM OpenHouse_OPEN 
	WHERE MLNumber = {$mls}
";

$result = $conn->query($query);

echo $query;

$returnText = "";
if ($result->num_rows > 0) {
	$agFax = "";
	while( $row = $result -> fetch_assoc() ) {
		$rows[] = $row;
	}
}

echo $rows;

mysqli_close($conn);

$returnText = str_replace('"', '\"', $returnText);
$returnText = str_replace('/', '\/', $returnText);
//echo 'openHouseCallBack({"html":"'.$returnText.'"})';
//echo '{"html": "'.$returnText.'"}';
<?php

// File loaded from tbb-functions/tbb-functions.php in add_action('wp_footer', 'rets_footer_code') 
// Displays Open House date & times on single property IDX page

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

$html = "";

// Create array of returned values
if ($result->num_rows > 0) {
	while( $row = $result -> fetch_assoc() ) {
		$rows[] = $row;
	}
	//print_r( $rows );
	
	$html .= '<div id="OpenHouse" class="clearfix"><h3>Open House Times</h3>';

		foreach( $rows as $row ) {

			$date = new DateTime( $row['DateAndTime'] );
			$date_format = $date->format('M jS');
			$time = $row['TimeComments'];

			$html .= sprintf( '<div class="time">%s %s</div>', $date_format, $time );

		}

	$html .= '</div>';
	
} else {
	$html .= '<div></div>';
}

mysqli_close($conn);

$html = str_replace('"', '\"', $html);
$html = str_replace('/', '\/', $html);

echo 'openHouseRender({"html":"'. $html .'"})';
//echo '{"html": "'.$html.'"}';


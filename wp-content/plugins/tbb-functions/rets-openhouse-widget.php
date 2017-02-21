<?php

// File loaded from tbb-functions/tbb-functions.php in add_action('wp_footer', 'rets_footer_code') 
// Displays Open House date & times on single property IDX page

function create_openhouse_widget() {
$openhouses_query = new Rets_DB();
$openhouses = $openhouses_query->select( $query );

print_r($openhouses);
}

create_openhouse_widget();


/*include_once '/var/databaseIncludes/retsDBInfo.php';

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

$html = "";

$result = $conn->query($query);

// Create array of returned values
if ($result->num_rows > 0) {
	
	$html .= '<div id="OpenHouse" class="clearfix"><h3>Open House Times</h3>';
	
		for($i = 0; $i < count($result); ++$i) {
			$date = $result[$i]['DateAndTime'];
			$time = $result[$i]['TimeComments'];
			
			$html .= sprintf( '<div class="time">%s %s</div>', $date, $time );
		}
	
		/*while( $rows = $result->fetch_assoc() ) {
			print_r($rows);
			foreach( $rows AS $v ) {
				//$date = new DateTime( $v['DateAndTime'] );
				//$date_format = $date->format('M jS');
				$date_format = $v['DateAndTime'];
				$time = $v['TimeComments'];
				
				$html .= sprintf( '<div class="time">%s %s</div>', $date_format, $time );
			}
		}*/
	
	/*$html .= '</div>';
	
} else {
	$html .= '<div></div>';
}

mysqli_close($conn);

$html = str_replace('"', '\"', $html);
$html = str_replace('/', '\/', $html);

echo 'openHouseRender({"html":"'. $html .'"})';
//echo '{"html": "'.$html.'"}';*/


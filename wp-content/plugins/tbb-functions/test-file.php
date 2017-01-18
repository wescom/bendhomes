<?php // Functions testing file. Not used for production

// These are our demo API keys, you can use them!
/*$username = "simplyrets";
$password = "simplyrets";
$remote_url = 'https://api.simplyrets.com/properties';

$opts = array(
    'http'=>array(
        'method'=>"GET",
        'header' => "Authorization: Basic " . base64_encode("$username:$password")
    )
);
$context = stream_context_create($opts);
$file = file_get_contents($remote_url, false, $context);
print($file);*/



$db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
);

$idString = "";

$conn = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);

if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
}

$query = "select ListingRid, MLNumber from Property_BUSI";
$result = $conn->query($query);

if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {
				//echo "<pre>id: ".$row['ListingRid']."</pre>";
				$idString .= $row['ListingRid']."-".$row['MLNumber']."<br>";
		}
}

$conn->close();

echo "idString: ".$idString;


/*$bh_rets_db = new wpdb( 'phrets', 'hCqaQvMKW9wJKQwS', 'bh_rets', 'localhost' );

$offices_query = $bh_rets_db->query( 'select ListingRid, MLNumber from Property_RESI' );

$office;

if( $offices_query->num_rows > 0 ) {
	
	while( $office = $offices_query->fetch_assoc() ) {
		$office .= $row['ListingRid']."-".$row['MLNumber']."<br>";
	}
	
}

echo $office;*/
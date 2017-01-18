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


$bh_rets_db = new wpdb( 'phrets', 'hCqaQvMKW9wJKQwS', 'bh_rets', 'localhost' );

$offices_query = $bh_rets_db->query( 'select ListingRid, MLNumber from Property_RESI' );

$office;

if( $offices_query->num_rows > 0 ) {
	
	while( $office = $offices_query->fetch_assoc() ) {
		$office .= $row['ListingRid']."-".$row['MLNumber']."<br>";
	}
	
}

echo $office;
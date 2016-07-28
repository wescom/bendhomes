<?php // Functions testing file. Not used for production

// These are our demo API keys, you can use them!
$username = "simplyrets";
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
print($file);
<?php

// File loaded from tbb-functions/tbb-functions.php in add_action('wp_footer', 'rets_footer_code') 
// Displays Open House date & times on single property IDX page

$mls = !empty( $_GET["mls"] ) ? $_GET["mls"] : '';
//$mls = 201610228;

$home_url = 'http://www.bendhomes.com';

$query = "
	SELECT MLNumber, StartDateTime, TimeComments
	FROM OpenHouse_OPEN 
	WHERE MLNumber = {$mls}
";

$openhouses_query = new Rets_DB();
		
$openhouses = $openhouses_query->select( $query );

print_r( $openhouses );
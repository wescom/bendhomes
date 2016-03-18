<?php

// set your timezone
date_default_timezone_set('America/New_York');

// pull in the packages managed by Composer
require_once("vendor/autoload.php");

// setup your configuration
$config = new \PHRETS\Configuration;
$config->setLoginUrl('http://rets172lax.raprets.com:6103/CentralOregon/COAR/Login.aspx');
$config->setUsername('BBUL');
$config->setPassword('JGrady');

// optional.  value shown below are the defaults used when not overridden
$config->setRetsVersion('1.7.2'); // see constants from \PHRETS\Versions\RETSVersion
// $config->setUserAgent('PHRETS/2.0');
$config->setUserAgent('BBulletin/1.72');
// $config->setUserAgentPassword($rets_user_agent_password); // string password, if given
$config->setHttpAuthenticationMethod('digest'); // or 'basic' if required
$config->setOption('use_post_method', false); // boolean
$config->setOption('disable_follow_location', false); // boolean

// get a session ready using the configuration
$rets = new \PHRETS\Session($config);

// print_r($rets);

// make the first request
$connect = $rets->Login();

echo '<h2>';
var_dump($connect);
echo '</h2>';

echo '<hr/>';

// $system = $rets->GetSystemMetadata();
// var_dump($system);

// $classes = $rets->GetClassesMetadata('Property');
// var_dump($classes);
// var_dump($classes->first());

// $objects = $rets->GetObject('OpenHouse');
// var_dump($objects);


// WORKS
// all residential properties
$resource = 'Property';
$class = 'RESI';
// $query = '(MLNumber=0+), (RESISTYL=|LOG), (LastModifiedDateTime=2016-02-10T00:00:00+)';
$query = '(MLNumber=0+), (LastModifiedDateTime=2016-02-10T00:00:00+)';
/* I can see this picture info, but how do I get the photos themselves? */
/*
["PictureCount"]=> string(2) "25"
["PictureModifiedDateTime"]=>
*/

// WORKS
// open houses
// $resource = 'OpenHouse';
// $class = 'OPEN';
// $query = '(OpenHouseRid=0+)';
// returns MLS number only, would have to do a join with main MLS DOMCharacterData

  // sample data
  /*
  array(16) {
            ["AgentFirstName"]=>
            string(4) "Ryan"
            ["AgentHomePhone"]=>
            string(12) "860-817-7036"
            ["AgentLastName"]=>
            string(3) "Bak"
            ["AgentMLSID"]=>
            string(5) "10460"
            ["EndDateTime"]=>
            string(19) "2016-02-14T00:00:00"
            ["Hosted"]=>
            string(0) ""
            ["ListingAgentNumber"]=>
            string(4) "4271"
            ["ListingOfficeNumber"]=>
            string(3) "299"
            ["ListingRid"]=>
            string(6) "191626"
            ["MLNumber"]=>
            string(9) "201601027"
            ["OfficeMLSID"]=>
            string(4) "FRED"
            ["OfficeName"]=>
            string(22) "Fred Real Estate Group"
            ["OfficePhone"]=>
            string(12) "541-647-6556"
            ["OpenHouseRid"]=>
            string(5) "23338"
            ["StartDateTime"]=>
            string(19) "2016-02-14T00:00:00"
            ["TimeComments"]=>
            string(11) "Noon to 4PM"
          }
  */

// WORKS
// Agents
// $resource = 'ActiveAgent';
// $class = 'MEMB';
// $query = '(IsActive=|1)';

    // sample data
    /*
    array(8) {
         ["FullName"]=>
         string(15) "Shane  Lundgren"
         ["IsActive"]=>
         string(4) "TRUE"
         ["LastModifiedDateTime"]=>
         string(19) "2015-09-08T08:31:07"
         ["MemberNumber"]=>
         string(2) "74"
         ["MLSID"]=>
         string(4) "6221"
         ["OfficeMLSID"]=>
         string(4) "POND"
         ["OfficeName"]=>
         string(20) "Ponderosa Properties"
         ["OfficeNumber"]=>
         string(4) "1095"
       }
       */







// $query = '(MLNumber=0+)';

// RESISTYL=|Ranch

// all residential properties
// $resource = 'OpenHouse';
// $class = 'OPEN';
// $query = '(MLNumber=0+)';
// $query = '(MLNumber=Christie)';

$search = $rets->Search($resource,$class,$query);

/*
$search = $rets->Search(
    $resource,
    $class,
    $query,
    [
        'QueryType' => 'DMQL2',
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => 99999999,
        'StandardNames' => 0, // give system names
    ]
);
*/

/*
if ($rets->NumRows($search) > 0) {
  echo 'count --> '.$rets->NumRows($search);
}
*/

// print_r($search);

// echo 'blar';

$bhresults = $search->results;

print_r($search);
// print_r($bhresults);



//[results:protected] => [items:protected] => Array
// 0,1,2,3 etc


?>

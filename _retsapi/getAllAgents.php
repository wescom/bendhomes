<?php
//include("inc/retsabspath.php");
//include("/var/www/html/_retsapi/inc/getBadIdsHeader.php");
ini_set('max_execution_time', 0);

$centralcount = 999999;
$lastDatePulled = 0;
$idString = "";

$scenarios = array(
  'ActiveAgent_MEMB' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'ActiveAgent',
    'class' => 'MEMB'
  )
);

$universalkeys = array(
  'ActiveAgent' => array(
    'MEMB' => 'MemberNumber'
    // 'MEMB' => '(IsActive=1)'
  ),
  'Agent' => array(
    'MEMB' => 'MemberNumber'
    // 'MEMB' => '(IsActive=1)'
  ),
  'MemberAssociation' => array(
    'ASSC' => 'MemberNumber'
  ),
  'Office' => array(
    'OFFI' => 'OfficeNumber'
  ),
  'OfficeAssociation' => array(
    'ASSC' => 'OfficeAssociationKey'
  ),
);

/* ##### ######### ##### */
/* ##### FUNCTIONS ##### */
/* ##### ######### ##### */

/* ##### Build RETS db query ##### */
function buildRetsQuery($fqvars) {
  global $lastDatePulled;
  $resource = $fqvars['resource'];
  $class = $fqvars['class'];

  // we do this date store and pull for the query.
  // It looks up the last time it was run, and queries from last modified forward
  // we store it as simple .txt file based on the query in use
  // $fnamerecent = '/Users/justingrady/web_dev/phpretstest/pulldates/'.$resource.'_'.$class.'.txt';
  //$fnamerecent = RETSABSPATH.'/pulldates/'.$resource.'_'.$class.'.txt';

  //$pulldate = array();
  //$pulldate['now'] = (int) time();

  //if(file_exists($fnamerecent)) {
 //   $pulldate['recent'] = file_get_contents($fnamerecent);
 //   $pulldate['recent'] = (int) $pulldate['recent'];
 // } else {
  //  $pulldate['recent'] = strtotime("-1 day"); // 1 day, 2 days, 1 year, 2 years, 1 week, 2 weeks, etc
  //}
  //$lastDatePulled = $pulldate['recent'];
  $pulldate['retsquery'] = "2001-06-01T00:00:00-08:00"; //date('c',$pulldate['recent']);
  $funiversalqueries = universalqueries($pulldate['retsquery']);

  // $pulldate['recent'] = file_get_contents('/Users/justingrady/web_dev/phpretstest/pulldates/'.$resource.'_'.$class.'.txt');

  echo '<p style="background-color: orange;">using date: '.$pulldate['retsquery'].'</p>';
//  file_put_contents($fnamerecent,$pulldate['now']);

  // first part, resource and class uses the minimum unique key for query, then last modified
  // $usethisquery = ''.$funiversalqueries[$resource][$class].', (LastModifiedDateTime='.$pulldate['retsquery'].'+)';
  $usethisquery = ''.$funiversalqueries[$resource][$class].'';
  return $usethisquery;
}

/* ##### Refactor returned data with key supplied by universalkeys in header file ##### */
function refactorarr($itemsarray,$ukeys,$qvars) {
  $newarray = array();
  foreach ($itemsarray as $prop) {
    foreach($prop as $key => $val) {
      if($key == $ukeys[$qvars['resource']][$qvars['class']]) {
        $newarray[$val] = $prop;
      }
    }
  }
  return $newarray;
}


/* ##### ######### ##### */
/* ##### RETS QUERY #### */
/* ##### ######### ##### */

/* ##### ######### ##### */
/* ##### RETS QUERY #### */
/* ##### ######### ##### */

function universalqueries($pulltime) {

  $manual_mls = (isset($_GET['mls']) ? $_GET['mls'] : NULL);

  $universalqueries = array(
  
    'ActiveAgent' => array(
      'MEMB' => '(MemberNumber=0+), (PictureModifiedDateTime='.$pulltime.'+)',
      // 'MEMB' => '(IsActive=1)'
    ),
    'Agent' => array(
      'MEMB' => '(MemberNumber=0+), (PictureModifiedDateTime='.$pulltime.'+)',
      // 'MEMB' => '(IsActive=1)'
    ),
    'MemberAssociation' => array(
      'ASSC' => '(MemberNumber=0+), (PictureModifiedDateTime='.$pulltime.'+)',
    ),
    'Office' => array(
      'OFFI' => '(OfficeNumber=0+), (PictureModifiedDateTime='.$pulltime.'+)',
    ),
    'OfficeAssociation' => array(
      'ASSC' => '(OfficeAssociationKey=0+), (PictureModifiedDateTime='.$pulltime.'+)',
    ),
  );

  return $universalqueries;
}


function runRetsQuery($qvars) {
  global $universalkeys;
  global $rets;
  global $lastDatePulled;

  $query = buildRetsQuery($qvars);

  print_r($query);

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

  // make the first request
  $connect = $rets->Login();

  $results = $rets->Search(
      $qvars['resource'],
      $qvars['class'],
      $query,
      [
          'QueryType' => 'DMQL2', // it's always use DMQL2
          'Count' => 1, // count and records
          'Format' => 'COMPACT',
          'Limit' => $qvars['count'],
          'StandardNames' => 0, // give system names
          'Select' => 'MemberNumber',
      ]
  );

     //$results = $rets->Search(Class=RESI&SearchType=Property&Count=1&Limit=NONE&QueryType=DMQL2&Format=COMPACT&Select= ListingRid,LastModifiedDateTime,PictureModifiedDateTime,PictureCount&Query=(ListingRid=1+),(LastModifiedDateTime=2017-01-01T00:00:00-08:00+));

   echo '<pre>';
//   print_r($results);
   echo '</pre>';

  // convert from objects to array, easier to process
  $temparr = $results->toArray();
  // refactor arr with keys supplied by universalkeys in header
  $itemsarr = refactorarr($temparr, $universalkeys, $qvars);

  global $idString;
  foreach ($itemsarr as $prop) {

   //   $puid = $universalkeys[$qvars['resource']][$qvars['class']];
   //   $dt2 = date('Y-m-d H:i:s');
      $idString.= $prop['ListingRid'].",";
     // echo '<pre style="background-color: green; color: #fff;">id: '.$prop['ListingRid'].'</pre>';;
  }
   echo '<pre style="background-color: brown; color: #fff;">count: '.sizeof($itemsarr).'</pre>';

  return $itemsarr;
}

/* ##### ######### ####### */
/* ##### GET ALL DATA #### */
/* ##### ######### ####### */

echo '<h1 style="border: 3px solid orange; padding: 3px;">start - '.date(DATE_RSS).' - v2100</h1>';

foreach($scenarios as $qvars) {
  // 1. Get RETS data
  $rets_data = runRetsQuery($qvars);
   echo '<pre>';
   print_r($rets_data);
   echo '</pre>';

  /*
  echo '<pre style="background-color: brown; color: #fff;">';
  echo $rets_data['ListingAgentFullName'].'<br/>';
  echo $rets_data['ListingAgentMLSID'].'<br/>';
  echo $rets_data['ListingAgentNumber'].'<br/>';
  echo '</pre>';
  */

}

//echo 'idString = '.$idString;
//$file = './IdTextFiles/retsIds.txt';
//file_put_contents($file, $idString);
echo '<h1 style="border: 3px solid orange; color: green; padding: 3px;">completed - '.date(DATE_RSS).'</h1>';

?>
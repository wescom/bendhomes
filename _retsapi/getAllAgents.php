<?php
//include("inc/retsabspath.php");
include("/var/www/html/_retsapi/inc/header.php");
ini_set('max_execution_time', 0);

$centralcount = 999999;

$scenarios = array(
  'ActiveAgent_MEMB' => array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'ActiveAgent',
    'class' => 'MEMB'
  ),
  'Agent_MEMB'=> array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Agent',
    'class' => 'MEMB'
  ),
  'Office_OFFI'=> array(
    'count' => $centralcount,
    'fotos' => 'no',
    'resource' => 'Office',
    'class' => 'OFFI'
  )
);

/* ##### ######### ##### */
/* ##### FUNCTIONS ##### */
/* ##### ######### ##### */

/* ##### Build RETS db query ##### */
function buildRetsQuery($fqvars, $pullDate) {
  
  $resource = $fqvars['resource'];
  $class = $fqvars['class'];

  $funiversalqueries = universalqueries($pullDate);

  // first part, resource and class uses the minimum unique key for query, then last modified
  // $usethisquery = ''.$funiversalqueries[$resource][$class].', (LastModifiedDateTime='.$pulldate['retsquery'].'+)';
  $usethisquery = ''.$funiversalqueries[$resource][$class].'';
  return $usethisquery;
}

function getSetPullDate() {


  /*$fnamerecent = RETSABSPATH.'/pulldates/'.$resource.'_'.$class.'.txt';

  $pulldate = array();
  $pulldate['now'] = (int) time();

  if(file_exists($fnamerecent)) {
    $pulldate['recent'] = file_get_contents($fnamerecent);
    $pulldate['recent'] = (int) $pulldate['recent'];
  } else {
    $pulldate['recent'] = strtotime("-1 day"); // 1 day, 2 days, 1 year, 2 years, 1 week, 2 weeks, etc
  }*/

  $pulldate['retsquery'] = "2017-01-10T00:00:00-08:00"; //date('c',$pulldate['recent']);
  echo '<p style="background-color: orange;">using date: '.$pulldate['retsquery'].'</p>';
  return $pulldate['retsquery'];
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

function runRetsQuery($qvars, $datePulled) {
  global $universalkeys;
  global $rets;

  $query = buildRetsQuery($qvars, $datePulled);

  print_r($query);

  if ($qvars['class'] == 'OFFI') {
      $dataType = 'OfficeNumber';
  } else {
      $dataType = 'MemberNumber';
  }

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
          'Select' => $dataType,
      ]
  );

   echo '<pre>';
   //print_r($results);
   echo '</pre>';

  // convert from objects to array, easier to process
  $temparr = $results->toArray();
  // refactor arr with keys supplied by universalkeys in header
  $itemsarr = refactorarr($temparr, $universalkeys, $qvars);

  $idString = "";
  $isFirst = 1;
  foreach ($itemsarr as $prop) {
      if($isFirst == 1) {
        $idString.= $prop[$dataType];
        $isFirst = 0;
      } else {
        $idString.= $prop[$dataType].",";
      }
  }
  echo '<pre style="background-color: brown; color: #fff;">count: '.sizeof($itemsarr).' - '.$idString.'</pre>';
  return $idString;
}

function getAllAgentData($qvars, $pullDate, $idList) {
  global $universalkeys;
  global $rets;

  if ($qvars['class'] == 'OFFI') {
      $dataType = 'OfficeNumber';
  } else {
      $dataType = 'MemberNumber';
  }

  $query = "(".$dataType."=".$idList.")";

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
      ]
  );

  echo '<pre>';
  print_r($results);
  echo '</pre>';

  // convert from objects to array, easier to process
  $temparr = $results->toArray();
  // refactor arr with keys supplied by universalkeys in header
  $itemsarr = refactorarr($temparr, $universalkeys, $qvars);
  return $itemsarr;
}

function processData($qvars, $itemsarr) {
  foreach($itemsarr as $prop) {
    $puid = $universalkeys[$qvars['resource']][$qvars['class']];
    $photos = $rets->GetObject($qvars['resource'], 'Photo', $prop[$puid],'*', 0);
    if ($qvars['fotos'] == 'yes') {
      unset($photos);
      foreach($photos as $photo) {
        if ($photo->getObjectId() != '*') {
          if ($qvars['class'] == 'OFFI') {
            $dataType = 'OfficeNumber';
          } else {
            $dataType = 'MemberNumber';
          }
          $photoName = RETSABSPATH.'/imagesAgents/'.$prop[$dataType].'_'.$photo->getObjectId().'.jpg';
          $photobinary = $photo->getContent();
          file_put_contents($fnamebackup, $photobinary, LOCK_EX);
        }
      }
    }
  }
}

/* ##### ######### ####### */
/* ##### GET ALL DATA #### */
/* ##### ######### ####### */

echo '<h1 style="border: 3px solid orange; padding: 3px;">start - '.date(DATE_RSS).' - v2100</h1>';

$pullDate = getSetPullDate();

foreach($scenarios as $qvars) {
  // 1. Get ids of Agents that have updated since last pull date
   $idList = runRetsQuery($qvars, $pullDate);
   echo '<pre>';
   //print_r($rets_data);
   echo '</pre>';
   $all_agent_data = getAllAgentData($qvars, $pullDate, $idList);
   echo '<pre>';
   print_r($all_agent_data);
   echo '</pre>';
   //processData($qvars, $rets_data);

}

echo '<h1 style="border: 3px solid orange; color: green; padding: 3px;">completed - '.date(DATE_RSS).'</h1>';

?>
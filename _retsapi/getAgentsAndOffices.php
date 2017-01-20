<?php
//include("inc/retsabspath.php");
include("/var/www/html/_retsapi/inc/header.php");
include("/var/www/html/_retsapi/AgentOfficesFunctions.php");
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

function getSetPullDate() {

  //$file = RETSABSPATH.'/newPullDates/AgentsOffices.txt';

  $pulldate = array();
  $pulldate['now'] = (int) time();

  //if(file_exists($fnamerecent)) {
  //  $pulldate['recent'] = file_get_contents($fnamerecent);
  //  $pulldate['recent'] = (int) $pulldate['recent'];
  //} else {
  $pulldate['recent'] = strtotime("-2 hour"); // 1 day, 2 days, 1 year, 2 years, 1 week, 2 weeks, etc
  //}

  $pulldate['retsquery'] = date('c',$pulldate['recent']);
  //file_put_contents($file, "Last Pulled: ".$pulldate['retsquery']);
  
  return $pulldate['retsquery'];
}

/* ##### ######### ####### */
/* ##### GET ALL DATA #### */
/* ##### ######### ####### */

echo '<h1 style="border: 3px solid orange; padding: 3px;">start - '.date(DATE_RSS).' - v2100</h1>';

$updateByIdListFile = false;  // only used manually to pull all data in from text file of ids

if ($updateByIdListFile == true) {
  $pullDate = '2001-01-01T00:00:00-08:00'; //set this to however far back you want to pull from
} else {
  $pullDate = getSetPullDate();
}
echo '<p style="background-color: orange;">using date: '.$pullDate.'</p>';

foreach($scenarios as $qvars) {

  if ($updateByIdListFile == true) {
    // Comment out part 2 first and run to get ids, then uncomment part 2 and comment part 1 out and run.
    // **********  1. This is first step - get all the ids for the time range you are doing *************
    $idList = runRetsQuery($qvars, $pullDate);
    echo '<pre>';
    print_r($idList);
    echo '</pre>';
    $file = '/var/www/html/_retsapi/IdTextFiles/'.$qvars['resource'].'.txt';
    file_put_contents($file, $idList);
    // ***********  End part 1 ***********
    // *********** 2. this is second step, use the ids you got previous and chunk them up in reasonable imports ************
    /*$start = 0; // start index
    $count = 200; // how many past start to grab
    $idFile = "./IdTextFiles/".$qvars['resource'].'.txt';;
    $idString = file_get_contents($idFile);
    echo '<pre> file ids: '.$idString.'</pre>';
    $idArray = explode(",", $idString);
    if (sizeof($idArray) > $start) {
      $pieceArray = array_slice($idArray, $start, $count);
      $pieceString = implode(",", $pieceArray);
      echo '<pre> piece ids: '.$pieceString.'</pre>';
      $all_agent_data = getAllAgentData($qvars, $pullDate, $pieceString);
      echo '<pre>';
      print_r($all_agent_data);
      echo '</pre>';
      $all_agent_data_wPhotos = getPhotos($qvars, $all_agent_data, $pullDate);
      saveToDB($all_agent_data_wPhotos, $qvars, $pullDate);
    } else {
      echo '<pre style="color:red">At end of array.</pre>';
    }*/
    // ***********  End part 2 ***********

  } else {
  // 1. Get ids of Agents that have updated since last pull date
   $idList = runRetsQuery($qvars, $pullDate);
   echo '<pre>';
   print_r($idList);
   echo '</pre>';
   $file = '/var/www/html/_retsapi/IdTextFiles/'.$qvars['resource'].'_time.txt';
   file_put_contents($file, $idList);
   $all_agent_data = getAllAgentData($qvars, $pullDate, $idList);
   echo '<pre>';
   print_r($all_agent_data);
   echo '</pre>';
   $all_agent_data_wPhotos = getPhotos($qvars, $all_agent_data, $pullDate);
   saveToDB($all_agent_data_wPhotos, $qvars, $pullDate);
  }

}

echo '<h1 style="border: 3px solid orange; color: green; padding: 3px;">completed - '.date(DATE_RSS).'</h1>';

?>
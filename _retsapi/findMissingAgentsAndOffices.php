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
  /*'Agent_MEMB'=> array(
    'count' => $centralcount,
    'fotos' => 'yes',
    'resource' => 'Agent',
    'class' => 'MEMB'
  ),*/
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

function findMissingIds($retsIdArray, $ourIdArray) {
  // anything that is in ours but not rets, should be deleted from ours
  $badIds = "";
  $idArray = [];
  foreach($retsIdArray as $item) {
    if (in_array($item, $ourIdArray)){
        //echo "<pre>".$item." - good</pre>";
      } else {
        array_push($idArray, $item);
      }
  }
  echo '<pre style="color: red;">MISSING Ids - count: '.sizeof($idArray).' - '.implode(",",$idArray).'</pre>';
  return $idArray;
}


$pullDate = '2001-01-01T00:00:00-08:00';

foreach($scenarios as $qvars) {

  $retsIdArray = runRetsQuery($qvars, $pullDate);


  $ourIdArray = getOurIds($qvars);


  $missingIdsArray = findMissingIds($retsIdArray, $ourIdArray);
  //$badIdsArray = compareAndGetBads($retsIdArray, $ourIdArray);

  if (sizeof($missingIdsArray) > 0) {

      $missingIdsList = implode(",", $missingIdsArray);
      $all_agent_data = getAllAgentData($qvars, $pullDate, $missingIdsList);
      echo '<pre>';
      print_r($all_agent_data);
      echo '</pre>';
      $all_agent_data_wPhotos = getPhotos($qvars, $all_agent_data, $pullDate);
      saveToDB($all_agent_data_wPhotos, $qvars, $pullDate);

  } else {
    echo '<p>Empty array - no missing agents or offices.</p>';
  }

}

?>
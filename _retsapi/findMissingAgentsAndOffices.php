<?php
//include("inc/retsabspath.php");
include("/var/www/html/_retsapi/inc/header.php");
include("/var/www/html/_retsapi/AgentOfficesFunctions.php");
ini_set('max_execution_time', 0);

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

      $all_agent_data = getAllAgentData($qvars, $pullDate, $missingIdsArray);
      echo '<pre>';
      print_r($all_agent_data);
      echo '</pre>';
      $all_agent_data_wPhotos = getPhotos($qvars, $all_agent_data);
      saveToDB($all_agent_data_wPhotos, $qvars, $pullDate);

  } else {
    echo '<p>Empty array - no missing agents or offices.</p>';
  }

}

?>
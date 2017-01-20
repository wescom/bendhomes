<?php
//include("inc/retsabspath.php");
include("/var/www/html/_retsapi/inc/header.php");
include("/var/www/html/_retsapi/AgentOfficesFunctions.php");
ini_set('max_execution_time', 0);

/* ##### ######### ##### */
/* ##### FUNCTIONS ##### */
/* ##### ######### ##### */

function deletBadIds($badIdsArray, $qvars) {
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );

  if ($qvars['class'] == "OFFI") {
    $idType = "OfficeNumber";
  } else {
    $idType = "MemberNumber";
  }

  $conn = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $query = "DELETE from ".$qvars['resource'].'_'.$qvars['class']." WHERE ".$idType." IN (".implode(", ",$badIdsArray).")";
  echo '<p>'.$query.'</p>';
  
  if($conn->query($query)) {
    echo "<p>Success!!!!</p>";
  } else {
    echo "<p>Error: ".mysqli_error($conn)."</p>";
  }
  mysqli_close($conn);
}


function compareAndGetBads($retsIdArray, $ourIdArray) {
  // anything that is in ours but not rets, should be deleted from ours
  $badIds = "";
  $idArray = [];
  foreach($ourIdArray as $item) {
    if (in_array($item, $retsIdArray)){
        //echo "<pre>".$item." - good</pre>";
      } else {
        array_push($idArray, $item);
      }
  }
  echo '<pre style="color: red;">BAD Ids - count: '.sizeof($idArray).' - '.implode(",",$idArray).'</pre>';
  return $idArray;
}

$pullDate = '2001-01-01T00:00:00-08:00';

foreach($scenarios as $qvars) {

  $retsIdArray = runRetsQuery($qvars, $pullDate);


  $ourIdArray = getOurIds($qvars);


  $badIdsArray = compareAndGetBads($retsIdArray, $ourIdArray);

  if (sizeof($badIdsArray) > 0) {
    deletBadIds($badIdsArray, $qvars);
  } else {
    echo '<p>Empty array - no bad ids to delete...</p>';
  }

}

?>
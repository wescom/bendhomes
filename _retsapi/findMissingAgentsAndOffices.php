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

/* ##### ######### ##### */
/* ##### RETS QUERY #### */
/* ##### ######### ##### */

function runRetsQuery($qvars, $datePulled) {
  global $universalkeys;
  global $rets;

  $query = buildRetsQuery($qvars, $datePulled);

  //print_r($query);

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
          'Format' => 'COMPACT-DECODED',
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

  //$idString = "";
  $idArray = [];
  //$isFirst = 1;
  foreach ($itemsarr as $prop) {
      array_push($idArray, $prop[$dataType]);
      /*if($isFirst == 1) {
        $idString.= $prop[$dataType];
        $isFirst = 0;
      } else {
        $idString.= ",".$prop[$dataType];
      }*/
  }
  echo '<pre style="color: green;">RETS Ids - count: '.sizeof($idArray).' - '.implode(",",$idArray).'</pre>';
  return $idArray;
}

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

function getOurIds($qvars){
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

  $query = "select ".$idType." from ".$qvars['resource'].'_'.$qvars['class'];
  $result = $conn->query($query);

  $idArray = [];
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($idArray, $row[$idType]);
    }
  }
  echo '<pre style="color: blue;">OUR Ids - count: '.sizeof($idArray).' - '.implode(",",$idArray).'</pre>';
  mysqli_close($conn);

  return $idArray;
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


  $missingIdsArray = $findMissingIds($retsIdArray, $ourIdArray);
  //$badIdsArray = compareAndGetBads($retsIdArray, $ourIdArray);

  if (sizeof($missingIdsArray) > 0) {
    //deletBadIds($badIdsArray, $qvars);
  } else {
    echo '<p>Empty array - no bad ids to delete...</p>';
  }

}

?>
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

function getOurIds($qvars){
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );

  if ($qvars['class']= "OFFI") {
    $idType = "OfficeNumber";
  } else {
    $idType = "MemberNumber";
  }

  $conn = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $query = "select ".$idType." from ".$qvars['resource'].'_'.$qvars['class'];
  echo '<p>'.$query.'</p>';
  $result = $conn->query($query);

  $idArray = [];
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($idArray, $row[$idType]);
    }
  }
  echo '<pre style="color: blue;">OUR Ids - count: '.sizeof($idArray).' - '.implode(",",$idArray).'</pre>';
  return $idArray;
}

function compareAndGetBads($retsIdArray, $ourIdArray) {
  // anything that is in ours but not rets, should be deleted from ours
  $badIds = "";
  $isFirst = 1;
  foreach($ourIdArray as $item) {
    if (in_array($item, $retsIdArray)){
        //echo "<pre>".$item." - good</pre>";
      } else {
        if ($isFirst == 1){
          $badIds = $item;
          $isFirst = 0;
        }
        //echo "<pre>".$item."bad - delete from our table </pre>";
      }
  }
}

$pullDate = '2001-01-01T00:00:00-08:00';

foreach($scenarios as $qvars) {
  
  print_r($qvars);
  $retsIdArray = runRetsQuery($qvars, $pullDate);


  $ourIdArray = getOurIds($qvars);
  /*$ourIdArray = explode(",", $ourIddList);
  echo '<pre>';
  print_r($ourIdArray);
  echo '</pre>';

  $badIdsList = compareAndGetBads($retsIdArray, $ourIdArray);
  $badIdArray = explode(",", $badIdsList);
  echo '<pre>';
  print_r($badIdArray);
  echo '</pre>';*/

}

?>
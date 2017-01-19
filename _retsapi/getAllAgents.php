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

  $file = RETSABSPATH.'/newPullDates/AgentsOffices.txt';

  $pulldate = array();
  $pulldate['now'] = (int) time();

  //if(file_exists($fnamerecent)) {
  //  $pulldate['recent'] = file_get_contents($fnamerecent);
  //  $pulldate['recent'] = (int) $pulldate['recent'];
  //} else {
    $pulldate['recent'] = strtotime("-2 hour"); // 1 day, 2 days, 1 year, 2 years, 1 week, 2 weeks, etc
  //}

  $pulldate['retsquery'] = date('c',$pulldate['recent']);
  file_put_contents($file, "Last Pulled: ".$pulldate['retsquery']);
  
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

  $idString = "";
  $isFirst = 1;
  foreach ($itemsarr as $prop) {
      if($isFirst == 1) {
        $idString.= $prop[$dataType];
        $isFirst = 0;
      } else {
        $idString.= ",".$prop[$dataType];
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
          'Format' => 'COMPACT-DECODED',
          'Limit' => $qvars['count'],
          'StandardNames' => 0, // give system names
      ]
  );

  //echo '<pre>';
  //print_r($results);
  //echo '</pre>';

  // convert from objects to array, easier to process
  $temparr = $results->toArray();
  // refactor arr with keys supplied by universalkeys in header
  $itemsarr = refactorarr($temparr, $universalkeys, $qvars);
  return $itemsarr;
}

function saveToDB($itemsarr, $qvars){

  //echo "saving to db";
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );
  $dbConnection = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
  unset($db);
  if($qvars['resource'] == 'Office') {
    $tableItemsArray = ['IDX','IsActive','LastModifiedDateTime','MLSID','OfficeName','OfficeNumber','OfficePhone','OfficePhoneComplete','StreetAddress','StreetCity','StreetState','StreetZipCode','lastPullTime']; 
  } else if ($qvars['resource'] == 'ActiveAgent') {
    $tableItemsArray = ['FullName','IsActive','LastModifiedDateTime','MLSID','OfficeMLSID','OfficeName','OfficeNumber','images','lastPullTime'];
  } else {
    $tableItemsArray = ['ContactAddlPhoneType1','ContactPhoneAreaCode1','ContactPhoneNumber1','ContactAddlPhoneType2','ContactPhoneAreaCode2','ContactPhoneNumber2','ContactAddlPhoneType3','ContactPhoneAreaCode3','ContactPhoneNumber3','FullName','IsActive','LastModifiedDateTime','MemberNumber','MLSID','images','lastPullTime'];
  }

  foreach($itemsarr as $key => $array) {
    $escarray = array_map('mysql_real_escape_string', $array);


    $query = "INSERT INTO ".$qvars['resource']."_".$qvars['class'];
    $query .= " (`".implode("`, `", array_keys($escarray))."`)";
    $query .= " VALUES ('".implode("', '", $escarray)."') ";
    $query .= "ON DUPLICATE KEY UPDATE ";  //MemberNumber = VALUES(".$array['MemberNumber'].")";

    $isFirst = 1;
    foreach($tableItemsArray as $item) {
      if ($isFirst == 1) {
        $query .= $item." = VALUES(".$item.")";
        $isFirst = 0;
      } else {
        $query .= ", ".$item." = VALUES(".$item.")";

      }
    }
    echo '<pre style="color:red">Query: '.$query.'</pre>';
    if (mysqli_query($dbConnection, $query)) {
        echo "<p style='margin: 0; background-color: green; color: #fff;'>Successfully inserted " . mysqli_affected_rows($dbConnection) . " row</p>";
    } else {
        echo "<p style='margin: 0; background-color: red; color: #fff;'>Error occurred: " . mysqli_error($dbConnection) . " row</p>";;
    }
  }
  mysqli_close($dbConnection);
}

function getPhotos($qvars, $itemsarr, $pullDate) {
  global $universalkeys;
  global $rets;
  if ($qvars['class'] == 'OFFI') {
    $dataType = 'OfficeNumber';
  } else {
    $dataType = 'MemberNumber';
  }

  foreach($itemsarr as $prop)  {
    $puid = $universalkeys[$qvars['resource']][$qvars['class']];
    $photoName = "";
    if ($qvars['fotos'] == 'yes') {
      unset($photos);
      $photos = $rets->GetObject($qvars['resource'], 'Photo', $prop[$puid],'*', 0);
      foreach($photos as $photo) {
        if ($photo->getObjectId() != '*') {
          $photoName = $prop[$dataType].'_'.$photo->getObjectId().'.jpg';
          $photoPath = RETSABSPATH.'/imagesAgents/'.$photoName;
          $photobinary = $photo->getContent();
          file_put_contents($photoPath, $photobinary, LOCK_EX);
          echo "<pre style='color:blue'>Saving photo: ".$photoPath."</pre>";

        }
      }
      $itemsarr[$prop[$puid]]['images'] = $photoName;
      $itemsarr[$prop[$puid]]['lastPullTime'] = $pullDate;
    }
    
   
  }
  return $itemsarr;
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
    $file = './IdTextFiles/'.$qvars['resource'].'.txt';
    file_put_contents($file, $idList);
    // ***********  End part 1 ***********
    // *********** 2. this is second step, use the ids you got previous and chunk them up in reasonable imports ************
    /*$start = 2200; // start index
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
   $file = './IdTextFiles/'.$qvars['resource'].'.txt';
   file_put_contents($file, $idList);
   $all_agent_data = getAllAgentData($qvars, $pullDate, $idList);
   echo '<pre>';
   print_r($all_agent_data);
   echo '</pre>';
   //$all_agent_data_wPhotos = getPhotos($qvars, $all_agent_data, $pullDate);
   //saveToDB($all_agent_data_wPhotos, $qvars, $pullDate);
  }

}

echo '<h1 style="border: 3px solid orange; color: green; padding: 3px;">completed - '.date(DATE_RSS).'</h1>';

?>
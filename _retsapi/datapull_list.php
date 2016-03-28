<?php
include("inc/retsabspath.php");
include(RETSABSPATH."/inc/header.php");

ini_set('max_execution_time', 0);

$scenarios = array(
  'ActiveAgent_MEMB' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'ActiveAgent',
    'class' => 'MEMB'
  ),
  /*
  'Agent_MEMB'=> array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Agent',
    'class' => 'MEMB'
  ), */
  /*
  'Office_OFFI'=> array(
    'count' => 999999,
    'fotos' => 'no',
    'resource' => 'Office',
    'class' => 'OFFI'
  ),*/
  /*
  'OpenHouse_OPEN'=> array(
    'count' => 999999,
    'fotos' => 'no',
    'resource' => 'OpenHouse',
    'class' => 'OPEN'
  ),*/

  'Property_BUSI' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'BUSI'
  ),
  'Property_COMM' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'COMM'
  ),
  'Property_FARM' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'FARM'
  ),
  'Property_LAND' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'LAND'
  ),
  'Property_MULT' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'MULT'
  ),
  'Property_RESI' => array(
    'count' => 999999,
    'fotos' => 'yes',
    'resource' => 'Property',
    'class' => 'RESI'
  )

);

/* ##### ######### ##### */
/* ##### FUNCTIONS ##### */
/* ##### ######### ##### */

/* ##### Build RETS db query ##### */
function buildRetsQuery($fqvars,$funiversalqueries) {
  $resource = $fqvars['resource'];
  $class = $fqvars['class'];

  // we do this date store and pull for the query.
  // It looks up the last time it was run, and queries from last modified forward
  // we store it as simple .txt file based on the query in use
  // $fnamerecent = '/Users/justingrady/web_dev/phpretstest/pulldates/'.$resource.'_'.$class.'.txt';
  $fnamerecent = RETSABSPATH.'/pulldates/'.$resource.'_'.$class.'.txt';

  $pulldate = array();
  $pulldate['now'] = (int) time();

  if(file_exists($fnamerecent)) {
    $pulldate['recent'] = file_get_contents($fnamerecent);
    $pulldate['recent'] = (int) $pulldate['recent'];
  } else {
    $pulldate['recent'] = strtotime('-30 days');
  }
  // $pulldate['recent'] = file_get_contents('/Users/justingrady/web_dev/phpretstest/pulldates/'.$resource.'_'.$class.'.txt');
  $pulldate['retsquery'] = date('c',$pulldate['recent']);
  echo '<p style="background-color: orange;">using date: '.$pulldate['retsquery'].'</p>';
  file_put_contents($fnamerecent,$pulldate['now']);

  // first part, resource and class uses the minimum unique key for query, then last modified
  $usethisquery = ''.$funiversalqueries[$resource][$class].', (LastModifiedDateTime='.$pulldate['retsquery'].'+)';
  // $usethisquery = ''.$funiversalqueries[$resource][$class].'';

  // print_r($usethisquery);
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

/* ##### Populate API bh_rets database with data ##### */
function dbpopulate($items,$dbtable) {
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );
  $dbConnection = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
  unset($db);
  $reportout = '<h4>db table: '.$dbtable.'</h4>';
  foreach($items as $key => $array) {

    // escape the array for db username
    $escarray = array_map('mysql_real_escape_string', $array);

    // $query  = "INSERT INTO ".$dbtable;
    $query  = "REPLACE INTO ".$dbtable;
    $query .= " (`".implode("`, `", array_keys($escarray))."`)";
    $query .= " VALUES ('".implode("', '", $escarray)."') ";

    if (mysqli_query($dbConnection, $query)) {
        $reportout .= "<p style='margin: 0; background-color: green; color: #fff;'>Successfully inserted " . mysqli_affected_rows($dbConnection) . " row</p>";
    } else {
        $reportout .= "<p style='margin: 0; background-color: red; color: #fff;'>Error occurred: " . mysqli_error($dbConnection) . " row</p>";;
    }
  }
  return $reportout;
}

/* ##### ######### ##### */
/* ##### RETS QUERY #### */
/* ##### ######### ##### */

function runRetsQuery($qvars) {
  global $universalqueries;
  global $universalkeys;
  global $rets;

  $query = buildRetsQuery($qvars,$universalqueries);
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

  // convert from objects to array, easier to process
  $temparr = $results->toArray();
  // refactor arr with keys supplied by universalkeys in header
  $itemsarr = refactorarr($temparr, $universalkeys, $qvars);

  /* ##### ######### ##### ########################### */
  /* ##### DATA PROCESSING FOR DB POPULATION ### ##### */
  /* ##### ######### ##### ########################### */
  $i = 0;
  foreach ($itemsarr as $prop) {

      $puid = $universalkeys[$qvars['resource']][$qvars['class']];
      $dt2 = date('Y-m-d H:i:s');
      $itemsarr[$prop[$puid]]['lastPullTime'] = $dt2;

      // if( ($qvars['fotos'] == 'yes') && ($prop['PictureCount'] > 0) ) {
      if($qvars['fotos'] == 'yes') {
        unset($photos);
        // print_r($puid);
        $photos = $rets->GetObject($qvars['resource'], 'Photo', $prop[$puid],'*', 0);
        $itemsarr[$prop[$puid]]['images'] = '';
        if($qvars['resource'] == 'Property') {
          $itemsarr[$prop[$puid]]['imagepref'] = '';
        }
        $fnamestor = NULL;
        $photolist = array();
        foreach ($photos as $photo) {
          $photopreferred = $photo->getPreferred();
          if($photo->getObjectId() != '*') {
            $photofilename = $prop[$puid].'-'.$photo->getObjectId().'.jpg';
            // echo '<pre style="color: blue;">'.$photofilename.'</pre>';
            $photolist[] = $photofilename;
            $fname = RETSABSPATH.'/images/'.strtolower($qvars['resource']).'/'.$photofilename;
            $fnamebackup = RETSABSPATH.'/imagesbackup/'.strtolower($qvars['resource']).'/'.$photofilename;
            // array_push($itemsarr[$prop['ListingRid']]['images'], $photometa);
            if (file_exists($fname)) {
              echo "<p style='margin: 0; color: blue;'>photo file: ".$fname." already exists.</p>";
              // skip
            } else {
              $photobinary = $photo->getContent();
              file_put_contents($fname, $photobinary, LOCK_EX);
              file_put_contents($fnamebackup, $photobinary, LOCK_EX);
              echo "<p style='margin: 0; color: green;'>photo file: ".$fname." written to filesystem.</p>";
            }
          }
        }

        if( ($photopreferred == NULL) && ($qvars['resource'] == 'Property')) {
          $photopreferred = $photolist[0];
        }

        $itemsarr[$prop[$puid]]['images'] = implode("|",$photolist);
        if($qvars['resource'] == 'Property') {
          $itemsarr[$prop[$puid]]['imagepref'] = $photopreferred;
        }

      }

      $i++;
  }
  // echo '<pre style="background-color: brown; color: #fff;">count: '.$i.'</pre>';
  return $itemsarr;
}

/* ##### ######### ####### */
/* ##### GET ALL DATA #### */
/* ##### ######### ####### */

echo '<h1 style="border: 1px solid red; color: red;">start - '.date(DATE_RSS).'</h1>';

foreach($scenarios as $qvars) {
  // 1. Get RETS data
  $rets_data = runRetsQuery($qvars);

  echo '<pre style="background-color: brown; color: #fff;">';
  echo $rets_data['ListingAgentFullName'].'<br/>';
  echo $rets_data['ListingAgentMLSID'].'<br/>';
  echo $rets_data['ListingAgentNumber'].'<br/>';
  echo '</pre>';

  // 2. specify table we want data to go into
  $db_table = $qvars['resource'].'_'.$qvars['class'];
  // 3. populate local database with harvested RETS data
  $do = dbpopulate($rets_data,$db_table);
  echo $do.' --- '.$db_table;
}

/* ##### ######### ####### #### */
/* ##### PULL DATA INTO WP #### */
/* ##### ######### ####### #### */

// $get = file_get_contents('http://dev.bendhomes.com/import-property-test/');

// echo '<hr/>';
// print_r($get);
echo '<h1 style="border: 1px solid green; color: green;">completed - '.date(DATE_RSS).'</h1>';

?>

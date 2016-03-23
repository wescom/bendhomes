<?php
include("inc/retsabspath.php");
include(RETSABSPATH."/inc/header.php");

ini_set('max_execution_time', 0);

$qvars = array();
$qvars['class'] = (isset($_GET['class']) ? $_GET['class'] : 'RESI');
$qvars['class'] = strtoupper($qvars['class']);
$qvars['resource'] = (isset($_GET['resource']) ? $_GET['resource'] : 'Property');
$qvars['count'] = (isset($_GET['count']) ? $_GET['count'] : '999999999');
$qvars['count'] = (int) $qvars['count'];
$qvars['fotos'] = (isset($_GET['photos']) ? $_GET['photos'] : 'yes');

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
    $pulldate['recent'] = strtotime('-7 days');
  }
  // $pulldate['recent'] = file_get_contents('/Users/justingrady/web_dev/phpretstest/pulldates/'.$resource.'_'.$class.'.txt');
  $pulldate['retsquery'] = date('c',$pulldate['recent']);
  echo '<p style="background-color: orange;">using date: '.$pulldate['retsquery'].'</p>';
  file_put_contents($fnamerecent,$pulldate['now']);

  // first part, resource and class uses the minimum unique key for query, then last modified
  $usethisquery = ''.$funiversalqueries[$resource][$class].', (LastModifiedDateTime='.$pulldate['retsquery'].'+)';
  // $usethisquery = ''.$funiversalqueries[$resource][$class].'';

  print_r($usethisquery);
  return $usethisquery;
}

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

$temparr = $results->toArray();

function refactorarr($itemsarray,$ukeys,$qvars) {
  // refactor properties array so ListingRid is the key for each item
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

$itemsarr = refactorarr($temparr, $universalkeys, $qvars);

$i = 0;

foreach ($itemsarr as $prop) {

    $puid = $universalkeys[$qvars['resource']][$qvars['class']];
    $dt2 = date('Y-m-d H:i:s');
    $itemsarr[$prop[$puid]]['lastPullTime'] = $dt2;

    // if( ($qvars['fotos'] == 'yes') && ($prop['PictureCount'] > 0) ) {
    if($qvars['fotos'] == 'yes') {
      unset($photos);
      print_r($puid);
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
          echo '<pre style="color: blue;">'.$photofilename.'</pre>';
          $photolist[] = $photofilename;
          $fname = RETSABSPATH.'/images/'.strtolower($qvars['resource']).'/'.$photofilename;
          $fnamebackup = RETSABSPATH.'/imagesbackup/'.strtolower($qvars['resource']).'/'.$photofilename;
          // array_push($itemsarr[$prop['ListingRid']]['images'], $photometa);
          if (file_exists($fname)) {
            echo "<p style='margin: 1px; border: 2px solid blue; color: blue;'>photo file: ".$fname." already exists.</p>";
            // skip
          } else {
            $photobinary = $photo->getContent();
            file_put_contents($fname, $photobinary, LOCK_EX);
            file_put_contents($fnamebackup, $photobinary, LOCK_EX);
            echo "<p style='margin: 1px; border: 2px solid green; color: green;'>photo file: ".$fname." written to filesystem.</p>";
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

echo '<pre style="background-color: brown; color: #fff;">';
echo '<p>count: '.$i.'</p>';
// print_r($itemsarr);
echo '</pre>';

$jsondata = json_encode($itemsarr);
$jsonfilename = strtolower($qvars['class']).'-'.strtolower($qvars['resource']).'.json';
// file_put_contents('json/'.$jsonfilename, $jsondata, LOCK_EX);

$db_table = $qvars['resource'].'_'.$qvars['class'];

// print_r($itemsarr);

$do = dbpopulate($itemsarr,$db_table);

function dbpopulate($items,$dbtable) {
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );
  $dbConnection = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
  unset($db);

  foreach($items as $key => $array) {

    // escape the array for db username
    $escarray = array_map('mysql_real_escape_string', $array);

    print_r($escarray);/

    // $query  = "INSERT INTO ".$dbtable;
    $query  = "REPLACE INTO ".$dbtable;
    $query .= " (`".implode("`, `", array_keys($escarray))."`)";
    $query .= " VALUES ('".implode("', '", $escarray)."') ";

    echo '<div>';
    if (mysqli_query($dbConnection, $query)) {
        echo "<p style='background-color: green; color: #fff;'>Successfully inserted " . mysqli_affected_rows($dbConnection) . " row</p>";
    } else {
        echo "<p style='background-color: red; color: #fff;'>Error occurred: " . mysqli_error($dbConnection) . " row</p>";;
    }
    echo '</div>';

  }

}

?>

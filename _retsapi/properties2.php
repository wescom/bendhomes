<?php

include("inc/abspath.php");
include(ABSPATH."/inc/header.php");

$qvars = array();
$qvars['class'] = (isset($_GET['class']) ? $_GET['class'] : 'RESI');
$qvars['class'] = strtoupper($qvars['class']);
$qvars['resource'] = (isset($_GET['resource']) ? $_GET['resource'] : 'Property');
$qvars['count'] = (isset($_GET['count']) ? $_GET['count'] : '999999999');
$qvars['count'] = (int) $qvars['count'];
$qvars['fotos'] = (isset($_GET['photos']) ? $_GET['photos'] : 'yes');

// print_r($qvars);

?>
<head>
  <title><?php echo $class.' - '.$resource; ?> | Properties</title>
<head>
<?php

// $system = $rets->GetSystemMetadata();
// var_dump($system);

// $classes = $rets->GetClassesMetadata('Property');
// var_dump($classes);
// var_dump($classes->first());

// $objects = $rets->GetObject('OpenHouse');
// var_dump($objects);


// WORKS
// all residential properties

// $resource = 'Property';
// $class = 'RESI';
// $query = '(MLNumber=0+), (RESISTYL=|LOG), (LastModifiedDateTime=2016-02-10T00:00:00+)';

$resource = $qvars['resource'];
$class = $qvars['class'];
// $query = '(MLNumber=0+), (Status=|P), (LastModifiedDateTime=2016-03-10T00:00:00+)';
// $query = '(MLNumber=0+), (RESISTYL=|LOG), (LastModifiedDateTime=2016-02-10T00:00:00+)';
// $query = '(MLNumber=0+)';
$query = ''.$universalqueries[$resource][$class].', (LastModifiedDateTime=2016-02-10T00:00:00+)';
// $query = ''.$universalqueries[$resource][$class].'';

$results = $rets->Search(
    $resource,
    $class,
    $query,
    [
        'QueryType' => 'DMQL2', // it's always use DMQL2
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => $qvars['count'],
        'StandardNames' => 0, // give system names
    ]
);

// $totalrecordscount = $rets->TotalRecordsFound();
// echo '<h1>'.$totalrecordscount.'</h1>';

// $json = $results->toJSON();
$temparr = $results->toArray();

echo '<pre>';
print_r($temparr);
echo '</pre>';

function refactorarr($proparray) {
  // refactor properties array so ListingRid is the key for each item
  $newarray = array();
  foreach ($proparray as $prop) {
    foreach($prop as $key => $val) {
      if($key == 'ListingRid') {
        $newarray[$val] = $prop;
      }
    }
  }
  return $newarray;
}

// print_r(refactorarr($tenparr));

// $proparr = refactorarr($temparr);

$i = 0;

foreach ($proparr as $prop) {
    // echo '<div style="background-color: brown; color: #fff; margin: 1em; padding: 1em;">';
    // echo '<h3>MLNumber: '.$prop['MLNumber'].'</h3>';
    // echo '<p>';
    // echo $prop['StreetNumber'].' '.$prop['StreetNumberModifier'].' '.$prop['StreetName'].' '.$prop['StreetSuffix'];
    // echo '<br/>';
    // echo $prop['City'].', '.$prop['State'].' '.$prop['ZipCode'];
    // echo '<p style="background-color: green;"> '.($i + 1).' </p';
    // echo '</p>';
    // echo print_r($prop);
    // echo '</div>';
    if( ($qvars['fotos'] == 'yes') && ($prop['PictureCount'] > 0) ) {
      unset($photos);
      $photos = $rets->GetObject('Property', 'Photo', $prop['ListingRid'],'*', 0);
      $proparr[$prop['ListingRid']]['images'] = array();
      foreach ($photos as $photo) {
        $photobinary = $photo->getContent();
        $photopreferred = $photo->getPreferred();
        $photofilename = $prop['ListingRid'].'-'.$photo->getObjectId().'.jpg';
        $photometa = array(
          'file' => $photofilename,
          'preferred' => $photopreferred
        );
        array_push($proparr[$prop['ListingRid']]['images'], $photometa);
        file_put_contents('images/'.$photofilename, $photobinary, LOCK_EX);
      }
    }

    $i++;
}

echo '<pre>';
echo '<p>count: '.$i.'</p>';
print_r($proparr);
echo '</pre>';

$jsondata = json_encode($proparr);
$jsonfilename = strtolower($qvars['class']).'-'.strtolower($qvars['resource']).'.json';
file_put_contents('json/'.$jsonfilename, $jsondata, LOCK_EX);

/*
echo '<div style="background-color: green; color: #fff; margin: 1em; padding: 1em;">';
echo 'count: '.$i;
echo '</div>';
*/



?>

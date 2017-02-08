<?php

include("inc/retsabspath.php");
include(RETSABSPATH."/inc/header.php");

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
$query = '(MLNumber=0+), (LastModifiedDateTime=2000-02-10T00:00:00+)';

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

// $json = $results->toJSON();
$proparr = $results->toArray();

echo '<pre>';
print_r($proparr);
echo '</pre>';

$jsonfilename = strtolower($qvars['class']).'-'.strtolower($qvars['resource']).'.json';

file_put_contents('json/'.$jsonfilename, $json, LOCK_EX);

$i = 0;
foreach ($results as $record) {
    echo '<div style="background-color: brown; color: #fff; margin: 1em; padding: 1em;">';
    echo '<h3>MLNumber: '.$record['MLNumber'].'</h3>';
    echo '<p>';
    echo $record['StreetNumber'].' '.$record['StreetNumberModifier'].' '.$record['StreetName'].' '.$record['StreetSuffix'];
    echo '<br/>';
    echo $record['City'].', '.$record['State'].' '.$record['ZipCode'];
    echo '<p style="background-color: green;"> '.($i + 1).' </p';
    echo '</p>';
    // echo print_r($record);
    echo '</div>';
    if($qvars['fotos'] == 'yes') {
      unset($photos);
      // $photos = $rets->GetObject('Property', 'Photo', $record['ListingRid']);
      // $photos = $rets->GetObject('Property', 'Photo', $record['ListingRid'],'*', 0);
      $photos = $rets->GetObject('Property', 'Photo', $record['ListingRid'],'*', 0);
      foreach ($photos as $photo) {
        // print_r($photo);
        // echo '<hr/><hr/>';
        // echo $photo['content_type:protected'];
        $photobinary = $photo->getContent();
        $photofilename = $record['ListingRid'].'-'.$photo->getObjectId().'.jpg';
        file_put_contents('images/'.$photofilename, $photobinary, LOCK_EX);
      }
    }

    /*
    $rcds = $record->getFields();
    foreach($rcds as $field) {
      echo $field;
      echo '<br/>';
    }
    */

    /// echo '<br/>';
    // print_r($results->getMetadata());
    //echo $record->get('MarketingRemarks') . "\n";
    // echo $i;
    // echo '<br/>';
    /// echo '</pre>';


    // echo $record['Address'] . "\n";
    // is the same as:
    // echo $record->get('Address') . "\n";
    $i++;
}

echo '<div style="background-color: green; color: #fff; margin: 1em; padding: 1em;">';
echo 'count: '.$i;
echo '</div>';



?>

<?php

include("inc/retsabspath.php");
include(RETSABSPATH."/inc/header.php");

// $system = $rets->GetSystemMetadata();
// var_dump($system);

// $classes = $rets->GetClassesMetadata('Property');
// var_dump($classes);
// var_dump($classes->first());

// $objects = $rets->GetObject('OpenHouse');
// var_dump($objects);

// $object_types = $rets->GetMetadataObjects('Property');
// var_dump($object_types);

// WORKS
// all residential properties
$resource = 'Property';
$class = 'RESI';
// $query = '(MLNumber=0+), (RESISTYL=|LOG), (LastModifiedDateTime=2016-02-10T00:00:00+)';
$query = '(MLNumber=0+), (LastModifiedDateTime=2016-02-10T00:00:00+)';

// $search = $rets->Search($resource,$class,$query);
// $results = $search;

$results = $rets->Search(
    $resource,
    $class,
    $query,
    [
        'QueryType' => 'DMQL2',
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => 1,
        'StandardNames' => 0, // give system names
    ]
);

// sleep(10);
// print_r($results);

$i = 0;
foreach ($results as $record) {
    echo '<pre style="background-color: brown; color: #fff; margin: 1em;">';
    // print_r($record);
    // print_r($record->getFields());
    $rcds = $record->getFields();
    foreach($rcds as $field) {
      echo $field;
      echo '<br/>';
    }
    echo '<br/>';
    // print_r($results->getMetadata());
    // echo $record->get('MarketingRemarks') . "\n";
    // echo $i;
    // echo '<br/>';
    echo '</pre>';
    $i++;

    // echo $record['Address'] . "\n";
    // is the same as:
    // echo $record->get('Address') . "\n";
}



?>

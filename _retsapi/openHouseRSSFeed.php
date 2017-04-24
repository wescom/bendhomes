<?php

/* ##### Populate API bh_rets database with data ##### */


function getAllOpens() {
    $db = array(
        'host' => 'localhost',
        'username' => 'phrets',
        'password' => 'hCqaQvMKW9wJKQwS',
        'database' => 'bh_rets'
    );
    $con = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
    unset($db);
 
    $opensArray = array();
    if (mysqli_connect_errno()) {
        echo "failed to connect ".mysqli_connect_error();
        }
        else {
                $qry = "SELECT AgentFirstName, AgentLastName, ListingOfficeNumber, description, MLNumber, StartDateTime, TimeComments from OpenHouse_OPEN";

                $result = mysqli_query($con, $qry);
                while($row = mysqli_fetch_array($result)) {
                        $rec = array(
                              'afname' => $row['AgentFirstName'], 
                              'alname' => $row['AgentLastName'], 
                              'officeNum' => $row['ListingOfficeNumber'],
                              'description' => $row['description'],
                              'MLNumber' = > $row['MLNumber'],
                              'startDateTime' => $row['StartDateTime'],
                              'timeComments' => $row['TimeComments']
                        );
                        array_push($opnesArray, $rec);
                }

                mysqli_close($con);
        }
    }
    return $statusArray;
}

function getOpenHouseData($open){
    var_dump($open);
    echo '<p style="background-color: green;">using date: '.$open['MLNumber'].' - '.$open['startDate'].'</p>';
}

function displayRssFeed($opensWithData){

}

echo '<h1>RSS Feed Start</h1>';
$opensArray = array();
$opensArray = getAllOpens();
$opensWithData = array();

foreach($opensArray as $open){
    $openWithData = getOpenHouseData($open);
    array_push($opensWithData);
}

displayRssFeed($opensWithData);


?>
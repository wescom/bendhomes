<?php

/* ##### Populate API bh_rets database with data ##### */


function getAllOpens() {
    $db = array(
        'host' => 'localhost',
        'username' => 'phrets',
        'password' => 'hCqaQvMKW9wJKQwS',
        'database' => 'bh_rets'
    );


    $conn = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);
    unset($db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $opensArray = array();

    $query = "SELECT AgentFirstName, AgentLastName, ListingOfficeNumber, MLNumber, StartDateTime, TimeComments from OpenHouse_OPEN";
    $result = $conn->query($query);  

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $rec = array(
                      'afname' => $row['AgentFirstName'], 
                      'alname' => $row['AgentLastName'], 
                      'officeNum' => $row['ListingOfficeNumber'],
                      'MLNumber' => $row['MLNumber'],
                      'startDateTime' => $row['StartDateTime'],
                      'timeComments' => $row['TimeComments']
            );
            array_push($opnesArray, $rec);
        }
    }
    $conn->close();
    return $opensArray;
}

function getOpenHouseData($open){
    var_dump($open);
    echo '<p style="background-color: green;">using date: '.$open['MLNumber'].' - '.$open['startDate'].'</p>';
}

function displayRssFeed($opensWithData){

}

echo '<h1>RSS Feed Start</h1>';
$opensArray = getAllOpens();
$opensArray = 
$opensWithData = array();

foreach($opensArray as $open){
    $openWithData = getOpenHouseData($open);
    array_push($opensWithData);
}

displayRssFeed($opensWithData);


?>
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
            //var_dump($row);
            $rec = array(
                      'afname' => $row['AgentFirstName'], 
                      'alname' => $row['AgentLastName'], 
                      'officeNum' => $row['ListingOfficeNumber'],
                      'MLNumber' => $row['MLNumber'],
                      'startDateTime' => $row['StartDateTime'],
                      'timeComments' => $row['TimeComments']
            );
            array_push($opensArray, $rec);
        }
    }
    $conn->close();
    return $opensArray;
}

function getOpenHouseData($open){
    
    echo '<p style="background-color: green; color:white">MLNumber: '.$open['MLNumber'].' - StartDate: '.$open['startDateTime'].'</p>';

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

    $rec = array();

    $query = "(SELECT images, StreetNumber, StreetName, StreetSuffix, RESISRHI, SquareFootage, ListingPrice from Property_BUSI Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, RESISRHI, SquareFootage, ListingPrice from Property_COMM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, RESISRHI, SquareFootage, ListingPrice from Property_FARM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, RESISRHI, SquareFootage, ListingPrice from Property_LAND Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, RESISRHI, SquareFootage, ListingPrice from Property_MULT Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, RESISRHI, SquareFootage, ListingPrice from Property_RESI Where MLNumber = ".$open['MLNumber'].")";

    $result = $conn->query($query); 

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $rec = array(
                  'afname' => $open['AgentFirstName'], 
                  'alname' => $open['AgentLastName'], 
                  'officeNum' => $open['ListingOfficeNumber'],
                  'MLNumber' => $open['MLNumber'],
                  'startDateTime' => $open['StartDateTime'],
                  'timeComments' => $open['TimeComments'],
                  'images' => $row['images'],
                  'StreetNumber' => $row['StreetNumber'],
                  'StreetName' => $row['StreetName'],
                  'StreetSuffix' => $row['StreetSuffix'],
                  'area' => $row['RESISRHT'],
                  'SquareFootage' => $row['SquareFootage'],
                  'ListingPrice' => $row['ListingPrice'],
            );
        }
    }


    return $open;
}

function displayRssFeed($opensWithData){

}

echo '<h1>RSS Feed Start</h1>';
$opensArray = getAllOpens();

$opensWithData = array();

foreach($opensArray as $open){
    $openWithData = getOpenHouseData($open);
    array_push($opensWithData, $openWithData);
}

var_dump($opensWithData);
displayRssFeed($opensWithData);


?>
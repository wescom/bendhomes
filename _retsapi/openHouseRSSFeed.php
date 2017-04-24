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
    
    //echo '<p style="background-color: green; color:white">MLNumber: '.$open['MLNumber'].' - StartDate: '.$open['startDateTime'].'</p>';

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

    $query = "(SELECT images, StreetNumber, StreetName, StreetSuffix, City, ListingPrice from Property_BUSI Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, ListingPrice from Property_COMM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, ListingPrice from Property_FARM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, ListingPrice from Property_LAND Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, ListingPrice from Property_MULT Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, ListingPrice from Property_RESI Where MLNumber = ".$open['MLNumber'].")";

    $result = $conn->query($query); 

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $rec = array(
                  'afname' => $open['afname'], 
                  'alname' => $open['alname'], 
                  'officeNum' => $open['officeNum'],
                  'MLNumber' => $open['MLNumber'],
                  'startDateTime' => $open['startDateTime'],
                  'timeComments' => $open['timeComments'],
                  'images' => $row['images'],
                  'StreetNumber' => $row['StreetNumber'],
                  'StreetName' => $row['StreetName'],
                  'StreetSuffix' => $row['StreetSuffix'],
                  'area' => $row['City'],
                  'ListingPrice' => $row['ListingPrice'],
            );
        }
    }

    return $rec;
}

function displayRssFeed($opensWithData){
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:ynews="http://news.yahoo.com/rss/" xmlns:bing="http://bing.com/schema/media/" xmlns:dc="http://purl.org/dc/elements/1.1/">';
    echo '<channel>';
    echo '<title>BendHomes Open Houses</title>';
    echo '<link>http://www.bendhomes.com/_retsapi/openHouseRSSFeed.php</link>';
    echo '<description>BendHomes Open Houses RSS Feed</description>';
    echo '<language>en-us</language>';

    foreach($opensWithData as $itm){
      var_dump($itm);
        echo "<item>";
        echo "<title>".$itm['StreetNumber']." ".$itm['StreetName']." ".$itm['StreetSuffix'].", ".$itm['City']."</title>";
        echo "<description>".$itm['startDateTime']. " ".$itm['timeComments']."</description>";

        $imgArray = explode("|", $itm['images']);
        echo '<media:content medium="image" type="image/jpeg" url="http://www.bendhomes.com/_retsapi/imagesProperties/'.$imgArray[0].'">';
        echo '</media:content>';
        echo "</item>";
    }

    echo '</channel>';
    echo '</rss>';
}

echo '<h1>RSS Feed Start</h1>';
$opensArray = getAllOpens();

$opensWithData = array();

foreach($opensArray as $open){
    $openWithData = getOpenHouseData($open);
    array_push($opensWithData, $openWithData);
}

displayRssFeed($opensWithData);


?>
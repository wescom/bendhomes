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

    $dt = date("Y-m-d");
    $dt = strtotime($dt);
    $dt = strtotime("+7 day", $dt);
    $endDate = date("Y-m-d",$dt);

    $query = "SELECT MLNumber, StartDateTime from OpenHouse_OPEN where StartDateTime < '".$endDate."' Order by MLNumber, StartDateTime";
    $result = $conn->query($query);  

    $oldMls = 0;
    $rec = array();
    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            if ($row['MLNumber'] == $oldMls) {
            } else {
              if ($oldMls != 0){
                  array_push($opensArray, $rec);
              }
              $oldMls = $row['MLNumber'];
              $rec = array(
                        'MLNumber' => $row['MLNumber']
              );
            }
            
        }
        array_push($opensArray, $rec);  // push last record
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

    $query = "(SELECT images, Status from Property_BUSI Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, Status from Property_COMM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, Status from Property_FARM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, Status from Property_LAND Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, Status from Property_MULT Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, Status from Property_RESI Where MLNumber = ".$open['MLNumber'].")";

    $result = $conn->query($query); 

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $rec = array(
                  'images' => $row['images'],
                  'Status' => $row['Status'],
                  'MLNumber' => $open['MLNumber']
            );
        }
    }

    return $rec;
}

function emptyDirectory() {
    // empty the directory imagesNewsletters
}

function resizeAndSavePhoto($opensWithImages){
    
    foreach($opensWithImages as $itm){
        if ($itm['Status'] == 'Active'){
            echo "MLS: ".$itm['MLNumber']." - resizing photo: ";
            $imgArray = explode("|", $itm['images']);
            echo $imgArray[0];

            $pic = new Imagick();
            $pic->readImage("./imagesProperties/".$imgArray[0]);
            $pic->resizeImage("250","175",Imagick::FILTER_LANCZOS,1);
            $pic->writeImage("./imagesNewsletters/".$imgArray[0]);
            $pic->clear();
            $pic->destroy();
        }
    }
}


$opensArray = getAllOpens();

$opensWithData = array();

foreach($opensArray as $open){
    $openWithData = getOpenHouseData($open);
    array_push($opensWithData, $openWithData);
}

emptyDirectory();
resizeAndSavePhoto($opensWithData);


?>
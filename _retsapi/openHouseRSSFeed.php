<?php

/* #########################################################

        Hit database for open houses and create an rss feed of them. 
        Currently goes back 7 days from current day hit.  This also
        depends on the script running in the background that grabs
        the images from thier normal full size folder and using 
        imagick, creates a smaller usable size for things such as
        mailchimp.  That file is called getAndResizeNLPhotos.php.  
        It is run from the /var/shellscripts folder script called
        getAndResizeNLPhotos.sh via a crontab job once an hour so the
        photos will be up to date with the rets table data.

############################################################ */


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

    $query = "SELECT AgentFirstName, AgentLastName, OfficeName, ListingOfficeNumber, MLNumber, StartDateTime, TimeComments from OpenHouse_OPEN where StartDateTime < '".$endDate."' Order by MLNumber, StartDateTime";
    $result = $conn->query($query);  

    $oldMls = 0;
    $rec = array();
    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            if ($row['MLNumber'] == $oldMls) {
                // this means we already have this msl in array, so concat the new listing time only
                $rec['startDateTime'] = $rec['startDateTime']. "|".$row['StartDateTime'];
                $rec['timeComments'] = $rec['timeComments']. "|".$row['TimeComments'];
            } else {
                // this is a new mls, so create the array, but don't push yet - next loop could be same
                // mls and just new show time.  In that case we concat to the startDateTime and TimeComments
                if ($oldMls != 0){
                    array_push($opensArray, $rec);  // make sure not the first loop with empty array
                }
                $oldMls = $row['MLNumber'];
                $rec = array(
                        'afname' => $row['AgentFirstName'], 
                        'alname' => $row['AgentLastName'], 
                        'officeNum' => $row['ListingOfficeNumber'],
                        'officeName' => $row['OfficeName'],
                        'MLNumber' => $row['MLNumber'],
                        'startDateTime' => $row['StartDateTime'],
                        'timeComments' => $row['TimeComments']
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

    $query = "(SELECT images, StreetNumber, StreetName, StreetSuffix, City, State, ZipCode, ListingPrice, Status  from Property_BUSI Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, State, ZipCode, ListingPrice, Status from Property_COMM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, State, ZipCode, ListingPrice, Status  from Property_FARM Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, State, ZipCode, ListingPrice, Status  from Property_LAND Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, State, ZipCode, ListingPrice, Status  from Property_MULT Where MLNumber = ".$open['MLNumber'].")";
    $query .= " UNION (SELECT images, StreetNumber, StreetName, StreetSuffix, City, State, ZipCode, ListingPrice, Status  from Property_RESI Where MLNumber = ".$open['MLNumber'].")";

    $result = $conn->query($query); 

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $rec = array(
                  'afname' => $open['afname'],          // push the old data onto the array
                  'alname' => $open['alname'], 
                  'officeNum' => $open['officeNum'],
                  'officeName' => $open['officeName'],
                  'MLNumber' => $open['MLNumber'],
                  'startDateTime' => $open['startDateTime'],
                  'timeComments' => $open['timeComments'],
                  'images' => $row['images'],               // push the new data onto the array
                  'StreetNumber' => $row['StreetNumber'],
                  'StreetName' => $row['StreetName'],
                  'StreetSuffix' => $row['StreetSuffix'],
                  'State' => $row['State'],
                  'ZipCode' => $row['ZipCode'],
                  'area' => $row['City'],
                  'ListingPrice' => $row['ListingPrice'],
                  'Status' => $row['Status']
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
        if ($itm['Status'] == 'Active'){
            $price = number_format($itm['ListingPrice']);
       
            echo "<item>";
            echo "<title>".$itm['StreetNumber']." ".$itm['StreetName']." ".$itm['StreetSuffix'].", ".$itm['area']." - $".$price."</title>";
            $linkAddress = $itm['StreetNumber']."-".$itm['StreetName']."-".$itm['StreetSuffix']."-".$itm['area']."-".$itm['State']."-".$itm['ZipCode'];
            $linkAddress = htmlspecialchars($linkAddress, ENT_QUOTES);
            echo "<link><![CDATA[http://bendhomes.idxbroker.com/idx/details/listing/a098/".$itm['MLNumber']."/".$linkAddress."]]></link>";
            $dateArray = explode("|", $itm['startDateTime']);
            $commArray = explode("|", $itm['timeComments']);
            $count = 0;
            echo "<description>";
            $firstLoop = true;
            foreach($dateArray as $date) {
                $date = date("D", strtotime($date));
                if ($firstLoop == true) {
                    $firstLoop = false;
                    echo $date." (".$commArray[$count].")";
                } else {
                    echo ", ".$date." (".$commArray[$count].")";
                }
                $count++;
            }
            echo "</description>";
            echo "<dc:creator>".htmlspecialchars($itm['officeName'], ENT_QUOTES)."</dc:creator>";
            $imgArray = explode("|", $itm['images']);
            echo '<media:content medium="image" type="image/jpeg" url="http://www.bendhomes.com/_retsapi/imagesNewsletters/'.$imgArray[0].'">';
            echo '</media:content>';
            echo "</item>";
        }
    }

    echo '</channel>';
    echo '</rss>';
}


// entry point of file

$opensArray = getAllOpens();

$opensWithData = array();

foreach($opensArray as $open){
    $openWithData = getOpenHouseData($open);
    array_push($opensWithData, $openWithData);
}

displayRssFeed($opensWithData);


?>
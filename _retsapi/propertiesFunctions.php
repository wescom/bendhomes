<?php

include("/var/www/html/_retsapi/inc/header.php");
include("/var/databaseIncludes/retsDBInfo.php");

function getScenarios() {

        $centralcount = 999999;
        $scenarios = array(
                'Property_BUSI' => array(
                        'count' => $centralcount,
                        'fotos' => 'yes',
                        'resource' => 'Property',
                        'class' => 'BUSI'
                ),
                'Property_COMM' => array(
                        'count' => $centralcount,
                        'fotos' => 'yes',
                        'resource' => 'Property',
                        'class' => 'COMM'
                ),
                'Property_FARM' => array(
                        'count' => $centralcount,
                        'fotos' => 'yes',
                        'resource' => 'Property',
                        'class' => 'FARM'
                ),
                'Property_LAND' => array(
                        'count' => $centralcount,
                        'fotos' => 'yes',
                        'resource' => 'Property',
                        'class' => 'LAND'
                ),
                'Property_MULT' => array(
                        'count' => $centralcount,
                        'fotos' => 'yes',
                        'resource' => 'Property',
                        'class' => 'MULT'
                ),
                'Property_RESI' => array(
                        'count' => $centralcount,
                        'fotos' => 'yes',
                        'resource' => 'Property',
                        'class' => 'RESI'
                )
        );
        return $scenarios;
}

/* ##### Build RETS db query ##### */
function buildRetsQuery($fqvars, $pullDate) {
        $resource = $fqvars['resource'];
        $class = $fqvars['class'];

        //$pullDate = "2001-06-01T00:00:00-08:00"; //date('c',$pulldate['recent']);
        $funiversalqueries = universalqueries($pullDate);

        echo '<p style="background-color: orange;">using date: '.$pullDate.'</p>';

        // first part, resource and class uses the minimum unique key for query, then last modified
        // $usethisquery = ''.$funiversalqueries[$resource][$class].', (LastModifiedDateTime='.$pulldate['retsquery'].'+)';
        $usethisquery = ''.$funiversalqueries[$resource][$class].'';
        return $usethisquery;
}

/* ##### Refactor returned data with key supplied by universalkeys in header file ##### */
function refactorarr($itemsarray,$ukeys,$qvars) {
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

/*function removeOldSoldsFromArray($itemsarr) {
    $xMonthsAgo = (int)str_replace("-", "", date('Y-m-d', strtotime("-6 months")));
    $newarray = array();
    foreach($itemsarr as $prop){
        $pullNumber = explode('T', $prop['LastModifiedDateTime']);
        $pullNumber = (int)str_replace("-", "", $pullNumber[0]);
        
        if (($prop['Status'] == "Sold") && ($pullNumber < $xMonthsAgo)){
            echo "Skipping ".$prop['ListingRid']." **** Status: ".$prop['Status']." Last Modified: ".$prop['LastModifiedDateTime']." PullNumber: ".$pullNumber." Today: ".$xMonthsAgo."</br>";
        } else {
            echo "Status3: ".$prop['Status']." Last Modified: ".$prop['LastModifiedDateTime']." PullNumber: ".$pullNumber." Today: ".$xMonthsAgo."</br>";
            array_push($newarray, $prop);
        }
        
    }
    return $newarray;
}*/

function getSetPullDate() {

        $pulldate = array();
        $pulldate['now'] = (int) time();

        $pulldate['recent'] = strtotime("-3 hours"); // 1 day, 2 days, 1 year, 2 years, 1 week, 2 weeks, etc
        $pulldate['retsquery'] = date('c',$pulldate['recent']);

        return $pulldate['retsquery'];
}

/* ##### ######### ##### */
/* ##### RETS QUERY #### */
/* ##### ######### ##### */

function runRetsQuery($qvars, $pullDate) {
        global $universalkeys;
        global $rets;

        print_r($qvars);
        $query = buildRetsQuery($qvars, $pullDate);
        //print_r($query);

        $results = $rets->Search(
                $qvars['resource'],
                $qvars['class'],
                $query,
                        [
                        'QueryType' => 'DMQL2', // it's always use DMQL2
                        'Count' => 1, // count and records
                        'Format' => 'COMPACT',
                        'Limit' => $qvars['count'],
                        'StandardNames' => 0, // give system names
                        'Select' => 'ListingRid',
                ]
        );

        echo '<pre>';
        //print_r($results);
        echo '</pre>';

        // convert from objects to array, easier to process
        $temparr = $results->toArray();
        // refactor arr with keys supplied by universalkeys in header
        $itemsarr = refactorarr($temparr, $universalkeys, $qvars);


        echo '<pre style="background-color: brown; color: #fff;">count: '.sizeof($itemsarr).'</pre>';

        return $itemsarr;
}

function getPropertyData($qvars, $pullDate, $idArray){
    global $universalkeys;
    global $rets;

    //$query = buildRetsQuery($qvars, $pullDate);
    $idListArray = [];
    foreach ($idArray as $itm) {
        array_push($idListArray, $itm['ListingRid']);
    }

    $idList = implode(",", $idListArray);

    $query = "(ListingRid=".$idList.")";

    print_r($query);

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

    echo '<pre>';
        //print_r($results);
    echo '</pre>';

    // convert from objects to array, easier to process
    $temparr = $results->toArray();
    // refactor arr with keys supplied by universalkeys in header
    $itemsarr = refactorarr($temparr, $universalkeys, $qvars);

    // remove any old 'sold' properties from array
    //$itemsarr = removeOldSoldsFromArray($itemsarr);

    $xMonthsAgo = (int)str_replace("-", "", date('Y-m-d', strtotime("-6 months")));
    
    // get the property photos and save locally as well as add to properties array
    foreach($itemsarr as $prop) {
        $savePhoto = 1;  

        // if old 'sold' property, set flag to not save the photos to our site
        $pullNumber = explode('T', $prop['LastModifiedDateTime']);
        $pullNumber = (int)str_replace("-", "", $pullNumber[0]);
        
        if (($prop['Status'] == "Sold") && ($pullNumber < $xMonthsAgo)){
            $savePhoto = 0; // set flag to not save
            //echo "not processing photos...";
        } else {
        
            $puid = $universalkeys[$qvars['resource']][$qvars['class']];
            if ($qvars['fotos'] == 'yes') {
                unset($photos);
                $photos = $rets->GetObject($qvars['resource'], 'Photo', $prop[$puid],'*', 0);
                $itemsarr[$prop[$puid]]['images'] = '';
                if($qvars['resource'] == 'Property') {
                    $itemsarr[$prop[$puid]]['imagepref'] = '';
                }
                $photopreferred == NULL;
                $fnamestor = NULL;
                $haveOne = 0;
                $photolist = array();

                    foreach ($photos as $photo) {
                        $photopreferred = $photo->getPreferred();
                        if($photo->getObjectId() != '*') {
                            $haveOne = 1;
                            $photofilename = $prop[$puid].'-'.$photo->getObjectId().'.jpg';
                            $photolist[] = $photofilename;
                            $fname = '/var/www/html/_retsapi/imagesProperties/'.$photofilename;
                            $photobinary = $photo->getContent();
                            file_put_contents($fname, $photobinary, LOCK_EX);
                        }
                    }

                if( ($photopreferred == NULL) && ($qvars['resource'] == 'Property') && ($haveOne == 1)) {
                    $photopreferred = $photolist[0];
                }
                $itemsarr[$prop[$puid]]['images'] = implode("|",$photolist);
                if($qvars['resource'] == 'Property') {
                    $itemsarr[$prop[$puid]]['imagepref'] = $photopreferred;
                }
            }
        }
    }

    echo '<pre style="background-color: brown; color: #fff;">count2: '.sizeof($itemsarr).'</pre>';

    return $itemsarr;
}

function getAllRetsIdsQuery($qvars, $pullDate) {
        global $universalkeys;
        global $rets;

        print_r($qvars);
        $query = buildRetsQuery($qvars, $pullDate);
        //print_r($query);

        $results = $rets->Search(
                $qvars['resource'],
                $qvars['class'],
                $query,
                        [
                        'QueryType' => 'DMQL2', // it's always use DMQL2
                        'Count' => 1, // count and records
                        'Format' => 'COMPACT',
                        'Limit' => $qvars['count'],
                        'StandardNames' => 0, // give system names
                        'Select' => 'ListingRid',
                ]
        );

        echo '<pre>';
        //print_r($results);
        echo '</pre>';

        // convert from objects to array, easier to process
        $temparr = $results->toArray();
        // refactor arr with keys supplied by universalkeys in header
        $itemsarr = refactorarr($temparr, $universalkeys, $qvars);


        echo '<pre style="background-color: brown; color: #fff;">count: '.sizeof($itemsarr).'</pre>';

        return $itemsarr;
}

function savePropertyData($qvars, $itemsarr) {
    $dbConnection = mysqli_connect(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $reportout = "";
    $dbtable = $qvars['resource'].'_'.$qvars['class'];

    $xMonthsAgo = (int)str_replace("-", "", date('Y-m-d', strtotime("-6 months")));

    foreach($itemsarr as $key => $array) {
      
        // escape the array for db username
        //$escarray = array_map('mysql_real_escape_string', $array);
        foreach ($array as $key => $value) {
            $escarray[$key] = mysqli_real_escape_string($dbConnection, $value);
        }
        //echo "<p style='backgrond-color:green'>status: ".$escarray['Status']."</p>";
        $pullNumber = explode('T', $escarray['LastModifiedDateTime']);
        $pullNumber = (int)str_replace("-", "", $pullNumber[0]);

        if (($escarray['Status'] == "Sold") && ($pullNumber < $xMonthsAgo)){
            //echo "Skipping ".$escarray['ListingRid']." - ".$escarray['MLNumber']." **** Status: ".$escarray['Status']." Last Modified: ".$escarray['LastModifiedDateTime']." PullNumber: ".$pullNumber." Today: ".$xMonthsAgo."</br>";
        } else {

            echo "Adding ".$escarray['ListingRid']." - ".$escarray['MLNumber']." : ".$escarray['Status']." Last Modified: ".$escarray['LastModifiedDateTime']." PullNumber: ".$pullNumber." Today: ".$xMonthsAgo."</br>";
        
            $query  = "REPLACE INTO ".$dbtable;
            $query .= " (`".implode("`, `", array_keys($escarray))."`)";
            $query .= " VALUES ('".implode("', '", $escarray)."') ";

            if (mysqli_query($dbConnection, $query)) {
                $reportout .= "<p style='margin: 0; background-color: green; color: #fff;'>Successfully inserted " . mysqli_affected_rows($dbConnection) . " row</p>";
            } else {
                $reportout .= "<p style='margin: 0; background-color: red; color: #fff;'>Error occurred: " . mysqli_error($dbConnection) . " row</p>";;
            }
        }
    }

    //echo '</pre>';
    mysqli_close($dbConnection);
    return $reportout;
}


function getAllOurPropertyIds($qvars) {
        $conn = new mysqli(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }

        $dbtable = $qvars['resource'].'_'.$qvars['class'];
        $query = "select ListingRid from ".$dbtable." ORDER BY ListingRid ASC";
        echo "<br>query: ".$query."<br>";
        $result = $conn->query($query);

        $idArray = [];
        if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                        array_push($idArray, $row['ListingRid']);
                }
        }
        echo '<pre style="color: blue;">OUR Ids - count: '.sizeof($idArray).'</pre>';
        mysqli_close($conn);

        return $idArray;
}

function compareAndGetBads($retsIdArray, $ourIdArray) {
        // anything that is in ours but not rets, should be deleted from ours
        $badIds = "";
        $count = 0;
        $idArray = [];
        $idArray = array_diff($ourIdArray, $retsIdArray);
        echo '<pre style="color: red;">'.$count.', BAD Ids - count: '.sizeof($idArray).' - '.implode(",",$idArray).'</pre>';
        return $idArray;
}

function deletePropertyIds($qvars, $idArray) {
        $conn = new mysqli(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }

        $dbtable = $qvars['resource'].'_'.$qvars['class'];
        $query = "DELETE from ".$dbtable." WHERE ListingRid IN (".implode(", ",$idArray).")";
        echo '<p>'.$query.'</p>';

        foreach($idArray as $id){
            foreach(glob('/var/www/html/_retsapi/imagesProperties/'.$id.'-*.jpg') as $file)
            unlink($file);
        }

        if($conn->query($query)) {
                echo "<p>Success!!!!</p>";
        } else {
                echo "<p>Error: ".mysqli_error($conn)."</p>";
        }
        mysqli_close($conn);

}

function returnOldSolds($qvars){
    $conn = new mysqli(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $dbtable = $qvars['resource'].'_'.$qvars['class'];
    $query = "SELECT ListingRid, LastModifiedDateTime from ".$dbtable." where status = 'Sold'"; 

    $result = $conn->query($query);
    $idArray = [];
    if ($result->num_rows > 0) {
        $xMonthsAgo = (int)str_replace("-", "", date('Y-m-d', strtotime("-6 months")));

        while($row = $result->fetch_assoc()) {
            $pullNumber = explode('T', $row['LastModifiedDateTime']);
            $pullNumber = (int)str_replace("-", "", $pullNumber[0]);
            if ($xMonthsAgo < $pullNumber) {
                array_push($idArray, $row['ListingRid']);
            }
        }
    }
    return $idArray;
}

function getMissingProps($qvars, $idArray) {
        global $universalkeys;
        global $rets;

        $idList = implode(",", $idArray);

        $query = "(ListingRid=".$idList.")";

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

        // convert from objects to array, easier to process
        $temparr = $results->toArray();
        // refactor arr with keys supplied by universalkeys in header
        $itemsarr = refactorarr($temparr, $universalkeys, $qvars);
        return $itemsarr;
}

/*function executeUpdateAgentsLookupByMLSTable() {

        echo '<h1 style="border: 3px solid orange; padding: 3px;">start - '.date(DATE_RSS).' - v2100</h1>';

        $pullDate = getSetPullDate();
//      $pullDate = '2001-01-01T00:00:00-08:00';

        $scenarios = getScenarios();

        foreach($scenarios as $qvars) {

                // 1. Get RETS data
                echo '<pre style="color:green">'.$qvars['class'].'</pre>';
                $rets_data = runRetsQuery($qvars, $pullDate);
                saveToOurTable($rets_data);
                echo '<pre>';
                print_r($rets_data);
                echo '</pre>';

        }

        echo '<h1 style="border: 3px solid orange; color: green; padding: 3px;">completed - '.date(DATE_RSS).'</h1>';

}*/

/*function cleanAgentsLookupByMLSTable() {

        $pullDate = '2001-01-01T00:00:00-08:00';
        $scenarios = getScenarios();

        $our_ids = getAllOurIds();
        $rets_ids = [];
        foreach($scenarios as $qvars) {
                $rets_data = runRetsQuery($qvars, $pullDate);
                foreach ($rets_data as $itm) {
                        array_push($rets_ids, $itm['ListingRid']);
                }

        }
        sort($rets_ids);

        $badIds = compareAndGetBads($rets_ids, $our_ids);
        if (sizeof($badIds) > 0) {
                deleteBadIds($badIds);

                echo "<pre>Bad Ids: ".implode(", ",$badIds)."</pre>";
        } else {
                echo "No Bad Ids to delete.";
        }

        $missingIds = compareAndGetBads($our_ids, $rets_ids);
        if (sizeof($missingIds) > 0) {
                foreach($scenarios as $qvars) {
                        $rets_data = getMissingProps($qvars, $missingIds);
                        saveMissingToOurTable($rets_data);
                }
                echo "<pre>Missing Ids: ".implode(", ",$missingIds)."</pre>";
        } else {
                echo "No missing Ids to get.";
        }
        //echo "<pre>Rets: ".implode(", ",$rets_ids)."</pre>";
        //echo "<pre>Ours: ".implode(", ",$our_ids)."</pre>";
}*/

function cleanPropertiesTable() {
    $scenarios = getScenarios();

    $pullDate = '2001-01-01T00:00:00-08:00';

    foreach($scenarios as $qvars) {

        // Checking for any ids we have in our table that are no longer
        // in the rets feed and then removing them from our table.
        $rets_ids = [];
        $rets_idArray = runRetsQuery($qvars, $pullDate);
        foreach($rets_idArray as $id) {
            array_push($rets_ids, $id['ListingRid']);
        }
        //echo "<pre>RetsIds: ".implode(", ",$rets_ids)."</pre>";

        $our_ids = getAllOurPropertyIds($qvars);
        // echo "<pre>OurIds: ".implode(", ",$our_ids)."</pre>";

        $badIds = compareAndGetBads($rets_ids, $our_ids);
        if (sizeof($badIds) > 0) {
            deletePropertyIds($qvars, $badIds);
            echo "<pre>Bad Ids: ".implode(", ",$badIds)."</pre>";
        } else {
            echo " No Bad Ids to delete.\n\r";
        }

        // Looking for any actives that are in the rets feed but are
        // not in our database and then bringing them in.
        // $ourActive_ids = getOurActiveIds($qvars)
        // $retsActive_ids = getRetsActiveIds($qvars)
        // missingActive_ids = compareAndGetBads($ourActive_ids, $retsActive_ids)
        //$retsReturnData = getPropertyData($qvars, $pullDate, $missingActive_ids);
        // $returnString = savePropertyData($qvars, $retsReturnData);
        //echo '<pre>'.$returnString;
        //echo '</pre>';

        // looking for any properties with a status of 'Sold' that are older 
        // than 6 months and then deleting them from our database
        $oldIdsArray = returnOldSolds($qvars);
        if (sizeof($oldIdsArray) > 0){
            //deletePropertyIds($qvars, $oldIdsArray);
            echo "<pre>Old Ids to delete: ".implode(", ", $oldIdsArray)."</pre>";
        } else {
            echo " No old solds to delete.\r\n";
        }

    }
}

function executeUpdatePropertiesTable() {

    $scenarios = getScenarios();

    $pullDate = '2001-01-01T00:00:00-08:00';
    //$pullDate = getSetPullDate();

    $start = 96500; // start index
    $count = 500; // how many past start to grab

    foreach($scenarios as $qvars) {

            $retsIdArray = getAllRetsIdsQuery($qvars, $pullDate);

            if (sizeof($retsIdArray) > $start) {
                $pieceArray = array_slice($retsIdArray, $start, $count);
                //$pieceArray = $retsIdArray;


                //echo implode(',', $pieceArray)
                $retsReturnData = getPropertyData($qvars, $pullDate, $pieceArray);

               /* echo '<pre>';
                print_r($retsReturnData);
                echo '</pre>';*/

                $returnString = savePropertyData($qvars, $retsReturnData);
                echo '<pre>'.$returnString;
                echo '</pre>';
                
            } else {
                echo '<pre style="color:red">At end of array.</pre>';
            }
    }
}

?>
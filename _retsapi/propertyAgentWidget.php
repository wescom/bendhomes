<?php

include_once '/var/databaseIncludes/retsDBInfo.php';

        $mls = $_GET["mls"];
        $mls = 201610228;

        $conn = new mysqli(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }

        $query = "select ListingAgentNumber from AgentLookupByMLS where MLNumber = ".$mls;
        $result = $conn->query($query);

        $agId = '';
        if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                        $agId = $row['ListingAgentNumber'];
                }
        }
        $agName = "";
        $agNum = "";
        $agImage = "";
        $isFeatured = "";
        //echo '<pre style="color: blue;">Agent ID: '.$agId.'</pre>';
        $query = "select * from ActiveAgent_MEMB ";
        $query .= "LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber ";
        $query .= "where MemberNumber = ".$agId. " AND (Office_OFFI.featured = 1 || ActiveAgent_MEMB.featured = 1)";
        echo $query;
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
                echo "Is featured!!! ";
                while($row = $result->fetch_assoc()) {
                        $agName = $row['FullName'];
                        $agNum = $row['OfficeNumber'];
                        $agImage = $row['images'];
                }
        } else {
                echo "Agent not featured!!!!";
        }
        echo "name: ".$agName." num: ".$agNum." im: ".$agImage;
        mysqli_close($conn);



?>
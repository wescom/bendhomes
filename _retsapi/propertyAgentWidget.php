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
        //echo $query;
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
                echo "Is featured!!! ";
                while($row = $result->fetch_assoc()) {
                        $agName = $row['FullName'];
                        $agPageUrl = str_replace(' ', '-', $agName);
                        $agNum = $row['OfficeNumber'];
                        $agImage = $row['images'];
                }
                echo '<section class="agent-widget clearfix">';
                echo '<a class="agent-image" href="http://www.bendhomes.com/agent/?agent='.$agPageUrl.'&id='.$agId.'">';
                echo '<image src="http://www.bendhomes.com/_retsapi/imagesAgents/'.$agImage.'" />';
                echo '</a>';
        } else {
                echo '<div class="agent- company-featured-false position-sidebar">';
                echo '<div class="rail-button-agent-wrapper"><a href="/agents/" class="button">Find an Agent</a></div>';
                echo '</div>';
        }
        echo "name: ".$agName." num: ".$agNum." im: ".$agImage;
        mysqli_close($conn);



?>
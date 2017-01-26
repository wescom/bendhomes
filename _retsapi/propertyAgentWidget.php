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
        /*$query = "select ActiveAgent_MEMB.FullName,
                ActiveAgent_MEMB.MemberNumber,
                ActiveAgent_MEMB.IsActive,
                ActiveAgent_MEMB.images,
                Agent_MEMB.ContactAddlPhoneType1 as 'ContactAddlPhoneType_1',
                Agent_MEMB.ContactPhoneAreaCode1 as 'ContactPhoneAreaCode_1',
                Agent_MEMB.ContactPhoneNumber1 as 'ContactPhoneNumber_1',
                Agent_MEMB.ContactAddlPhoneType2 as 'ContactAddlPhoneType_2',
                Agent_MEMB.ContactPhoneAreaCode2 as 'ContactPhoneAreaCode_2',
                Agent_MEMB.ContactPhoneNumber2 as 'ContactPhoneNumber_2',
                Agent_MEMB.ContactAddlPhoneType3 as 'ContactAddlPhoneType_3',
                Agent_MEMB.ContactPhoneAreaCode3 as 'ContactPhoneAreaCode_3',
                Agent_MEMB.ContactPhoneNumber3 as 'ContactPhoneNumber_3',
                Office_OFFI.OfficeName,
                Office_OFFI.OfficePhoneComplete,
                Office_OFFI.StreetAddress,
                Office_OFFI.StreetCity,
                Office_OFFI.StreetState,
                Office_OFFI.StreetZipCode from ActiveAgent_MEMB ";
        $query .= "LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber ";
        $query .= "where MemberNumber = ".$agId. " AND (Office_OFFI.featured = 1 OR ActiveAgent_MEMB.featured = 1)";
*/
        $query = "
                        SELECT ActiveAgent_MEMB.FullName,
                        ActiveAgent_MEMB.MemberNumber,
                        ActiveAgent_MEMB.IsActive,
                        ActiveAgent_MEMB.images,
                        Agent_MEMB.ContactAddlPhoneType1 as 'ContactAddlPhoneType_1',
                        Agent_MEMB.ContactPhoneAreaCode1 as 'ContactPhoneAreaCode_1',
                        Agent_MEMB.ContactPhoneNumber1 as 'ContactPhoneNumber_1',
                        Agent_MEMB.ContactAddlPhoneType2 as 'ContactAddlPhoneType_2',
                        Agent_MEMB.ContactPhoneAreaCode2 as 'ContactPhoneAreaCode_2',
                        Agent_MEMB.ContactPhoneNumber2 as 'ContactPhoneNumber_2',
                        Agent_MEMB.ContactAddlPhoneType3 as 'ContactAddlPhoneType_3',
                        Agent_MEMB.ContactPhoneAreaCode3 as 'ContactPhoneAreaCode_3',
                        Agent_MEMB.ContactPhoneNumber3 as 'ContactPhoneNumber_3',
                        Office_OFFI.OfficeName,
                        Office_OFFI.OfficePhoneComplete,
                        Office_OFFI.StreetAddress,
                        Office_OFFI.StreetCity,
                        Office_OFFI.StreetState,
                        Office_OFFI.StreetZipCode
                        FROM ActiveAgent_MEMB
                        LEFT JOIN Agent_MEMB on ActiveAgent_MEMB.MemberNumber = Agent_MEMB.MemberNumber
                        LEFT JOIN Office_OFFI on ActiveAgent_MEMB.OfficeNumber = Office_OFFI.OfficeNumber
                        WHERE ActiveAgent_MEMB.OfficeNumber <> 99999 
                        AND (Office_OFFI.featured = 1 OR ActiveAgent_MEMB.featured = 1)
                        AND ActiveAgent_MEMB.MemberNumber = {$agId}
                ";

        echo $query;
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
                echo "Is featured!!! ";
                while($row = $result->fetch_assoc()) {
                        $agName = $row['FullName'];
                        $agPageUrl = str_replace(' ', '-', $agName);
                        $agNum = $row['OfficeNumber'];
                        $agImage = str_replace('png', 'jpg', $row['images']);
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
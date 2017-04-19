<?php

// File loaded from tbb-functions/tbb-functions.php in add_action('wp_footer', 'rets_footer_code') 
// Displays Agent Widget box on single property IDX pages.

include_once '/var/databaseIncludes/retsDBInfo.php';

$mls = $_GET["mls"];
//$mls = 201610228;

$home_url = 'http://www.bendhomes.com';

$conn = new mysqli(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

//$query = "select ListingAgentNumber from AgentLookupByMLS where MLNumber = ".$mls;
$query = "(select ListingAgentNumber from Property_BUSI where MLNumber = ".$mls.") UNION ";
$query .= "(select ListingAgentNumber from Property_COMM where MLNumber = ".$mls.") UNION ";
$query .= "(select ListingAgentNumber from Property_FARM where MLNumber = ".$mls.") UNION ";
$query .= "(select ListingAgentNumber from Property_LAND where MLNumber = ".$mls.") UNION ";
$query .= "(select ListingAgentNumber from Property_MULT where MLNumber = ".$mls.") UNION ";
$query .= "(select ListingAgentNumber from Property_RESI where MLNumber = ".$mls.")";

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

//echo $query;
$returnText = "";
$result = $conn->query($query);

// If the current property agent is featured show that person
if ($result->num_rows > 0) {
	
		$agFax = "";
		while($row = $result->fetch_assoc()) {
				$agName = $row['FullName'];
				$agPageUrl = str_replace(' ', '-', $agName);
				$agPageUrl = str_replace('--', '-', $agPageUrl);
				$agNum = $row['OfficePhoneComplete'];
				$image = $row['images'];
				$agImage = str_replace('png', 'jpg', $image);
				$agImage = $home_url."/_retsapi/imagesAgents/".$agImage;
				$agOfficeName = $row['OfficeName'];
				$agOfficePhone = $row['OfficePhoneComplete'];
				if ($row['ContactAddlPhoneType_1'] == 'Cellular'){
						$agCell = $row['ContactPhoneAreaCode_1']."-".$row['ContactPhoneNumber_1'];
				} elseif ($row['ContactAddlPhoneType_2'] == 'Cellular'){
						$agCell = $row['ContactPhoneAreaCode_2']."-".$row['ContactPhoneNumber_2'];
				} elseif ($row['ContactAddlPhoneType_1'] == 'Cellular'){
						$agCell = $row['ContactPhoneAreaCode_3']."-".$row['ContactPhoneNumber_3'];
				}
				if ($row['ContactAddlPhoneType_1'] == 'Fax'){
						$agFax = $row['ContactPhoneAreaCode_1']."-".$row['ContactPhoneNumber_1'];
				} elseif ($row['ContactAddlPhoneType_2'] == 'Fax'){
						$agFax = $row['ContactPhoneAreaCode_2']."-".$row['ContactPhoneNumber_2'];
				} elseif ($row['ContactAddlPhoneType_1'] == 'Fax'){
						$agFax = $row['ContactPhoneAreaCode_3']."-".$row['ContactPhoneNumber_3'];
				}
		}
	
		$agClass = $image == "" ? ' style="margin-left: 0;"' : '';
	
		$returnText = '<section class="rets-agent agent-widget clearfix">';
		$returnText .=  '<h3 class="title">Listing Agent:<div><strong><a href="'.$home_url.'/agent/?'.$agPageUrl.'&id='.$agId.'">'.$agName.'</a></strong></div></h3>';
		if( $image != "" ) {
			$returnText .= '<a class="agent-image" href="'.$home_url.'/agent/?agent='.$agPageUrl.'&id='.$agId.'">';
			$returnText .=  '<image src="'.$agImage.'" alt="'.$agName.' for '.$agOfficeName.'" />';
			$returnText .=  '</a>';
		}
		$returnText .=  '<div class="agent-info clearfix"'. $agClass .'>';
		$returnText .=  '<div class="agent-office-name">'.$agOfficeName.'</div>';
		$returnText .=  '<div class="contacts-list">';
		if ($agOfficePhone != "")
				$returnText .=  '<span class="office"><a href="tel:'.preg_replace("/[^0-9]/", "", $agOfficePhone).'">'.$agOfficePhone.'</a> (Office)</span>';
		if ($agCell != "")
				$returnText .=  '<span class="mobile"><a href="tel:'.preg_replace("/[^0-9]/", "", $agCell).'">'.$agCell.'</a> (Cell)</span>';
		if ($agFax != "")
				$returnText .=  '<span class="fax"><a href="tel:'.preg_replace("/[^0-9]/", "", $agFax).'">'.$agFax.'</a> (Fax)</span>';
		$returnText .=  '</div><!-- contacts-list -->';
		$returnText .=  '</div><!-- agent-info -->';
	
} else {
	
	$not_featured_query = "
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
			ORDER BY RAND()
			LIMIT 1
	";
	
	$result2 = $conn->query($not_featured_query);
	
	// Show a random featured agent instead if the current property agent is not featured.
	if ($result2->num_rows > 0) {
		$agFax2 = "";
		while($row2 = $result2->fetch_assoc()) {
				$agId2 = $row2['MemberNumber'];
				$agName2 = $row2['FullName'];
				$agPageUrl2 = str_replace(' ', '-', $agName2);
				$agPageUrl2 = str_replace('--', '-', $agPageUrl2);
				$agNum2 = $row2['OfficePhoneComplete'];
				$image2 = $row2['images'];
				$agImage2 = str_replace('png', 'jpg', $image2);
				$agImage2 = $home_url."/_retsapi/imagesAgents/".$agImage2;
				$agOfficeName2 = $row2['OfficeName'];
				$agOfficePhone2 = $row2['OfficePhoneComplete'];
				if ($row2['ContactAddlPhoneType_1'] == 'Cellular'){
						$agCell2 = $row2['ContactPhoneAreaCode_1']."-".$row2['ContactPhoneNumber_1'];
				} elseif ($row2['ContactAddlPhoneType_2'] == 'Cellular'){
						$agCell2 = $row2['ContactPhoneAreaCode_2']."-".$row2['ContactPhoneNumber_2'];
				} elseif ($row2['ContactAddlPhoneType_1'] == 'Cellular'){
						$agCell2 = $row2['ContactPhoneAreaCode_3']."-".$row2['ContactPhoneNumber_3'];
				}
				if ($row2['ContactAddlPhoneType_1'] == 'Fax'){
						$agFax2 = $row2['ContactPhoneAreaCode_1']."-".$row2['ContactPhoneNumber_1'];
				} elseif ($row2['ContactAddlPhoneType_2'] == 'Fax'){
						$agFax2 = $row2['ContactPhoneAreaCode_2']."-".$row2['ContactPhoneNumber_2'];
				} elseif ($row2['ContactAddlPhoneType_1'] == 'Fax'){
						$agFax2 = $row2['ContactPhoneAreaCode_3']."-".$row2['ContactPhoneNumber_3'];
				}
		}
		
		$agClass2 = $image2 == "" ? ' style="margin-left: 0;"' : '';

		$returnText = '<section class="rets-agent agent-widget clearfix">';
		$returnText .=  '<h3 class="title">Contact an Agent:<div><strong><a href="'.$home_url.'/agent/?'.$agPageUrl2.'&id='.$agId2.'">'.$agName2.'</a></strong></div></h3>';
		if( $image2 != "" ) {
			$returnText .= '<a class="agent-image" href="'.$home_url.'/agent/?agent='.$agPageUrl2.'&id='.$agId2.'">';
			$returnText .=  '<image src="'.$agImage2.'" alt="'.$agName2.' for '.$agOfficeName2.'" />';
			$returnText .=  '</a>';
		}
		$returnText .=  '<div class="agent-info clearfix"'. $agClass2 .'>';
		$returnText .=  '<div class="agent-office-name">'.$agOfficeName2.'</div>';
		$returnText .=  '<div class="contacts-list">';
		if ($agOfficePhone2 != "")
				$returnText .=  '<span class="office"><a href="tel:'.preg_replace("/[^0-9]/", "", $agOfficePhone2).'">'.$agOfficePhone2.'</a> (Office)</span>';
		if ($agCell2 != "")
				$returnText .=  '<span class="mobile"><a href="tel:'.preg_replace("/[^0-9]/", "", $agCell2).'">'.$agCell2.'</a> (Cell)</span>';
		if ($agFax2 != "")
				$returnText .=  '<span class="fax"><a href="tel:'.preg_replace("/[^0-9]/", "", $agFax2).'">'.$agFax2.'</a> (Fax)</span>';
		$returnText .=  '</div><!-- contacts-list -->';
		$returnText .=  '</div><!-- agent-info -->';
	
	} else {
	
		// As a last resort fallback if nothing is available.
		$returnText .=  '<div class="rets-agent position-sidebar">';
		$returnText .=  '<div class="rail-button-agent-wrapper"><a href="'.$home_url.'/agents/" class="button">Find an Agent</a></div>';
		$returnText .=  '</div>';
		
	}
	
}
mysqli_close($conn);

$returnText = str_replace('"', '\"', $returnText);
$returnText = str_replace('/', '\/', $returnText);

echo 'agentRender({"html":"'.$returnText.'"})';
<?php
// HTML page for Offices admin

/*include_once '/var/databaseIncludes/retsDBInfo.php';

$conn = new mysqli(RETSHOST, RETSUSERNAME, RETSPASSWORD, RETSDB);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}*/

$query = "
	SELECT Office_OFFI.OfficeName,
	Office_OFFI.OfficeDescription,
	Office_OFFI.DisplayName,
	Office_OFFI.featured,
	FROM Office_OFFI
	WHERE IsActive = 'T'
	ORDER BY OfficeName ASC
";

$rows = array();
$html = '';
$html .= $query;
$html .= '<h1>RETS Featured Offices Testing</h1>';
$html .= '<div class="company-wrap">

		<h2 class="nav-tab-wrapper" id="tbb-company-tabs">
			<a class="nav-tab nav-tab-active" id="tbb-company-tab" href="#top#company">Offices</a>
		</h2>

		<div id="sections">
			<section id="company" class="tbb-tab active">
				<form id="create-companies" method="post" action="'. admin_url( 'admin.php' ) .'" enctype="multipart/form-data">';

/*$result = $conn->query($query);

if ($result->num_rows > 0) {
	
	while( $row = mysqli_fetch_assoc($result) ) {
		$html .= '<div>'. $row['OfficeName'] .'</div>';
	}
	
} else {
	$html .= '';
}*/

$html .= '<p>
					<input type="hidden" name="action" value="companies_created" />
					<input id="company-submit" class="button-primary" type="submit" value="Update" />
				</p>
				</form>
			</section>
		</div>

</div>';

//mysqli_close($conn);
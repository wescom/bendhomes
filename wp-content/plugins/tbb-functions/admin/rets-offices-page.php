<?php
// HTML page for Offices admin

$query = "
	SELECT Office_OFFI.OfficeName,
	Office_OFFI.OfficeDescription,
	Office_OFFI.DisplayName,
	Office_OFFI.featured,
	FROM Office_OFFI
	WHERE IsActive = 'T'
	ORDER BY OfficeName ASC
";

$companies_query = new Rets_DB();
		
$companies = $companies_query->select( $query );

print_r( $companies);

?>

<h1>RETS Featured Offices</h1> 
            
<div class="company-wrap">

		<h2 class="nav-tab-wrapper" id="tbb-company-tabs">
			<a class="nav-tab nav-tab-active" id="tbb-company-tab" href="#top#company">Offices</a>
		</h2>

		<div id="sections">
			<section id="company" class="tbb-tab active">
				<form id="create-companies" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">

				<p>
					<input type="hidden" name="action" value="companies_created" />
					<input id="company-submit" class="button-primary" type="submit" value="Update" />
				</p>
				</form>
			</section>
		</div>

</div>
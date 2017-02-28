<?php

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
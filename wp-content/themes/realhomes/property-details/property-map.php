<?php  // Moved social sharing box to property-contents.php
$display_google_map = get_option('theme_display_google_map');
//$display_social_share = get_option('theme_display_social_share');
//if($display_google_map == 'true' || $display_social_share == 'true'){
if($display_google_map == 'true') {
	global $post;
	
	$property_address = get_post_meta($post->ID,'REAL_HOMES_property_address',true);
	$property_map_title = get_option('theme_property_map_title');
	?>

    <div class="map-wrap clearfix">
    
    	<?php
		if( !empty($property_map_title) ){
			?><span class="map-label"><?php echo $property_map_title; ?></span><?php
		}
		?>
        
        <div id="property_map"></div>
        
        <form id="map-directions-form" method="get" action="http://maps.google.com/maps" target="_blank">
            <input class="start-addr" type="text" name="saddr" placeholder="Enter Your Starting Address Here" />
            <input class="end-addr" type="hidden" name="daddr" value="<?php echo $property_address; ?>" />
            <input class="btn real-btn map-btn" type="submit" value="Get Directions" />
        </form>

    </div>

    <?php
}


/*  Unminified version of maps script.  Minified version located above.
<script>
	function initialize_property_map(){

	var propertyMarkerInfo = <?php echo json_encode( $property_marker ); ?>

	var url = propertyMarkerInfo.icon;
	var size = new google.maps.Size( 42, 57 );

	// retina
	if( window.devicePixelRatio > 1.5 ) {
		if ( propertyMarkerInfo.retinaIcon ) {
			url = propertyMarkerInfo.retinaIcon;
			size = new google.maps.Size( 83, 113 );
		}
	}

	var image = {
		url: url,
		size: size,
		scaledSize: new google.maps.Size( 42, 57 ),
		origin: new google.maps.Point( 0, 0 ),
		anchor: new google.maps.Point( 21, 56 )
	};

	var propertyLocation = new google.maps.LatLng( propertyMarkerInfo.lat, propertyMarkerInfo.lang );
	var propertyMapOptions = {
		center: propertyLocation,
		zoom: 15,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		scrollwheel: false
	};
	var propertyMap = new google.maps.Map(document.getElementById("property_map"), propertyMapOptions);
	var propertyMarker = new google.maps.Marker({
		position: propertyLocation,
		map: propertyMap,
		icon: image
	});
}

window.onload = initialize_property_map();
</script> */


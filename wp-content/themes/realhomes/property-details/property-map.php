<?php  // Moved social sharing box to property-contents.php
$display_google_map = get_option('theme_display_google_map');
//$display_social_share = get_option('theme_display_social_share');
//if($display_google_map == 'true' || $display_social_share == 'true'){
if($display_google_map == 'true'){
global $post; ?>

    <div class="map-wrap clearfix">
        
		<?php
        add_action('custom_footer_scripts', 'load_maps_script_in_footer');
        function load_maps_script_in_footer() {
            $property_location = get_post_meta($post->ID,'REAL_HOMES_property_location',true);
            // set a trap if we get zeroes for map lat long from MLS | 1777 JTG
            if($property_location == '0.000000,0.000000') {
              unset($property_location);
            }
            $property_address = get_post_meta($post->ID,'REAL_HOMES_property_address',true);
            $property_map = get_post_meta($post->ID,'REAL_HOMES_property_map',true);
        
            if( $property_address && !empty($property_location) && $display_google_map == 'true' && ( $property_map != 1 ) ) {
                $property_marker = array();
                $lat_lng = explode(',',$property_location);
                $property_marker['lat'] = $lat_lng[0];
                $property_marker['lang'] = $lat_lng[1];
        
                /* Property Map Icon Based on Property Type */
                $property_type_slug = 'single-family-home'; // Default Icon Slug
        
                $type_terms = get_the_terms( $post->ID,"property-type" );
                if( !empty( $type_terms ) ) {
                    foreach($type_terms as $typ_trm){
                        $property_type_slug = $typ_trm->slug;
                        break;
                    }
                }
        
                if( file_exists( get_template_directory().'/images/map/'.$property_type_slug.'-map-icon.png' ) ){
                    $property_marker['icon'] = get_template_directory_uri().'/images/map/'.$property_type_slug.'-map-icon.png';
                    // retina icon
                    if( file_exists( get_template_directory().'/images/map/'.$property_type_slug.'-map-icon@2x.png' ) ) {
                        $property_marker['retinaIcon'] = get_template_directory_uri().'/images/map/'.$property_type_slug.'-map-icon@2x.png';
                    }
                }else{
                    $property_marker['icon'] = get_template_directory_uri().'/images/map/single-family-home-map-icon.png';// default icon
                    $property_marker['retinaIcon'] = get_template_directory_uri().'/images/map/single-family-home-map-icon@2x.png';  // default retina icon
                }
        
        
                $property_map_title = get_option('theme_property_map_title');
                if( !empty($property_map_title) ){
                    ?><span class="map-label"><?php echo $property_map_title; ?></span><?php
                }
                ob_start(); ?>
                
                <div id="property_map"></div>
                
                <script type="application/javascript">
                function initialize_property_map(){var e=<?php echo json_encode( $property_marker ); ?>,o=e.icon,n=new google.maps.Size(42,57);window.devicePixelRatio>1.5&&e.retinaIcon&&(o=e.retinaIcon,n=new google.maps.Size(83,113));var a={url:o,size:n,scaledSize:new google.maps.Size(42,57),origin:new google.maps.Point(0,0),anchor:new google.maps.Point(21,56)},i=new google.maps.LatLng(e.lat,e.lang),p={center:i,zoom:15,mapTypeId:google.maps.MapTypeId.ROADMAP,scrollwheel:!1},g=new google.maps.Map(document.getElementById("property_map"),p);new google.maps.Marker({position:i,map:g,icon:a})}window.onload=initialize_property_map();
                </script>
                
                <form id="map-directions-form" method="get" action="http://maps.google.com/maps" target="_blank">
                    <input class="start-addr" type="text" name="saddr" placeholder="Enter Your Starting Address Here" />
                    <input class="end-addr" type="hidden" name="daddr" value="<?php echo $property_address; ?>" />
                    <input class="btn real-btn map-btn" type="submit" value="Get Directions" />
                </form>
                
            <?php
                $output = ob_get_clean();
            }
            
            echo $output; 
        } // end function ?>

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


<?php
$properties_for_map = array(
    'post_type' => 'property',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'REAL_HOMES_property_address',
            'compare' => 'EXISTS'
        )
    )
);

if( is_page_template('template-search.php') || is_page_template('template-search-sidebar.php') ){

    /* Apply Search Filter */
    $properties_for_map = apply_filters( 'real_homes_search_parameters', $properties_for_map );

}elseif(is_page_template('template-home.php')){

    /* Apply Homepage Properties Filter */
    $properties_for_map = apply_filters( 'real_homes_homepage_properties', $properties_for_map );

}elseif( is_page_template( array (
    'template-property-listing.php',
    'template-property-grid-listing.php',
    'template-map-based-listing.php',
    ) ) ) {

    // Apply pagination
    global $paged;
    if ( is_front_page()  ) {
        $paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
    }
    $properties_for_map['paged'] = $paged;

    // Apply properties filter settings from properties list templates
    $properties_for_map = apply_filters( 'inspiry_properties_filter', $properties_for_map );

    // Apply sorting
    $properties_for_map = sort_properties( $properties_for_map );

}elseif( is_tax() ) {

    global $wp_query;
    /* Taxonomy Query */
    $properties_for_map['tax_query'] = array(
		array(
			'taxonomy' => $wp_query->query_vars['taxonomy'],
			'field' => 'slug',
			'terms' => $wp_query->query_vars['term']
		)
	);

}

$properties_for_map_query = new WP_Query( $properties_for_map );

$total_count = $properties_for_map_query->found_posts;

//if( $total_count < 2000 || is_page_template('template-property-listing.php') ) {

	$properties_data = array();
	
	if ( $properties_for_map_query->have_posts() ) :
	
		while ( $properties_for_map_query->have_posts() ) :
	
			$properties_for_map_query->the_post();
	
			$current_prop_array = array();
	
			/* Property Title */
			$current_prop_array['title'] = get_the_title();
	
			/* Property Price */
			$current_prop_array['price'] = get_property_price();
	
			/* Property Location */
			$property_location = get_post_meta($post->ID,'REAL_HOMES_property_location',true);
			if($property_location == '0.000000,0.000000') {
			  unset($property_location);
			}
			if(!empty($property_location)){
				$lat_lng = explode(',',$property_location);
				$current_prop_array['lat'] = $lat_lng[0];
				$current_prop_array['lng'] = $lat_lng[1];
			}
	
			/* Property Thumbnail */
			if(has_post_thumbnail()){
				$image_id = get_post_thumbnail_id();
				$image_attributes = wp_get_attachment_image_src( $image_id, 'property-thumb-image' );
				if(!empty($image_attributes[0])){
					$current_prop_array['thumb'] = $image_attributes[0];
				}
			}
	
			/* Property Title */
			$current_prop_array['url'] = get_permalink();
	
			/* Property Map Icon Based on Property Type */
			$property_type_slug = 'single-family-home'; // Default Icon Slug
	
			$type_terms = get_the_terms( $post->ID,"property-type" );
			if(!empty($type_terms)){
				foreach($type_terms as $typ_trm){
					$property_type_slug = $typ_trm->slug;
					break;
				}
			}
	
	
			if( file_exists( get_template_directory().'/images/map/'.$property_type_slug.'-map-icon.png' ) ) {
				$current_prop_array['icon'] = get_template_directory_uri().'/images/map/'.$property_type_slug.'-map-icon.png';
				// retina icon
				if( file_exists( get_template_directory().'/images/map/'.$property_type_slug.'-map-icon@2x.png' ) ) {
					$current_prop_array['retinaIcon'] = get_template_directory_uri().'/images/map/'.$property_type_slug.'-map-icon@2x.png';
				}
	
			} else {
				$current_prop_array['icon'] = get_template_directory_uri().'/images/map/single-family-home-map-icon.png';   // default icon
				$current_prop_array['retinaIcon'] = get_template_directory_uri().'/images/map/single-family-home-map-icon@2x.png';  // default retina icon
			}
	
			$properties_data[] = $current_prop_array;
	
		endwhile;
		wp_reset_query();
		?>
        
        <script type="text/javascript">
		function initializePropertiesMap(){function e(e,o,n){google.maps.event.addListener(o,"click",function(){var i=Math.pow(2,e.getZoom()),t=100/i||0,a=e.getProjection(),l=o.getPosition(),s=a.fromLatLngToPoint(l),r=new google.maps.Point(s.x,s.y-t),g=a.fromPointToLatLng(r);e.setCenter(g),n.open(e,o)})}for(var o=<?php echo json_encode( $properties_data ); ?>,n=(new google.maps.LatLng(o[0].lat,o[0].lng),{zoom:12,maxZoom:16,scrollwheel:!1}),i=new google.maps.Map(document.getElementById("listing-map"),n),t=new google.maps.LatLngBounds,a=new Array,l=(new Array,0);l<o.length;l++){var s=o[l].icon,r=new google.maps.Size(42,57);window.devicePixelRatio>1.5&&o[l].retinaIcon&&(s=o[l].retinaIcon,r=new google.maps.Size(83,113));var g={url:s,size:r,scaledSize:new google.maps.Size(42,57),origin:new google.maps.Point(0,0),anchor:new google.maps.Point(21,56)};a[l]=new google.maps.Marker({position:new google.maps.LatLng(o[l].lat,o[l].lng),map:i,icon:g,title:o[l].title,animation:google.maps.Animation.DROP,visible:!0}),t.extend(a[l].getPosition());var p=document.createElement("div");p.className="map-info-window";var m="";o[l].thumb&&(m+='<a class="thumb-link" href="'+o[l].url+'"><img class="prop-thumb" src="'+o[l].thumb+'" alt="'+o[l].title+'"/></a>'),m+='<h5 class="prop-title"><a class="title-link" href="'+o[l].url+'">'+o[l].title+"</a></h5>",o[l].price&&(m+='<p><span class="price">'+o[l].price+"</span></p>"),m+='<div class="arrow-down"></div>',p.innerHTML=m;var c={content:p,disableAutoPan:!0,maxWidth:0,alignBottom:!0,pixelOffset:new google.maps.Size(-122,-48),zIndex:null,closeBoxMargin:"0 0 -16px -16px",closeBoxURL:"<?php echo get_template_directory_uri() . '/images/map/close.png'; ?>",infoBoxClearance:new google.maps.Size(1,1),isHidden:!1,pane:"floatPane",enableEventPropagation:!1},d=new InfoBox(c);e(i,a[l],d)}i.fitBounds(t);var w={ignoreHidden:!0,maxZoom:14,styles:[{textColor:"#ffffff",url:"<?php echo get_template_directory_uri() . '/images/map/cluster-icon.png'; ?>",height:48,width:48}]};new MarkerClusterer(i,a,w)}google.maps.event.addDomListener(window,"load",initializePropertiesMap);
		</script>
	
		<div id="map-head">
			<div id="listing-map"></div>
		</div>
		<!-- End Map Head -->
	
		<?php
	else:
		if(is_tax()){
			get_template_part('banners/taxonomy_page_banner');
		}else{
			get_template_part('banners/default_page_banner');
		}
	endif;

/*}  else { // end if total count < 2000 ?>

	<div class="container">
		<div style="text-align: center; padding: 15px 0 80px;">
        	<h2 style="color: #971D20;">Oops. Your search was to large.</h2>
            <h4 style="color: #971D20; max-width: 900px; margin: 0 auto; line-height: 150%;">There was over 2000 results in your search.<br>Please narrow your criteria and try again. We recommend choosing a Min and Max price to narrow results.</h4>
        </div>
	</div>

<?php }*/


/* Uniminied script used above. 
<script type="text/javascript">
function initializePropertiesMap() {

	// Properties Array
	var properties = <?php echo json_encode( $properties_data ); ?>

	// Map Center Location - From Theme Options
	var location_center = new google.maps.LatLng(properties[0].lat,properties[0].lng);

	var mapOptions = {
		zoom: 12,
		maxZoom: 16,
		scrollwheel: false
	}

	var map = new google.maps.Map(document.getElementById("listing-map"), mapOptions);

	var bounds = new google.maps.LatLngBounds();

	// Loop to generate marker and infowindow based on properties array
	var markers = new Array();
	var info_windows = new Array();

	for (var i=0; i < properties.length; i++) {

		var url = properties[i].icon;
		var size = new google.maps.Size( 42, 57 );
		if( window.devicePixelRatio > 1.5 ) {
			if ( properties[i].retinaIcon ) {
				url = properties[i].retinaIcon;
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

		markers[i] = new google.maps.Marker({
			position: new google.maps.LatLng(properties[i].lat,properties[i].lng),
			map: map,
			// icon: properties[i].icon,
			icon: image,
			title: properties[i].title,
			animation: google.maps.Animation.DROP,
			visible: true
		});

		bounds.extend(markers[i].getPosition());

		var boxText = document.createElement("div");
		boxText.className = 'map-info-window';

		var innerHTML = "";
		if ( properties[i].thumb ) {
			innerHTML += '<a class="thumb-link" href="' + properties[i].url + '">' +
						'<img class="prop-thumb" src="' + properties[i].thumb + '" alt="' + properties[i].title + '"/>' +
						'</a>';
		}

		innerHTML += '<h5 class="prop-title"><a class="title-link" href="' + properties[i].url + '">' + properties[i].title + '</a></h5>';

		if ( properties[i].price ) {
			innerHTML += '<p><span class="price">' + properties[i].price + '</span></p>';
		}

		innerHTML += '<div class="arrow-down"></div>';

		boxText.innerHTML = innerHTML;


		var myOptions = {
			content: boxText,
			disableAutoPan: true,
			maxWidth: 0,
			alignBottom: true,
			pixelOffset: new google.maps.Size( -122, -48 ),
			zIndex: null,
			closeBoxMargin: "0 0 -16px -16px",
			closeBoxURL: "<?php echo get_template_directory_uri() . '/images/map/close.png'; ?>",
			infoBoxClearance: new google.maps.Size( 1, 1 ),
			isHidden: false,
			pane: "floatPane",
			enableEventPropagation: false
		};

		var ib = new InfoBox( myOptions );

		attachInfoBoxToMarker( map, markers[i], ib );
	}

	map.fitBounds(bounds);

	// Marker Clusters
	var markerClustererOptions = {
		ignoreHidden: true,
		maxZoom: 14,
		styles: [{
			textColor: '#ffffff',
			url: "<?php echo get_template_directory_uri() . '/images/map/cluster-icon.png'; ?>",
			height: 48,
			width: 48
		}]
	};

	var markerClusterer = new MarkerClusterer( map, markers, markerClustererOptions );

	function attachInfoBoxToMarker( map, marker, infoBox ){
		google.maps.event.addListener( marker, 'click', function(){
			var scale = Math.pow( 2, map.getZoom() );
			var offsety = ( (100/scale) || 0 );
			var projection = map.getProjection();
			var markerPosition = marker.getPosition();
			var markerScreenPosition = projection.fromLatLngToPoint( markerPosition );
			var pointHalfScreenAbove = new google.maps.Point( markerScreenPosition.x, markerScreenPosition.y - offsety );
			var aboveMarkerLatLng = projection.fromPointToLatLng( pointHalfScreenAbove );
			map.setCenter( aboveMarkerLatLng );
			infoBox.open( map, marker );
		});
	}

}

google.maps.event.addDomListener(window,"load",initializePropertiesMap);

</script>
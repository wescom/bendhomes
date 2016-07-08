<?php  // Moved social sharing box to property-contents.php
$display_google_map = get_option('theme_display_google_map');
//$display_social_share = get_option('theme_display_social_share');
//if($display_google_map == 'true' || $display_social_share == 'true'){
if($display_google_map == 'true'){
global $post;

    ?>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAA8EncSAhKkbYnf0txTDxwGxTFgP_vIm-2wYPBky6y1LAhjOnrGRSb-EbA2u31k_ZTl9y6o9tLpImoHw" type="text/javascript"></script>
    <script src="http://www.wrightcontracting.com/jquery.gMap.js" type="text/javascript"></script>
    <script src="http://www.wrightcontracting.com/jquery.gps.js" type="text/javascript"></script>

    <div class="map-wrap clearfix">
        <?php
            $property_location = get_post_meta($post->ID,'REAL_HOMES_property_location',true);
            // set a trap if we get zeroes for map lat long from MLS | 1777 JTG
            if($property_location == '0.000000,0.000000') {
              unset($property_location);
            }
            $property_address = get_post_meta($post->ID,'REAL_HOMES_property_address',true);
            $property_map = get_post_meta($post->ID,'REAL_HOMES_property_map',true);

            if( $property_address && !empty($property_location) && $display_google_map == 'true' && ( $property_map != 1 ) )
            {
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
                ?>
                
                <div class="embedMap">
					<script type="text/javascript">
                        $(function(){
                            $("#map_canvas").googleMap({
                                zoomLevel: 15,
                                center: '<?php echo $property_address; ?>',
                                tooltip: true,
                                image: '<?php echo $property_marker['icon']; ?>',
                                imagewidth: 40,
                                imageheight: 33
                            }).load();
                        });
                    </script>
                    
                    <div id="map_canvas" style="width:350px; height:280px;"></div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="directions_form">
                        <p><strong>Get Directions:</strong></p>
                        <li><label>From: </label><input id="start" type="text" size="38" /></li>
                        <li><label>To: </label><input id="end" type="text" readonly="readonly" size="38" value="<?php echo $property_address; ?>" /></li>
                        <input name="submit" id="getdirections" type="submit" value="Get Directions" />
                    </form>
                    
                    <div id="directions"></div>
                </div>
                
                <?php /*<div id="property_map"></div>
                <script>
                    // Property Detail Page - Google Map for Property Location

                    <?php // Unminified JS. Minified js located below
					
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

                    window.onload = initialize_property_map(); ?>
					
					function initialize_property_map(){var e=<?php echo json_encode( $property_marker ); ?>,o=e.icon,n=new google.maps.Size(42,57);window.devicePixelRatio>1.5&&e.retinaIcon&&(o=e.retinaIcon,n=new google.maps.Size(83,113));var a={url:o,size:n,scaledSize:new google.maps.Size(42,57),origin:new google.maps.Point(0,0),anchor:new google.maps.Point(21,56)},i=new google.maps.LatLng(e.lat,e.lang),p={center:i,zoom:15,mapTypeId:google.maps.MapTypeId.ROADMAP,scrollwheel:!1},g=new google.maps.Map(document.getElementById("property_map"),p);new google.maps.Marker({position:i,map:g,icon:a})}window.onload=initialize_property_map();
                </script>
                
                <form id="directions-form" method="post" action="">
                    Enter your starting address:
                    <input type="text" id="map-start" placeholder="Starting Address" />
                    <input type="text" id="map-end" value="<?php echo $property_address; ?>" readonly="readonly" />
                    <input type="submit" id="map-submit" value="Get Directions" />
                </form>
                
                <div id="print-directions"></div>

                <?php */ // end commented original code
            }

            /*if ( $display_social_share == 'true' ) {
                ?>
                <div class="share-networks clearfix">
                    <span class="share-label"><?php _e('Share this', 'framework'); ?></span>
                    <span><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"><i class="fa fa-facebook fa-lg"></i><?php _e('Facebook','framework'); ?></a></span>
                    <span><a target="_blank" href="https://twitter.com/share?url=<?php the_permalink(); ?>" ><i class="fa fa-twitter fa-lg"></i><?php _e('Twitter','framework'); ?></a></span>
                    <span><a target="_blank" href="https://plus.google.com/share?url={<?php the_permalink(); ?>}" onclick="javascript:window.open(this.href,  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes')"><i class="fa fa-google-plus fa-lg"></i><?php _e('Google','framework'); ?></a></span>
                </div>
                <?php
            }*/
        ?>

    </div>

    <?php
}
?>

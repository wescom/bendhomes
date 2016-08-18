<?php // Map Modal ?>

<script type="text/javascript">
function initializePropertiesMap(){function e(e,o,n){google.maps.event.addListener(o,"click",function(){var i=Math.pow(2,e.getZoom()),t=100/i||0,a=e.getProjection(),l=o.getPosition(),s=a.fromLatLngToPoint(l),r=new google.maps.Point(s.x,s.y-t),g=a.fromPointToLatLng(r);e.setCenter(g),n.open(e,o)})}for(var o=<?php echo json_encode( $properties_data ); ?>,n=(new google.maps.LatLng(o[0].lat,o[0].lng),{zoom:12,maxZoom:16,scrollwheel:!1}),i=new google.maps.Map(document.getElementById("listing-map"),n),t=new google.maps.LatLngBounds,a=new Array,l=(new Array,0);l<o.length;l++){var s=o[l].icon,r=new google.maps.Size(42,57);window.devicePixelRatio>1.5&&o[l].retinaIcon&&(s=o[l].retinaIcon,r=new google.maps.Size(83,113));var g={url:s,size:r,scaledSize:new google.maps.Size(42,57),origin:new google.maps.Point(0,0),anchor:new google.maps.Point(21,56)};a[l]=new google.maps.Marker({position:new google.maps.LatLng(o[l].lat,o[l].lng),map:i,icon:g,title:o[l].title,animation:google.maps.Animation.DROP,visible:!0}),t.extend(a[l].getPosition());var p=document.createElement("div");p.className="map-info-window";var m="";o[l].thumb&&(m+='<a class="thumb-link" href="'+o[l].url+'"><img class="prop-thumb" src="'+o[l].thumb+'" alt="'+o[l].title+'"/></a>'),m+='<h5 class="prop-title"><a class="title-link" href="'+o[l].url+'">'+o[l].title+"</a></h5>",o[l].price&&(m+='<p><span class="price">'+o[l].price+"</span></p>"),m+='<div class="arrow-down"></div>',p.innerHTML=m;var c={content:p,disableAutoPan:!0,maxWidth:0,alignBottom:!0,pixelOffset:new google.maps.Size(-122,-48),zIndex:null,closeBoxMargin:"0 0 -16px -16px",closeBoxURL:"<?php echo get_template_directory_uri() . '/images/map/close.png'; ?>",infoBoxClearance:new google.maps.Size(1,1),isHidden:!1,pane:"floatPane",enableEventPropagation:!1},d=new InfoBox(c);e(i,a[l],d)}i.fitBounds(t);var w={ignoreHidden:!0,maxZoom:14,styles:[{textColor:"#ffffff",url:"<?php echo get_template_directory_uri() . '/images/map/cluster-icon.png'; ?>",height:48,width:48}]};new MarkerClusterer(i,a,w)}
</script>

<!-- Map Modal -->
<div id="map-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="mapModal" aria-hidden="true">
	<div class="modal-scrollable">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
            
            <div id="listing-map"></div>
            
        </div>
    </div>
</div>
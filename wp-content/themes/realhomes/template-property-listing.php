<?php
/*
*   Template Name: Property Listing Template
*/
get_header();

/* Theme Listing Page Module */
$theme_listing_module = get_option('theme_listing_module');

/* Only for demo purpose only */
if(isset($_GET['module'])){
    $theme_listing_module = $_GET['module'];
}

switch($theme_listing_module){
    case 'properties-map':
        get_template_part('banners/map_based_banner');
        break;

    default:
        get_template_part('banners/default_page_banner');
        break;
}
?>

    <!-- Content -->
    <?php
    if(isset($_GET['view'])){
        $view_type = $_GET['view'];
    }else{
        /* Theme Options Listing Layout */
        $view_type = get_option('theme_listing_layout');
    }
	
	switch( $view_type ) {
		case 'grid':
			get_template_part("template-parts/grid-listing-container");
			break;
			
		case 'map':
			get_template_part("bend-homes/template-parts/map-listing-container");
			break;
			
		default:
			get_template_part("template-parts/listing-container");
			break;
	}
    ?>
    <!-- End Content -->

<?php get_footer(); ?>

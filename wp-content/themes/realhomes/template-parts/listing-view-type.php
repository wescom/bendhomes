<div class="view-type clearfix">
    <?php

    //page url
    /*if( is_tax() ) {
        $page_url = custom_taxonomy_page_url();
    } else {
        global $post;
        $page_url = get_permalink( $post->ID );
    }

    //separator
    $separator = ( parse_url( $page_url, PHP_URL_QUERY ) == NULL ) ? '?' : '&';*/

    // View Type
	if(isset($_GET['view'])){
        $view_type = $_GET['view'];
    }else{
        /* Theme Options Listing Layout */
        $view_type = get_option('theme_listing_layout');
    }
    ?>
    <span>View Style:</span>
    <a class="map <?php echo ( $view_type == 'map' )?'active':''; ?>" href="<?php echo tbb_current_url( 'view=map' ); ?>" data-toggle="tooltip" title="Map View">
    	<i class="fa fa-map-marker"></i>
    </a>
    <a class="list <?php echo ( $view_type == 'list' )?'active':''; ?>" href="<?php echo tbb_current_url( 'view=list' ); ?>" data-toggle="tooltip" title="List View">
        <i class="fa fa-list"></i>
    </a>
    <a class="grid <?php echo ( $view_type == 'grid' )?'active':''; ?>" href="<?php echo tbb_current_url( 'view=grid' ); ?>" data-toggle="tooltip" title="Grid View">
        <i class="fa fa-th"></i>
    </a>
</div>
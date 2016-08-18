<div class="view-type clearfix">
    <?php

    //page url
    if( is_tax() ) {
        $page_url = custom_taxonomy_page_url();
    } else {
        global $post;
        $page_url = get_permalink( $post->ID );
    }

    //separator
    $separator = ( parse_url( $page_url, PHP_URL_QUERY ) == NULL ) ? '?' : '&';

    // View Type
	if(isset($_GET['view'])){
        $view_type = $_GET['view'];
    }else{
        /* Theme Options Listing Layout */
        $view_type = get_option('theme_listing_layout');
    }
    ?>
    <a class="map <?php echo ( $view_type == 'map' )?'active':''; ?>" href="<?php echo $page_url . $separator . 'view=map'; ?>">
    	<i class="fa fa-map-marker"></i>
    </a>
    <a class="list <?php echo ( $view_type == 'list' )?'active':''; ?>" href="<?php echo $page_url . $separator . 'view=list'; ?>">
        <i class="fa fa-list"></i>
    </a>
    <a class="grid <?php echo ( $view_type == 'grid' )?'active':''; ?>" href="<?php echo $page_url . $separator . 'view=grid'; ?>">
        <i class="fa fa-th"></i>
    </a>
</div>
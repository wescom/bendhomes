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
    $view_type = 'list';
    if( isset( $_GET['view'] ) ) {
        if ( $_GET['view'] == 'grid' ) {
            $view_type = 'grid';
        }
    } else {
        if( is_page_template( 'template-property-grid-listing.php' ) ) {
            $view_type = 'grid';
        }
    }
    ?>
    <a class="map <?php echo ( $view_type == 'map' )?'active':''; ?>" data-toggle="modal" href="#map-modal">
    	<i class="fa fa-map-marker"></i>
    </a>
    <a class="list <?php echo ( $view_type == 'list' )?'active':''; ?>" href="<?php echo $page_url . $separator . 'view=list'; ?>">
        <i class="fa fa-list"></i>
    </a>
    <a class="grid <?php echo ( $view_type == 'grid' )?'active':''; ?>" href="<?php echo $page_url . $separator . 'view=grid'; ?>">
        <i class="fa fa-th"></i>
    </a>
</div>
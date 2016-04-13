<div class="span3 sidebar-wrap">

    <!-- Sidebar -->
    <aside class="sidebar">
        <?php
        if ( ! dynamic_sidebar( 'property-sidebar' ) ) :
        endif;
        get_template_part( 'template-parts/rail-ad' );
        ?>
    </aside><!-- End Sidebar -->

</div>

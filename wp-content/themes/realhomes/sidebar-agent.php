<div class="span3 sidebar-wrap">

    <!-- Sidebar -->
    <aside class="sidebar">
        <?php
        if ( ! dynamic_sidebar ( 'agent-sidebar' ) ) :
        endif;
        ?>
        <div class="ad-wrapper">
          <div class="container">
            <?php // 1777ad
              do_action('dfp_ad_spot','siderail'); 
            ?>
          </div>
        </div>
    </aside>
    <!-- End Sidebar -->

</div>

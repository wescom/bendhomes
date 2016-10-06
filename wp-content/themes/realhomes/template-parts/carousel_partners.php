<?php
$show_partners = get_option('theme_show_partners');

if($show_partners == 'true'){
    ?>
    <div class="container page-carousel">
        <div class="row-fluid">
            <div class="span12">
                <section class="brands-carousel clearfix">
                    <h3><span><?php echo $partners_title = get_option('theme_partners_title'); ?></span></h3>
                            <div class="row-fluid clearfix">
                                
                                <div class="span2 partner"><?php do_action('dfp_ad_spot','partners1'); ?></div>
                                
                                <div class="span2 partner"><?php do_action('dfp_ad_spot','partners2'); ?></div>
                                
                                <div class="span2 partner"><?php do_action('dfp_ad_spot','partners3'); ?></div>
                                
                                <div class="span2 partner"><?php do_action('dfp_ad_spot','partners4'); ?></div>
                                
                                <div class="span2 partner"><?php do_action('dfp_ad_spot','partners5'); ?></div>
                                
                            </div>
                </section>
            </div>
        </div>
    </div>

	<?php
}

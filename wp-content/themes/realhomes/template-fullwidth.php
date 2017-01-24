<?php
/*
*  Template Name: Full Width Template
*/

get_header();
?>
    
<!--div class="simple-search-wrap">
	<div class="container">
		<div class="row">
			<?php
			//get_template_part('bend-homes/simple-search');
			?>
		</div>
	</div>
</div-->

    <!-- Page Head -->
    <?php //get_template_part("banners/default_page_banner"); ?>
    
    <?php if( is_front_page() ) { ?>
    <div class="page-head" style="text-align: center; position: relative;">
    	<img src="<?php echo home_url(); ?>/wp-content/uploads/2013/08/old-mill-district-bend-oregon.jpg" alt="" width="1600" height="500" />
    	<div style="position: absolute; top: 100px; width: 100%;">
			<div class="container">
				<div class="wrap clearfix">
					<h1 class="page-title">Welcome to BendHomes.com</h1>
					<?php echo do_shortcode('[idx-platinum-widget id="20817-34207" ]'); ?>
				</div>
			</div>
		</div>
	</div>
    <?php } ?>

    <!-- Content -->
    <div class="container contents single">
        <div class="row">
            <div class="span12 main-wrap">
                <!-- Main Content -->
                <div class="main">

                    <div class="inner-wrapper">
                        <?php
                        if ( have_posts() ) :
                            while ( have_posts() ) :
                                the_post();
                                ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class("clearfix"); ?>>
                                        <?php

                                        the_content();

                                        // WordPress Link Pages
                                        wp_link_pages(array('before' => '<div class="pages-nav clearfix">', 'after' => '</div>', 'next_or_number' => 'next'));
                                        ?>
                                </article>
                                <?php
                            endwhile;
                            //comments_template();
                        endif;
                        ?>
                    </div>

                </div><!-- End Main Content -->

            </div> <!-- End span12 -->

        </div><!-- End contents row -->

    </div><!-- End Content -->

<?php get_footer(); ?>
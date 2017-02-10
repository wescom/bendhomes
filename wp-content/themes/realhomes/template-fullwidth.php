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
    
    <?php 
	if( is_front_page() ) {
		if( current_user_can('administrator')) {
			
			echo '<h1>Welcome to Bendhomes.com</h1>';
			
		} else { // Display current header area if not admin while I'm working on a new crappy banner above. ?>
			
			<div class="page-head">
				<div class="banner-search-wrap">
					<div class="container">
						<div class="clearfix">
							<h1 class="page-title">Welcome to BendHomes.com</h1>
							<div class="header-search"><?php echo do_shortcode('[idx-omnibar styles="1" extra="0" min_price="1" ]'); ?></div>
						</div>
					</div>
				</div>
			</div>
			
		<?php }
	} 
	?>

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
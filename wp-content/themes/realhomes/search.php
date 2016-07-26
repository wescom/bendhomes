<?php
    get_header();

    $banner_image_path = get_default_banner();
?>
    <?php /* <div class="page-head" style="background-repeat: no-repeat;background-position: center top;background-image: url('<?php echo $banner_image_path; ?>'); ">
 */ ?>
    <div class="page-head">
        <div class="container">
            <div class="wrap clearfix">
                <h1 class="pagetitle">Search Result for 
				<?php /* Search Count */
				$post_type = $_GET['post_type'];
				$allsearch = &new WP_Query("s=$s&post_type=$post_type&showposts=-1"); 
				$key = wp_specialchars($s, 1); 
				$count = $allsearch->post_count; _e(''); _e('<span class="search-terms">'); echo $key; _e('</span>'); _e(' &mdash; '); echo $count . ' '; _e('articles'); 
				wp_reset_query(); 
				?>
                </h1>
            </div>
        </div>
    </div><!-- End Page Head -->

    <!-- Content -->
    <div class="container contents blog-page">
        <div class="row">
            <div class="span9 main-wrap">
                <!-- Main Content -->
                <div class="main">

                    <div class="inner-wrapper">
                        <?php  get_template_part("loop");  ?>
                    </div>

                </div><!-- End Main Content -->

            </div> <!-- End span9 -->

            <?php get_sidebar(); ?>

        </div><!-- End contents row -->
    </div><!-- End Content -->

<?php get_footer(); ?>

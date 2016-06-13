<?php
get_header();

$banner_image_path = get_default_banner();

$banner_title = __('404 - Page Not Found!', 'framework');
$banner_details = __('The page you are looking for is not here!', 'framework');
?>
    <?php /* <div class="page-head" style="background-repeat: no-repeat;background-position: center top;background-image: url('<?php echo $banner_image_path; ?>'); ">
 */ ?>

    <div class="page-head">
        <div class="container">
            <div class="wrap clearfix">
                <h1 class="page-title"><?php echo $banner_title .' | '. $banner_details; ?></h1>
            </div>
        </div>
    </div><!-- End Page Head -->

    <!-- Content -->
    <div class="container contents single">
        <div class="row">
            <div class="span9 main-wrap">

                <!-- Main Content -->
                <div class="main">

                    <div class="inner-wrapper">
                        <article class="page-404">
                            <p><br><strong><?php _e('Please try using top navigation OR search for what you are looking for!', 'framework'); ?></strong></p>
                        </article>
                    </div>

                </div><!-- End Main Content -->

            </div> <!-- End span9 -->

            <?php get_sidebar(); ?>

        </div><!-- End contents row -->

    </div><!-- End Content -->

<?php get_footer(); ?>

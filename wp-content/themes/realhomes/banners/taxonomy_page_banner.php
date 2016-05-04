<?php
    // Banner Image
    $banner_image_path = get_default_banner();

    // Banner Title
    $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
    $banner_title = $current_term->name;

    // Banner Sub Title
    $banner_sub_title = $current_term->description;
    ?>
    <?php /* <div class="page-head" style="background-repeat: no-repeat;background-position: center top;background-image: url('<?php echo $banner_image_path; ?>'); background-size: cover; "> */ ?>
    <div class="page-head">
        <?php if(!('true' == get_option('theme_banner_titles'))): ?>
        <div class="container">
            <div class="wrap clearfix">
                <h1 class="page-title"><?php echo $banner_title .' | '. $banner_sub_title; ?></h1>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- End Page Head -->

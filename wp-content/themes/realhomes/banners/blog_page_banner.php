<?php
    $banner_title = get_option('theme_news_banner_title');
    $banner_title = empty($banner_title)?__('News','framework'):$banner_title;

    $banner_sub_title = get_option('theme_news_banner_sub_title');
    $banner_sub_title = empty($banner_sub_title)?__('Check out market updates','framework'):$banner_sub_title;

    $banner_image_path = "";

    /* If posts page is set in Reading Settings */
    $page_for_posts = get_option('page_for_posts');
    if($page_for_posts){
        $banner_image_id = get_post_meta( $page_for_posts, 'REAL_HOMES_page_banner_image', true );
        if($banner_image_id){
            $banner_image_path = wp_get_attachment_url($banner_image_id);
        }else{
            $banner_image_path = get_default_banner();
        }
    }else{
        $banner_image_path = get_default_banner();
    }
?>
    <?php /* <div class="page-head" style="background-repeat: no-repeat;background-position: center top;background-image: url('<?php echo $banner_image_path; ?>'); background-size: cover; "> */ ?>
    <div class="page-head">
        <?php if(!('true' == get_option('theme_banner_titles'))): ?>
        <div class="container">
            <div class="wrap clearfix">
              <h1 class="page-title">yy<?php echo $banner_title .' | '. $banner_details; ?></h1>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- End Page Head -->

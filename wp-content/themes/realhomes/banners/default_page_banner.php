<?php
    $rev_slider_alias = get_post_meta($post->ID,'REAL_HOMES_rev_slider_alias',true);
    if( function_exists('putRevSlider') && (!empty($rev_slider_alias)) ){
        putRevSlider( $rev_slider_alias );
    }else{

    // Banner Image
    $banner_image_path = "";
    $banner_image_id = get_post_meta( $post->ID, 'REAL_HOMES_page_banner_image', true );
    if($banner_image_id){
        $banner_image_path = wp_get_attachment_url($banner_image_id);
    }else{
        $banner_image_path = get_default_banner();
    }

    // Banner Title
    $banner_title = get_post_meta( $post->ID, 'REAL_HOMES_banner_title', true );
    if(empty($banner_title)){
        $banner_title = get_the_title($post->ID);
    }

    // Banner Sub Title
    $banner_sub_title = get_post_meta( $post->ID, 'REAL_HOMES_banner_sub_title', true );

    ?>
    <?php /* <div class="page-head" style="background-repeat: no-repeat;background-position: center top;background-image: url('<?php echo $banner_image_path; ?>'); background-size: cover; ">
*/ ?>
    <div class="page-head">
        <?php if(!('true' == get_option('theme_banner_titles'))): ?>
        <div class="container">
            <div class="wrap clearfix">
                <h1 class="page-title"><?php
                  echo $banner_title;
				  if(isset($_GET['type'])) $banner_sub_title = ucfirst($_GET['type']);
                  if(!empty($banner_sub_title)) {
                    echo ' | ';
                    echo $banner_sub_title;
                  }
                  ?></h1>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- End Page Head -->
    <?php

    }
?>

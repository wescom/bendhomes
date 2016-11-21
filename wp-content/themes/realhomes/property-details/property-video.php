<?php
$display_video = get_option('theme_display_video');
if($display_video == 'true'){
$tour_video_url = get_post_meta($post->ID, 'REAL_HOMES_tour_video_url', true );
$tour_video_image_id = get_post_meta( $post->ID, 'REAL_HOMES_tour_video_image', true );
$tour_video_image_src = wp_get_attachment_image_src($tour_video_image_id,'property-detail-slider-image-two');
$tour_video_image = $tour_video_image_src[0];

    if( !empty($tour_video_image) && !empty($tour_video_url) ) {
        ?>
        <div class="property-video">
            <a href="<?php echo $tour_video_url; ?>" class="property-video-link" target="_blank" title="Video">
                <div class="play-btn"></div>
                <?php echo '<img src="'.$tour_video_image.'" alt="'.get_the_title($post->ID).'">'; ?>
            </a>
        </div>
        <?php
    }
}
?>
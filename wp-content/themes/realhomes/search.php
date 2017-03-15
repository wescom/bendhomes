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
				$allsearch = new WP_Query("s=$s&post_type=$post_type&showposts=-1"); 
				$key = wp_specialchars($s, 1); 
				$count = $allsearch->post_count; _e(''); _e('<span class="search-terms">"'); echo $key; _e('"</span>'); _e(' &mdash; '); echo $count . ' '; _e('found'); 
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

                    <div class="inner-wrapper clearfix" style="margin-bottom: 0;">
                    	<?php 
						if( $post_type == 'agent' || $post_type == 'company' ) {
							
							$find_text = $post_type == 'agent' ? 'Find an '. $post_type : 'Find a '. $post_type;
							
							$output = '<div class="custom-search-wrap">';
								$output .= '
									<form role="search" action="'. site_url('/') .'" method="get" id="searchform">
										<input type="text" class="search-field" name="s" placeholder="'. $find_text .'"/>
										<input type="hidden" name="post_type" value="'. $post_type .'" />
										<input type="submit" class="btn real-btn" alt="Search" value="Search" />
									</form>
								';
							$output .= '</div>';
							
							echo $output;
						}
                        
						get_template_part("loop");
						?>
                    </div>

                </div><!-- End Main Content -->

            </div> <!-- End span9 -->

            <?php get_sidebar(); ?>

        </div><!-- End contents row -->
    </div><!-- End Content -->

<?php get_footer(); ?>

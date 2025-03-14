<?php
get_header();
?>

        <!-- Page Head -->
        <?php //get_template_part("banners/blog_page_banner"); ?>

        <!-- Content -->
        <div class="container contents single" style="margin-top:30px">
            <div class="row">
                <div class="span9 main-wrap">
                    <!-- Main Content -->
                    <div class="main">

                        <div class="inner-wrapper">
                            <?php
                            if ( have_posts() ) :
                                while ( have_posts() ) :
                                    the_post();

                                    $format = get_post_format();
                                    if( false === $format ) { $format = 'standard'; }

                                    ?>
                                    <article  <?php post_class(); ?>>
                                            <header>
                                                <h1 class="post-title"><?php the_title(); ?></h1>
                                                <div class="post-meta <?php echo $format; ?>-meta thumb-<?php echo has_post_thumbnail()?'exist':'not-exist'; ?>">
                                                    <span> <?php _e('Posted on', 'framework'); ?>  <span class="date"> <?php the_time('F d, Y'); ?> </span> </span>
                                                    <?php _e('by', 'framework'); ?> <a href="http://www.bendbulletin.com/" target="_blank"> <?php the_author(); ?></a><?php _e(', in ', 'framework'); ?>  <?php the_category(', '); ?>
                                                </div>
                                            </header>
                                            <?php
										
											echo the_content();
										
											/*$content = get_the_content();
											if(strpos( $content, '<img' ) !== false) {
												echo $content;
											} else {
												get_template_part( 'post-formats/' . $format );
												echo $content;
											}*/
                                            ?>
                                    </article>
                                    <?php

                                    wp_link_pages(array('before' => '<div class="pages-nav clearfix">', 'after' => '</div>', 'next_or_number' => 'next'));

                                endwhile;
                                comments_template();
                            endif;
                            ?>
                        </div>

                    </div><!-- End Main Content -->

                </div> <!-- End span9 -->

                <?php get_sidebar(); ?>

            </div><!-- End contents row -->

        </div><!-- End Content -->

<?php get_footer(); ?>
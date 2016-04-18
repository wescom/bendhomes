<section class="home-recent-posts container-fluid clearfix">
    <?php
    $news_posts_title = get_option('theme_news_posts_title');
    $news_posts_text = get_option('theme_news_posts_text');

    if( !empty( $news_posts_title ) ){
        ?>
        <div class="section-title">
            <h3><?php echo $news_posts_title; ?></h3>
            <?php
            if( !empty( $news_posts_text ) ){
                ?><p><?php echo $news_posts_text; ?></p><?php
            }
            ?>
        </div>
    <?php
    }
    ?>
    <div class="recent-posts-container row-fluid clearfix">
        <?php
        $recent_posts_args = array(
            'post_type' => 'post',
            // 'cat' => '68',
            'posts_per_page' => 3,
            'ignore_sticky_posts' => 1,
            'tax_query' => array(
                array(
                  'taxonomy' => 'category',
                  'field'    => 'slug',
                  'terms'    => array( 'real-estate-news' )
                  /*
                  'taxonomy' => 'post_format',
                  'field' => 'slug',
                  'terms' => array('post-format-quote', 'post-format-link', 'post-format-audio'),
                  'operator' => 'NOT IN'
                  */
                )
            )
        );

        // The Query
        $recent_posts_query = new WP_Query( $recent_posts_args );

        // The Loop
        if ( $recent_posts_query->have_posts() ) {
            while ( $recent_posts_query->have_posts() ) {
                $recent_posts_query->the_post();
                $format = get_post_format( $post->ID );
                if (false === $format) {
                    $format = 'standard';
                }
                $format = 'newsitem';
                ?>
                <article <?php post_class('span4 clearfix'); ?>>
                    <?php get_template_part( "bend-homes/$format" ); ?>
                    <h4 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <div class="post-meta">
                        <span class="date"> <?php the_time('M j, Y g:iA'); ?></span>
                        <?php /* <span><?php _e('by', 'framework'); ?> <span class="author-link"><?php the_author() ?></span></span> */ ?>
                    </div>
                    <p><?php framework_excerpt(18);  ?></p>
                    <a class="more-details" href="<?php the_permalink() ?>"><?php _e('Read More ','framework'); ?><i class="fa fa-caret-right"></i></a>
                </article>
                <?php
            }
        }else{
            ?>
            <div class="span12">
                <p class="nothing-found"><?php _e('No Posts Found!', 'framework'); ?></p>
            </div>
            <?php
        }

        /* Restore original Post Data */
        wp_reset_query();
        ?>
    </div>
</section>

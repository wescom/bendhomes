<?php 
	global $post;
    $format = get_post_format();
    if( false === $format ) {
        $format = 'standard';
    }
?>

<article <?php post_class( array('row-fluid') ); ?>>
	<div class="span5">
    	<?php get_template_part( "post-formats/$format" ); ?>
    </div>
    <div class="span7">
    	<header>
            <h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <small class="post-meta <?php echo $format; ?>-meta thumb-<?php echo has_post_thumbnail()?'exist':'not-exist'; ?>">
                <span><?php _e('Posted on', 'framework'); ?> <span class="date"> <?php the_time('F d, Y'); ?></span></span>
                <span><?php _e('by', 'framework'); ?>
					<a class="author-link" href="http://www.bendbulletin.com/" target="_blank"><?php the_author() ?></a>
                    <?php _e('in', 'framework'); ?> <?php the_category(', '); ?> 
                    <?php if (function_exists('z_taxonomy_image')) z_taxonomy_image(); ?>
                </span>
            </small>
        </header>
		<div><?php //framework_excerpt(30);  ?><?php //echo str_replace( '&#013; ', '', get_the_excerpt() ); ?></div>
    </div>
</article>

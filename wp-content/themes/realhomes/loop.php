<?php
if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
		
		$post_type = $_GET['post_type'];
		
		if( $post_type == 'agent' ) {
			
			get_template_part("bend-homes/template-parts/search-agent");
			
		} elseif( $post_type == 'company' ) {
		
			get_template_part("bend-homes/template-parts/search-company");
			
		} else {
		
        	get_template_part("template-parts/article-for-listing");
		
		}
    endwhile;
    theme_pagination( $wp_query->max_num_pages);
else :
    ?><p class="nothing-found"><?php _e('No Posts Found!', 'framework'); ?></p><?php
endif;
?>
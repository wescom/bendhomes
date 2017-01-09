<?php
    // Banner Image
    $banner_image_path = get_default_banner();

    // Banner Title
    $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
    $banner_title = $current_term->name;

    // Banner Sub Title
    $banner_sub_title = !empty($current_term->description) ? ' | '. $current_term->description : '';

	$taxonomy = get_query_var( 'taxonomy' );
	switch( $taxonomy ) {
		case 'elementary_school' :
			$banner_sub_title = ' Elementary School';
			break;
		case 'middle_school' :
			$banner_sub_title = ' Middle School';
			break;
		case 'high_school' :
			$banner_sub_title = ' High School';
			break;
		case 'county' :
			$banner_sub_title = ' County';
			break;
		case 'area' :
			$banner_sub_title = ' Area';
			break;
	}
    ?>
    <?php /* <div class="page-head" style="background-repeat: no-repeat;background-position: center top;background-image: url('<?php echo $banner_image_path; ?>'); background-size: cover; "> */ ?>
    <div class="page-head">
        <?php if(!('true' == get_option('theme_banner_titles'))): ?>
        <div class="container">
            <div class="wrap clearfix">
                <h1 class="page-title">xx<?php echo $banner_title .''. $banner_sub_title; ?>
                	<img src="<?php if (function_exists('z_taxonomy_image_url')) echo z_taxonomy_image_url(); ?>" />
                </h1>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- End Page Head -->

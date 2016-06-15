<?php
// Create "Company" custom post type using CPT class

require_once( TBB_FUNCTIONS_DIR .'/class-custom-posts.php' );

if(class_exists('CPT')) {
	
	// create a new custom post type
	$company = new CPT( array(
		'post_type_name' => 'company',
		'singular' => 'Company',
		'plural' => 'Companies',
		'slug' => 'company'
	), array(
    	'supports' => array('title', 'editor', 'thumbnail'),
		'menu_position' => 5,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'has_archive' => true,
	));
	
	// create taxonomies
	$company->register_taxonomy(array(
		'taxonomy_name' => 'company_category',
		'singular' => 'Company Category',
		'plural' => 'Company Categories',
		'slug' => 'company_category'
	), array(
		'hierarchical' => true
	));
	
	// define the columns to appear on the admin screen
	$company->columns(array(
		'cb' => '<input type="checkbox" />',
		'title' => __('Title'),
		'image' => __('Image'),
		'project_category' => __('Categories'),
		'date' => __('Date')
	));
	
	// populate the Image column
	$company->populate_column('image', function($column, $post) {
		echo get_the_post_thumbnail( $post->ID, array(80,80) );
	});
	
	// populate the Company Category column
	$company->populate_column('company_category', function($column, $post) {
		$terms = get_the_terms( $post->ID , 'company_category' );
		foreach ( $terms as $term ) {
			echo '<a href="'. $term->slug .'">'. $term->name .'</a>, ';
		}
	});
	
	// array to make additional columns sortable
	$company->sortable(array(
		'company_category' => array('project_category', true)
	));
	
	// use "multisite" icon for post type
	$company->menu_icon("dashicons-admin-multisite");
}
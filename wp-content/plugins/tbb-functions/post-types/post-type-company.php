<?php

// Creates our Company CPT
add_action('init', 'tbb_company_post_type');
function tbb_company_post_type() {
	$labels = array(
		'name'               => _x( 'Companies', 'post type general name' ),
		'singular_name'      => _x( 'Company', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New Company' ),
		'edit_item'          => __( 'Edit Company' ),
		'new_item'           => __( 'New Company' ),
		'all_items'          => __( 'All Companies' ),
		'view_item'          => __( 'View Company' ),
		'search_items'       => __( 'Search Companies' ),
		'not_found'          => __( 'No company found' ),
		'not_found_in_trash' => __( 'No companies found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Companies'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our Companies',
		'public'        => true,
		'menu_position' => 4,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'capability_type' => 'post',
		'supports'      => array( 'title', 'editor', 'author', 'thumbnail' ),
		'menu_icon' => 'dashicons-admin-multisite',
		'taxonomies'    => array( 'company_category' ),
		'hierarchical'  => true,
		'has_archive'   => true,
		'rewrite' => array( 
			'slug' => 'company',
			'with_front' => false
		)
	);
	register_post_type( 'company', $args ); 
}


// Hierarchal categories for Company CPT
add_action( 'init', 'tbb_company_taxonomies', 0 );
function tbb_company_taxonomies() {
	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Company Categories' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ), 
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Company Category' ),
		'menu_name'         => __( 'Categories' ),
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);
	register_taxonomy( 'company_category', 'company', $args );
}
	

// Creates custom admin notification messages. Looks better than default WP notifications
add_filter( 'post_updated_messages', 'tbb_company_better_messages' );
function tbb_company_better_messages( $messages ) {
	global $post, $post_ID;
	
	$messages['company'] = array(
		0 => '', 
		1 => sprintf( __('Company updated. <a href="%s">View Company Page</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('company updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Company restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Company published. <a href="%s">View company</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Company saved.'),
		8 => sprintf( __('Company submitted. <a target="_blank" href="%s">Preview company</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Company scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview company</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Company draft updated. <a target="_blank" href="%s">Preview company</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	
	return $messages;
}
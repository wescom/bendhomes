<?php
/**
*** This file creates a metabox field on pages, posts to add custom css/scripts on a per page basis.
*** This metabox is added to the footer
*** Optionally you may uncomment the necessary lines below to add an additional css/scripts box that loads into the <head> too.
**/

add_action( 'add_meta_boxes', 'tbb_meta_box_scripts' );

$types = 'page, post';

function tbb_meta_box_scripts() {
	//add_meta_box( 'head-meta-box', 'Head: Page Specific CSS and/or Scripts', 'head_meta_box_callback', $types, 'advanced', 'low' );
	add_meta_box( 'tbb-meta-box', 'Page Specific CSS and/or Scripts', 'meta_box_callback', $types, 'advanced', 'low' );
}

	
function meta_box_callback( $post ) {
	$values = get_post_custom( $post->ID );
	$selected = isset( $values['tbb_meta_box_scripts_embed'] ) ? $values['tbb_meta_box_scripts_embed'][0] : '';

	wp_nonce_field( 'tbb_meta_box_nonce', 'meta_box_nonce' );
	?>
	<p>
		<label for="tbb_meta_box_scripts_embed"><p>Don't forget to wrap your css with <strong>&lt;style&gt; &lt;/style&gt;</strong> tags and your scripts with <strong>&lt;script&gt; &lt;/script&gt;</strong> tags.</p></label>
		<textarea name="tbb_meta_box_scripts_embed" id="tbb_meta_box_scripts_embed" cols="110" rows="10" style="width:97%;"><?php echo $selected; ?></textarea>
	</p>
	<?php   
}


/*function head_meta_box_callback( $post ) {
	$values = get_post_custom( $post->ID );
	$selected = isset( $values['head_meta_box'] ) ? $values['head_meta_box'][0] : '';
	
	wp_nonce_field( 'my_head_meta_box_nonce', 'head_meta_box_nonce' );
	?>
    <p>
		<label for="head_meta_box"><p>Don't forget to wrap your css with <strong>&lt;style&gt; &lt;/style&gt;</strong> tags and your scripts with <strong>&lt;script&gt; &lt;/script&gt;</strong> tags.</p></label>
		<textarea name="head_meta_box" id="head_meta_box" cols="110" rows="10" style="width:97%;"><?php echo $selected; ?></textarea>
	</p>
    <?php
}*/
	
	
add_action( 'save_post', 'tbb_meta_box_save' );
function tbb_meta_box_save( $post_id ) {
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'tbb_meta_box_nonce' ) ) return;
	//if( !isset( $_POST['head_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['head_meta_box_nonce'], 'my_head_meta_box_nonce' ) ) return;

	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;

	// now we can actually save the data
	$allowed = array( 
		'a' => array( // on allow a tags
			'href' => array() // and those anchords can only have href attribute
		)
	);

	// Probably a good idea to make sure your data is set

	if( isset( $_POST['tbb_meta_box_scripts_embed'] ) )
		update_post_meta( $post_id, 'tbb_meta_box_scripts_embed', $_POST['tbb_meta_box_scripts_embed'] );
		
	//if( isset( $_POST['head_meta_box'] ) )
	//	update_post_meta( $post_id, 'head_meta_box', $_POST['head_meta_box'] );

}

// Attach custom metabox to wp_footer function for all pages on site except landing pages.
add_action('wp_footer', 'tbb_add_metabox_to_footer');
function tbb_add_metabox_to_footer() {
	global $post;
	echo do_shortcode( get_post_meta($post->ID, 'tbb_meta_box_scripts_embed', true) );
}

/*add_action('wp_head', 'add_metabox_to_head');
function add_metabox_to_head() {
	global $post;
	echo do_shortcode( get_post_meta($post->ID, 'head_meta_box', true) );
}*/
	
?>
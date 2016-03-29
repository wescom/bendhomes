<?php
/**
 * Template Name: Import Agents
 *
 * Allow users to update their profile information from front end
 *
 */

// get_header();
?>

<!-- Page Head -->

                    <?php
                    /* Page contents */
                    if ( have_posts() ) :
                        while ( have_posts() ) :
                            the_post();
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class("clearfix"); ?>>
                                <?php the_content(); ?>
                            </article>
                            <?php
                        endwhile;
                    endif;


                    /* Stuff related to property submit or property edit */
                    if( is_user_logged_in() ) {

                        // get user information
                        global $current_user;
                        get_currentuserinfo();
                        $current_user_meta = get_user_meta( $current_user->ID );
                        print_r($current_user_meta);
                        ?>
                        <form id="inspiry-edit-user" enctype="multipart/form-data" class="submit-form">
                          <?php
                            if( isset( $current_user_meta['profile_image_id'] ) ) {
                                $profile_image_id = intval( $current_user_meta['profile_image_id'][0] );
                                if ( $profile_image_id ) {
                                    echo wp_get_attachment_image( $profile_image_id, 'agent-image' );
                                    echo '<input type="hidden" class="profile-image-id" name="profile-image-id" value="' . $profile_image_id .'"/>';
                                }
                            }
                            ?>

                                        <input name="first-name" type="text" id="first-name" value="<?php if( isset( $current_user_meta['first_name'] ) ) { echo $current_user_meta['first_name'][0]; } ?>" autofocus />
                                        <input name="last-name" type="text" id="last-name" value="<?php if( isset( $current_user_meta['last_name'] ) ) {  echo $current_user_meta['last_name'][0]; } ?>" />
                                        <input class="required" name="display-name" type="text" id="display-name" value="<?php echo $current_user->display_name; ?>" required />
                                        <input class="required" name="email" type="email" id="email" value="<?php echo $current_user->user_email; ?>" required/>
                                        <input name="pass1" type="password" id="pass1" />
                                        <input name="pass2" type="password" id="pass2" />
                                        <textarea name="description" id="description" rows="5" cols="30"><?php if( isset( $current_user_meta['description'] ) ) { echo $current_user_meta['description'][0]; } ?></textarea>
                                        <input name="mobile-number" type="text" id="mobile-number" value="<?php if( isset( $current_user_meta['mobile_number'] ) ) { echo $current_user_meta['mobile_number'][0]; } ?>" />
                                        <input name="office-number" type="text" id="office-number" value="<?php if( isset( $current_user_meta['office_number'] ) ) { echo $current_user_meta['office_number'][0]; } ?>" />
                                        <input name="fax-number" type="text" id="fax-number" value="<?php if( isset( $current_user_meta['fax_number'] ) ) { echo $current_user_meta['fax_number'][0]; } ?>" />
                                        <input name="facebook-url" type="text" id="facebook-url" value="<?php if( isset( $current_user_meta['facebook_url'] ) ) { echo $current_user_meta['facebook_url'][0]; } ?>" />
                                        <input name="twitter-url" type="text" id="twitter-url" value="<?php if( isset( $current_user_meta['twitter_url'] ) ) { echo $current_user_meta['twitter_url'][0]; } ?>" />
                                        <input name="google-plus-url" type="text" id="google-plus-url" value="<?php if( isset( $current_user_meta['google_plus_url'] ) ) { echo $current_user_meta['google_plus_url'][0]; } ?>" />
                                        <input name="linkedin-url" type="text" id="linkedin-url" value="<?php if( isset( $current_user_meta['linkedin_url'] ) ) { echo $current_user_meta['linkedin_url'][0]; } ?>" />

                                        <?php
                                        //action hook for plugin and extra fields
                                        do_action('edit_user_profile',$current_user);
                                        // WordPress Nonce for Security Check
                                        wp_nonce_field( 'update_user', 'user_profile_nonce' );
                                        ?>
                                        <input type="hidden" name="action" value="inspiry_update_profile" />

                                        <input name="update-user" type="submit" id="update-user" class="real-btn" value="<?php _e('Save Changes', 'framework'); ?>" />
                                    </div>


                                </div>
                        <?php
                    } ?>

<?php // get_footer(); ?>

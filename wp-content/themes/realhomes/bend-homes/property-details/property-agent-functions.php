<?php
global $post;   // property

/**
 * A function that works as re-usable template
 *
 * @param array $args
 */

 function bhLookupTaxonomy($postid,$taxonomy) {
   $args = array(
     'orderby' => 'name',
     'order' => 'ASC',
     'fields' => 'all'
   );
   $query = wp_get_object_terms($postid, $taxonomy, $args);
   $output = $query[0]->{slug};
   return $output;
 }


function display_sidebar_agent_box( $args ) {
    global $post;
    ?>
	<section class="widget">
		<?php
		if ( isset( $args[ 'agent_title_text' ] ) && ! empty( $args[ 'agent_title_text' ] ) ) {
			?><h3 class="title"><?php echo $args[ 'agent_title_text' ] ?></h3><?php
		}
		?>
		<div class="agent-info">
			<?php
			if ( isset( $args[ 'display_author' ] ) && ( $args[ 'display_author' ] ) ) {
				if ( isset( $args[ 'profile_image_id' ] ) && ( 0 < $args[ 'profile_image_id' ] ) ) {
					echo wp_get_attachment_image( $args[ 'profile_image_id' ], 'agent-image' );
				} elseif ( isset( $args[ 'agent_email' ] ) ) {
					echo get_avatar( $args[ 'agent_email' ], '210' );
				}
			} else {
				if ( isset( $args[ 'agent_id' ] ) && has_post_thumbnail( $args[ 'agent_id' ] ) ) {
					?>
					<a href="<?php echo get_permalink( $args[ 'agent_id' ] ); ?>">
						<?php echo get_the_post_thumbnail( $args[ 'agent_id' ], 'agent-image' ); ?>
					</a>
					<?php
				}
			}
			?>
			<ul class="contacts-list">
			<?php
				if ( isset( $args[ 'agent_office_phone' ] ) && ! empty( $args[ 'agent_office_phone' ] ) ) {
					?>
					<li class="office">
						<?php include( get_template_directory() . '/images/icon-phone.svg' );
						_e( 'Office', 'framework' ); ?> : <?php echo $args[ 'agent_office_phone' ]; ?>
					</li>
					<?php
				}
				if ( isset( $args[ 'agent_mobile' ] ) && ! empty( $args[ 'agent_mobile' ] ) ) {
					?>
					<li class="mobile">
						<?php include( get_template_directory() . '/images/icon-mobile.svg' );
						_e( 'Mobile', 'framework' ); ?> : <?php echo $args[ 'agent_mobile' ]; ?>
					</li>
					<?php
				}
				if ( isset( $args[ 'agent_office_fax' ] ) && ! empty( $args[ 'agent_office_fax' ] ) ) {
					?>
					<li class="fax">
						<?php include( get_template_directory() . '/images/icon-printer.svg' );
						_e( 'Fax', 'framework' ); ?> : <?php echo $args[ 'agent_office_fax' ]; ?>
					</li>
					<?php
				}
			?>
			</ul>
      <?php // brokerageBlock($args[ 'agent_id' ]); ?>
			<p><?php
				echo $args[ 'agent_description' ];
        brokerageBlock($args[ 'agent_id' ]);
				if ( isset( $args[ 'display_author' ] ) && ( $args[ 'display_author' ] ) ) {
					?><a class="real-btn" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php _e( 'Know More', 'framework' ); ?></a><?php
				} else {
					?><a class="real-btn" href="<?php echo get_permalink( $args[ 'agent_id' ] ); ?>"><?php _e( 'View Agent Profile & Properties', 'framework' ); ?></a><?php
				}
			?></p>
            <img class="reciprocity-logo" src="<?php echo get_stylesheet_directory_uri(); ?>/images/brslogosm.gif" alt="Broker Reciprocity Logo" />
		</div>

		<?php
		if ( isset( $args[ 'agent_email' ] ) && !empty($args[ 'agent_email' ]) ) {
			$agent_form_id = 'agent-form-id';
			if ( isset( $args[ 'agent_id' ] ) ) {
				$agent_form_id .= $args[ 'agent_id' ];
			}
			?>
			<div class="enquiry-form">
				<h4 class="agent-form-title"><?php _e('Send Message', 'framework'); ?></h4>
				<form id="<?php echo $agent_form_id ?>" class="agent-form contact-form-small" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
					<input type="text" name="name" placeholder="<?php _e('Name', 'framework'); ?>" class="required" title="<?php _e('* Please provide your name', 'framework'); ?>">
					<input type="text" name="email" placeholder="<?php _e('Email', 'framework'); ?>" class="email required" title="<?php _e('* Please provide valid email address', 'framework'); ?>">
					<textarea  name="message" class="required" placeholder="<?php _e('Message', 'framework'); ?>" title="<?php _e('* Please provide your message', 'framework'); ?>"></textarea>
					<?php
					if ( isset( $args[ 'agents_count' ] ) && ( $args[ 'agents_count' ] == 1 ) ) {
						get_template_part( 'recaptcha/custom-recaptcha' );  // Display recaptcha if enabled and configured from theme options
					} else {
						?><input type="hidden" name="inspiry_recaptcha" value="disabled" /><?php
					}
					?>
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'agent_message_nonce' ); ?>"/>
					<input type="hidden" name="target" value="<?php echo antispambot( $args[ 'agent_email' ] ); ?>">
					<input type="hidden" name="action" value="send_message_to_agent"/>
					<input type="hidden" name="property_title" value="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>"/>
					<input type="hidden" name="property_permalink" value="<?php echo esc_url_raw( get_permalink( $post->ID ) ); ?>"/>

					<input type="submit" value="<?php _e( 'Send Message', 'framework' ); ?>" name="submit" class="submit-button real-btn">
					<img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" class="ajax-loader" alt="Loading...">
					<div class="clearfix form-separator"></div>
					<div class="error-container"></div>
					<div class="message-container"></div>
				</form>
			</div>
			<script type="text/javascript">
				(function($){
					"use strict";

					if ( jQuery().validate && jQuery().ajaxSubmit ) {

						var agentForm = $('#<?php echo $agent_form_id ?>');
						var submitButton = agentForm.find( '.submit-button' ),
							ajaxLoader = agentForm.find( '.ajax-loader' ),
							messageContainer = agentForm.find( '.message-container' ),
							errorContainer = agentForm.find( ".error-container" );

						// Property detail page form
						agentForm.validate( {
							errorLabelContainer: errorContainer,
							submitHandler : function( form ) {
								$(form).ajaxSubmit( {
									beforeSubmit: function(){
										submitButton.attr('disabled','disabled');
										ajaxLoader.fadeIn('fast');
										messageContainer.fadeOut('fast');
										errorContainer.fadeOut('fast');
									},
									success: function( ajax_response, statusText, xhr, $form) {
										var response = $.parseJSON ( ajax_response );
										ajaxLoader.fadeOut('fast');
										submitButton.removeAttr('disabled');
										if( response.success ) {
											$form.resetForm();
											messageContainer.html( response.message ).fadeIn('fast');
										} else {
											errorContainer.html( response.message ).fadeIn('fast');
										}
									}
								} );
							}
						} );

					}

				})(jQuery);
			</script>
			<?php
		}
		?>
	</section>
	<?php
}

// $bh_display_agent_info = get_option( 'theme_display_agent_info' );
// $bh_agent_display_option = get_post_meta( $post->ID, 'REAL_HOMES_agent_display_option', true );

?>

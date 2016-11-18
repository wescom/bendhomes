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
	<section class="agent-widget clearfix">
		<?php
		// Display Image
		if ( isset( $args[ 'display_author' ] ) && ( $args[ 'display_author' ] ) ) {
			if ( isset( $args[ 'profile_image_id' ] ) && ( 0 < $args[ 'profile_image_id' ] ) ) {
				echo wp_get_attachment_image( $args[ 'profile_image_id' ], 'agent-image' );
			} elseif ( isset( $args[ 'agent_email' ] ) ) {
				echo get_avatar( $args[ 'agent_email' ], '210' );
			}
		} else {
			if ( isset( $args[ 'agent_id' ] ) && has_post_thumbnail( $args[ 'agent_id' ] ) ) {
				?>
				<a class="agent-image" href="<?php echo get_permalink( $args[ 'agent_id' ] ); ?>">
					<?php echo get_the_post_thumbnail( $args[ 'agent_id' ], 'agent-image' ); ?>
				</a>
				<?php
			}
		}
		?>
		
		<div class="agent-info">
		
			<?php
			if ( isset( $args[ 'agent_title_text' ] ) && ! empty( $args[ 'agent_title_text' ] ) ) { ?>
				<h3 class="title">Listing Agent: <strong>
				<a href="<?php echo get_permalink( $args[ 'agent_id' ] ); ?>"><?php echo str_replace( 'Agent ', '', $args[ 'agent_title_text' ] ); ?></a>
				</strong></h3>
				<?php
			}
			
			if ( isset( $args[ 'agent_brokerage' ] ) && ! empty( $args[ 'agent_brokerage' ] ) ) { ?>
			<div class="agent-office-name">
				<?php echo $args[ 'agent_brokerage' ]; ?>
			</div>
			<?php } ?>

			<div class="contacts-list">
			<?php
				if ( isset( $args[ 'agent_office_phone' ] ) && ! empty( $args[ 'agent_office_phone' ] ) ) {
					?>
					<span class="office">
						<a href="tel:<?php echo preg_replace("/[^0-9]/", "", $args[ 'agent_office_phone' ]); ?>"><?php echo $args[ 'agent_office_phone' ]; ?> (Office)</a>
					</span>
					<?php
				}
				if ( isset( $args[ 'agent_mobile' ] ) && ! empty( $args[ 'agent_mobile' ] ) ) {
					?>
					<span class="mobile">
						<a href="tel:<?php echo preg_replace("/[^0-9]/", "", $args[ 'agent_mobile' ]); ?>"><?php echo $args[ 'agent_mobile' ]; ?> (Cell)</a>
					</span>
					<?php
				}
				if ( isset( $args[ 'agent_office_fax' ] ) && ! empty( $args[ 'agent_office_fax' ] ) ) {
					?>
					<span class="fax">
						<?php
						_e( '<i class="fa fa-print"></i> Fax', 'framework' ); ?> : <?php echo $args[ 'agent_office_fax' ]; ?>
					</span>
					<?php
				}
			?>
			</div>
		
		</div>
		
		<?php
		//echo $args[ 'agent_description' ];
		//brokerageBlock($args[ 'agent_id' ]);

		if ( isset( $args[ 'display_author' ] ) && ( $args[ 'display_author' ] ) ) {
			?><a class="agent-btn" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php _e( 'Know More', 'framework' ); ?></a><?php
		} else {
			?><a class="agent-btn" href="<?php echo get_permalink( $args[ 'agent_id' ] ); ?>"><?php _e( 'View Profile & Properties', 'framework' ); ?></a><?php
		}
		?>
		<img class="reciprocity-logo" src="<?php echo get_stylesheet_directory_uri(); ?>/images/brslogosm.gif" alt="Broker Reciprocity Logo" />

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

<?php
// This file contains all custom code for Gravity Forms

// Remove anchor added to url when gforms is a multi-part form
add_filter( 'gform_confirmation_anchor', '__return_false' );

// Custom ajax loading spinner
//add_filter( 'gform_ajax_spinner_url', 'custom_gforms_spinner' );
/*function custom_gforms_spinner( $src ) {
    return TBB_FUNCTIONS_URL .'/images/loader.gif';
}

// Adds custom jquery form validation to GForms
//add_action('gform_enqueue_scripts', 'tbb_load_custom_validation', 10, 2);
function tbb_load_custom_validation($form) {
	wp_enqueue_script("jquery-validate", TBB_FUNCTIONS_URL . "/js/jquery.validate.min.js");
	wp_enqueue_script("tbb-additional-methods", TBB_FUNCTIONS_URL . "/js/additional-methods.min.js");
}

// Adds this custom jquery validation directly after the Wescom Contact Form only
//add_filter('gform_pre_render_5', 'tbb_contact_form_validation');
function tbb_contact_form_validation($form) {	
	$script = '
	(function($){ 
	  
		$("#gform_'.$form['id'].'").validate({
			rules: {
				"input_1": "required",
				"input_2": "required",
				"input_3": {
					required: true,
					email: true
				},
				"input_4": {
					required: true,
					phoneUS: true
				},
				"input_5": "required"
			},
			errorPlacement: function (error, element) {
			  //error.insertAfter($(element).parent());
			  //$(element).parent().addClass("error");
			  return true;
			},
			highlight: function(element) {
				$(element).parent().addClass("error");
			},
			unhighlight: function(element) {
				$(element).parent().removeClass("error");
			}
		});
		
		$("#gform_'.$form['id'].' .gform_button").click(function() {
			$("#gform_'.$form['id'].' .gform_button").attr("disabled","disabled");
			
			if ( $("#gform_'.$form['id'].'").valid() ) {
				$("#gform_'.$form['id'].'").submit();
			} else {
				$("#gform_'.$form['id'].' .gform_button").removeAttr("disabled");
				  return false;
			}
			
		});
	  
	})(jQuery);
	';
	
	GFFormDisplay::add_init_script($form['id'], 'per_form_validation', GFFormDisplay::ON_PAGE_RENDER, $script);
    return $form;
}

//add_action('gform_enqueue_scripts', 'tbb_load_jquery_validation', 10, 2);
function tbb_load_jquery_validation($form) {
	// Array of our Wescom forms
	$wescom_forms = array( '5' );
	// Don't include Wescom form
	if(in_array($form['id'], $wescom_forms ))
        return;
	wp_enqueue_script("tbb-form-validation", TBB_FUNCTIONS_URL . "/js/tbb-validation.js");
}*/
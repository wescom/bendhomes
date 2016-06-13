//Convert gravity form errors into bootstrap popovers to condense form height

// Define function variable outside jQuery wrapper so its available outside of this file
var minify_gform_validation;

jQuery(function($) {
	minify_gform_validation();
});

(function($) {
	minify_gform_validation = function(selector, old_unique) {	
		
		// Only handle fields for target form if one is passed in
		if(typeof selector !== 'undefined')
		{
			var target = document.getElementById(selector),
				target_form = target.tagName == 'FORM' ? $(target) : $('form', target);
			
			target_form.addClass('gform_minify_errors');
			
			var fields = $('.gfield', target_form);
		} else {
			target_form = $('.gform_minify_errors');
			var fields = $('.gfield', target_form);
		}
		
		var proceed = true;
		
		target_form.each(function() {// Run minifier on form submissions to iFrames
			var id = parseInt(this.id.replace('gform_','')),
				unique = $('[name=gform_unique_id]', this).val(),
				iframe = $('#gform_ajax_frame_'+id);
			
			//Verify the Form HTML has updated
			if(typeof old_unique !== 'undefined')
			{
				proceed = old_unique != unique;
				if(!proceed) {
					setTimeout(function() {
						 minify_gform_validation('gform_'+id, unique);
					}, 100);
				}
			} else if(iframe.length) {
				iframe.filter(':not(.minify-added)').addClass('minify-added').load(function() {
					minify_gform_validation('gform_'+id, unique);
				});
			}
		});
		
		if(!proceed) return;
		
		// Parse though form fields and check for errors
		fields.each(function(i) {
			var el = $(this), has_error = el.hasClass('gfield_error');
			
			if(!has_error)
				return;
				
			var val_msg_els = $('.validation_message', el), msg, instructions = new Array(), tooltip_text;
			
			/**
			 * Parse through validation messages and store text. Remove instruction 
			 * message elements (secondary validation messages) after storing the text
			**/
			val_msg_els.each(function(i)
			{
				var el = $(this), val_msg = el.html();
				
				if(el.is('.instruction'))
				{
					instructions.push(val_msg);
					el.remove();
				} else { msg = val_msg }
			});
			
			/**
			 * If the primary validation message container does not exist, append to the
			 * .gfield, otherwise, load the primary message into the tooltip string	
			**/
			if(typeof msg === 'undefined')
			{
				val_msg_els = $("<div class='gfield_description validation_message'></div>");
				val_msg_els.appendTo(this);
				tooltip_text = '';
			} else {
				tooltip_text = msg;
			}
			
			// Add additional instruction text to the tooltip string
			if(instructions.length)
			{
				for(i in instructions)
				{
					if(i != 0 || typeof msg !== 'undefined')
						tooltip_text += "<br>"+instructions[i];
					else
						tooltip_text += instructions[i];
				}
			}
			
			// Initialize Bootstrap tooltip
			val_msg_els.html('')
				.addClass('validation_message_popover').removeClass('validation_message')
				.tooltip({
					html: true,
					placement: 'top',
					title: tooltip_text,
					container: '#'+el.closest('form').attr('id')
				});
		});
	}
})(jQuery);
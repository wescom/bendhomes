// TBB Form Validation

(function($){
	
	$("#gform_5").validate({
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
		  $(element).parent().addClass('error');
		}
	});
	
	$("#gform_5 .gform_button").click(function() {
		$("#gform_5 .gform_button").attr("disabled","disabled");
		
		if ( $("#gform_5").valid() ) {
			$("#gform_5").submit();
		} else {
			$("#gform_5 .gform_button").removeAttr("disabled");
			  return false;
		}
		
	});
	
})(jQuery);
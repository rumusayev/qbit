<script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>jquery/jquery.validate.min.js"></script>
<script type="text/javascript" charset="utf-8">
	$(function() 
	{
	    $.validator.addMethod("cRequired", $.validator.methods.required, "This field should not be empty.");
		$.validator.addMethod("cNonZero", $.validator.methods.min, "The value should be more than zero.");

		$.validator.addClassRules('required_field', {
			   cRequired: true
		   }
		);
		$.validator.addClassRules('non_zero_field', {
			   cNonZero: 1
		   }
		);
	});
	
	function showPopover(element, msg)
	{
		$(element).popover({content: msg})
				.popover('show')
				.blur(function () {
					$(this).popover('destroy');
				});        
	}		
</script>
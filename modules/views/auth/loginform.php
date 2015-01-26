<?php require_once(Backstage::gi()->VIEWS_DIR.'admin/header.php'); ?>
<?php require_once(Backstage::gi()->VIEWS_DIR.'common/validator.php'); ?>

<script type="text/javascript" charset="utf-8">
$(function()
{
    $.validator.addMethod("cRequired", $.validator.methods.required, "Bu dəyər boş olmamalıdır.");
    $.validator.addMethod("cNonZero", $.validator.methods.min, $.format("Bu dəyər sıfırdan böyük olmalıdır."));

    $.validator.addClassRules('required_field', {
           cRequired: true
       }
    );
    $.validator.addClassRules('non_zero_field', {
           cNonZero: 1
       }
    );
	
    // Validate and Save the form    
    $('#auth_submit').click(function(e)
    {
        $('#auth_form').submit();
    });
    
    $('#auth_form').validate({
        errorPlacement: function(error, element) 
        {
           error.appendTo(element.next().find('.validation'));
        },    
        submitHandler: function(form)
        {
           submit_auth_form(form);
        }
    });
	
    function submit_auth_form(form)
    {   
		$.ajax(
		{
            url: form.action,
			type: "GET",
			data: $(form).serialize(),
			success:function(data, status, jqxhr)
			{
				if (data == 0)
				{
					$('#status').html('Login və/və ya parol yanlışdır.');
				}
				else
				{	
					$.cookie('AUTH', data, {expires: 2, path: '/'});
					location.reload(false);
				}
              
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}                        
		}); 
    }
});

</script>

<div class="jumbotron">
<div class="container">
<div id="status"></div>
<form name="auth_form" id="auth_form" role="form" class="form-horizontal" action="<?php echo Backstage::gi()->portal_url;?>auth/login/" method="post">
	<div class="form-group">
		<label for="login" class="col-sm-2 control-label">Login</label>
			<div class="col-xs-9">
				<input type="text" id="login" name="login" class="form-control input-sm" />
			</div>
			<div class="form_hint"><span class="validation"></span></div>			
	</div>		
	<div class="form-group">
		<label for="password" class="col-sm-2 control-label">Parol</label>
			<div class="col-xs-9">
				<input type="password" id="password" name="password" class="form-control input-sm" />
			</div>
			<div class="form_hint"><span class="validation"></span></div>			
	</div>
	<center><button type="button" class="btn btn-primary" id="auth_submit">Daxil ol</button></center>
</form>
</div>
</div>


<?php require_once(Backstage::gi()->VIEWS_DIR.'admin/footer.php'); ?>

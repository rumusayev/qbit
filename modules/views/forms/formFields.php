<script type="text/javascript" charset="utf-8">
	var max_num = <?php echo count($items);?>;
	function typeChange(id, obj)
	{    
		$('#row_'+id+' td.selects').html('');
		if ($(obj).find(":selected").html() == 'select' || $(obj).find(":selected").html() == 'multiselect')
			$("#row_"+id).find("select[name^=field_select_id]").show();
		else
			$("#row_"+id).find("select[name^=field_select_id]").hide();
	};
	
	function deleteField(obj)
	{
		if (confirm("Siz əminsiz?")) 
		{
			deleting_data = [];
			var data_num = $(obj).attr("data-num");
			item = {
				id: $(obj).attr("data-id")
			};
			deleting_data.push(item);
			$.ajax(
			{
				url:"<?php echo Backstage::gi()->portal_url;?>forms/deleteFormFields/",
				type: "DELETE",
				data: {
					fields: JSON.stringify(deleting_data)
				},
				success:function(data)
				{
					$('#fields_table tr#row_'+data_num).remove();
				},
				error: function (request, status, error) 
				{
					console.log(request.responseText);
				}                        
			});      
		}
	};	
	
	$(function()
	{
		$('#add_field_btn').click(function(){
            $.ajax(
            {
                url:"<?php echo Backstage::gi()->portal_url;?>forms/getFormField/",
                type: "GET",
                data: {
					max_num: max_num
				},
                success:function(data)
                {
					$('#fields_table tr:last').after(data);
					$('#field_title_'+max_num+'_tab a:first').tab('show');
					typeChange(max_num, $('select[name=field_type_id\\['+max_num+'\\]]'));
					max_num++;
                },
                error: function (request, status, error) 
				{
                    console.log(request.responseText);
                }                        
            });
		});

		$('#fields_delete_all').click(function(e)
		{
			$("#forms_common_form input[id^=fields_delete]").prop('checked', $(this).prop('checked'));
		});
		
		$('#delete_fields_btn').click(function(e)
		{
			if (confirm("Siz əminsiz?")) 
			{    
				deleting_data = [];
				$("#forms_common_form input[id^=fields_delete]").each(function(el, val)
				{
					if ($(this).prop('checked'))
					{
						item = {
							id: $(this).attr("data-id")
						};
						deleting_data.push(item);
					}
				});

				$.ajax(
				{
					url:"<?php echo Backstage::gi()->portal_url;?>forms/deleteFormFields/",
					type: "DELETE",
					data: {
						fields: JSON.stringify(deleting_data)
					},
					success:function(data)
					{
						$("#forms_common_form input[id^=fields_delete]").each(function(el, val)
						{
							if ($(this).prop('checked'))
							{
								$('#fields_table tr#row_'+$(this).attr("data-num")).remove();
							}
						});						
					},
					error: function (request, status, error) 
					{
						console.log(request.responseText);
					}                        
				});      
			}
		}); 
				
		<?php
		foreach ($items as $field)
		{
			if (is_array($field->field_title))	// Translations for the field are set		
				echo "$('#field_title_".$field->num."_tab a:first').tab('show');";
			echo 'typeChange('.$field->num.', $("select[name=field_type_id\\\\['.$field->num.'\\\\]]"));';				
		}
		?>
	});
</script>

<form name="forms_common_form" id="forms_common_form" role="form" class="form-horizontal" method="post" action="<?php echo Backstage::gi()->portal_url;?>forms/saveFormFields/">
<input type="hidden" id="form_id" name="form_id" value="<?php echo $request->parameters['form_id']; ?>">
<table class="table table-hover" id="fields_table">
<thead>
    <th><input type="checkbox" id="fields_delete_all"/></th>
    <th>Ordering</th>
    <th>Name</th>
    <th>Title</th>
    <th>Type</th>
    <th>Select</th>
    <th>Linked field</th>
    <th>Width</th>
    <th>Translation</th>
    <th>Datetime</th>
    <th>Required</th>
</thead>
<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
<?php

foreach ($items as $field)
{
	include 'formField.php';
}
?>
</table>
</form>
<button type="button" class="btn btn-success" id="add_field_btn"><span class="glyphicon glyphicon-plus"></span> Yeni xana</button>	
<button type="button" class="fields_delete_all_btn btn btn-danger" id="delete_fields_btn"><span class="glyphicon glyphicon-floppy-remove"></span> Pozmaq</button>	
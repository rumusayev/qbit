<script type="text/javascript" charset="utf-8">
	var max_num = <?php echo count($items);?>;
	function typeChange(id, obj)
	{    
		$('#row_'+id+' td.selects').html('');
		if ($(obj).find(":selected").html() == 'select' || $(obj).find(":selected").html() == 'multiselect')
			$("#row_"+id).find("select[name^=option_select_id]").show();
		else
			$("#row_"+id).find("select[name^=option_select_id]").hide();
	};
	
	function deleteOption(obj)
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
				url:"<?php echo Backstage::gi()->portal_url;?>forms/deleteFormFieldSelects/",
				type: "DELETE",
				data: {
					options: JSON.stringify(deleting_data)
				},
				success:function(data)
				{
					$('#options_table tr#row_'+data_num).remove();
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
		$('#add_option_btn').click(function(){
            $.ajax(
            {
                url:"<?php echo Backstage::gi()->portal_url;?>forms/getFormFieldSelectOption/",
                type: "GET",
                data: {
					max_num: max_num
				},
                success:function(data)
                {
					$('#options_table tr:last').after(data);
					$('#option_title_'+max_num+'_tab a:first').tab('show');
					typeChange(max_num, $('select[name=option_type_id\\['+max_num+'\\]]'));
					max_num++;
                },
                error: function (request, status, error) 
				{
                    console.log(request.responseText);
                }                        
            });
		});

		$('#options_delete_all').click(function(e)
		{
			$("#forms_common_form input[id^=options_delete]").prop('checked', $(this).prop('checked'));
		});
		
		$('#delete_options_btn').click(function(e)
		{
			if (confirm("Siz əminsiz?")) 
			{    
				deleting_data = [];
				$("#forms_common_form input[id^=options_delete]").each(function(el, val)
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
					url:"<?php echo Backstage::gi()->portal_url;?>forms/deleteFormFieldSelectOptions/",
					type: "DELETE",
					data: {
						fields: JSON.stringify(deleting_data)
					},
					success:function(data)
					{
						$("#forms_common_form input[id^=options_delete]").each(function(el, val)
						{
							if ($(this).prop('checked'))
							{
								$('#options_table tr#row_'+$(this).attr("data-num")).remove();
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
			if (is_array($field->option_title))	// Translations for the field are set		
				echo "$('#option_title_".$field->num."_tab a:first').tab('show');";
			echo 'typeChange('.$field->num.', $("select[name=option_type_id\\\\['.$field->num.'\\\\]]"));';				
		}
		?>
	});
</script>

<form name="forms_common_form" id="forms_common_form" role="form" class="form-horizontal" method="post" action="<?php echo Backstage::gi()->portal_url;?>forms/saveFormFieldSelectOptions/">
<input type="hidden" id="field_select_id" name="field_select_id" value="<?php echo $request->parameters['field_select_id']; ?>">
<table class="table table-hover" id="options_table">
<thead>
    <th><input type="checkbox" id="options_delete_all"/></th>
    <th>Ordering</th>
    <th>Title</th>
    <th>Value</th>
    <th>Selected</th>
    <th></th>
</thead>
<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
<?php

foreach ($items as $option)
{
	include 'formFieldSelectOption.php';
}
?>
</table>
</form>
<button type="button" class="btn btn-success" id="add_option_btn"><span class="glyphicon glyphicon-plus"></span> Yeni xana</button>	
<button type="button" class="options_delete_all_btn btn btn-danger" id="delete_options_btn"><span class="glyphicon glyphicon-floppy-remove"></span> Pozmaq</button>	
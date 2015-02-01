<?php require_once(Backstage::gi()->VIEWS_DIR.'common/validator.php'); ?>
<?php require_once(Backstage::gi()->VIEWS_DIR.'common/plugins.php'); ?>

<script type="text/javascript" charset="utf-8">
$(function()
{
<?php 
    if (isset($query)) 
    {
        echo "var query = '".base64_encode($query)."';";
        echo "$('#{$name}-crud_params_form #query').val(query);";
    }
    if (isset($mapped_values_f)) 
    {
        echo "var mapped_values_f = '".json_encode($mapped_values_f)."';";
        echo "$('#{$name}-crud_params_form #mapped_values_f').val(mapped_values_f);";
    }
    if (isset($where)) 
    {
        echo "var where = '".base64_encode($where)."';";
        echo "$('#{$name}-crud_params_form #where').val(where);";
    }
    if (isset($tables))
    {
        echo "var tables = '".$tables."';";
        echo "$('#{$name}-crud_params_form #tables').val(tables);";
    }
    if (isset($field_names))
    {
        echo "var field_names = '".$field_names."';";
        echo "$('#{$name}-crud_params_form #field_names').val(field_names);";
    }
    if (isset($order))
    {
        echo "var order = '".$order."';";
        echo "$('#{$name}-crud_params_form #order').val(order);";
    }
	if (isset($search))
    {
        echo "var search = '".json_encode($search)."';";
        echo "$('#{$name}-crud_params_form #search').val(search);";
    }
    if (isset($titles))
    {
        echo "var titles = '".json_encode($titles)."';";
        echo "$('#{$name}-crud_params_form #titles').val(titles);";
    }	
	if (isset($mapped_values))    
	{        
		echo "var mapped_values = '".json_encode($mapped_values)."';";        
		echo "$('#{$name}-crud_params_form #mapped_values').val(mapped_values);";
	}	
	
    if (isset($types))
    {
        echo "var types = '".json_encode($types)."';";
        echo "$('#{$name}-crud_params_form #types').val(types);";
    }
    if (isset($totals))
    {
        echo "var totals = '".json_encode($totals)."';";
        echo "$('#{$name}-crud_params_form #totals').val(totals);";
    }
    if (isset($links))
    {
        echo "var links = '".json_encode($links)."';";
        echo "$('#{$name}-crud_params_form #links').val(links);";
    }
    if (isset($buttons))
    {
        echo "var buttons = '".json_encode($buttons)."';";
        echo "$('#{$name}-crud_params_form #buttons').val(buttons);";
    }   
	if (isset($js_handlers))
    {
        echo "var js_handlers = '".json_encode($js_handlers)."';";
        echo "$('#{$name}-crud_params_form #js_handlers').val(js_handlers);";
    }
    if (isset($format_rules))
    {
        echo "var format_rules = '".json_encode($format_rules)."';";
        echo "$('#{$name}-crud_params_form #format_rules').val(format_rules);";
    }   
	if (isset($form_fields_dimensions))
    {
        echo "var form_fields_dimensions = '".json_encode($form_fields_dimensions)."';";
        echo "$('#{$name}-crud_params_form #form_fields_dimensions').val(form_fields_dimensions);";
    }
    if (isset($ids))
    {
        echo "var ids = '".json_encode($ids)."';";
        echo "$('#{$name}-crud_params_form #ids').val(ids);";
    }
    if (isset($mapped_parents)) 
    {
        echo "var mapped_parents = '".json_encode($mapped_parents)."';";
        echo "$('#{$name}-crud_params_form #mapped_parents').val(mapped_parents);";
    }    
	if (isset($mapped_fields)) 
    {
        echo "var mapped_fields = '".json_encode($mapped_fields)."';";
        echo "$('#{$name}-crud_params_form #mapped_fields').val(mapped_fields);";
    }
    if (isset($mapped_search)) 
    {
        echo "var mapped_search = '".json_encode($mapped_search)."';";
        echo "$('#{$name}-crud_params_form #mapped_search').val(mapped_search);";
    }
    if (isset($mapped_field_inputs)) 
    {
        echo "var mapped_field_inputs = '".json_encode($mapped_field_inputs)."';";
        echo "$('#{$name}-crud_params_form #mapped_field_inputs').val(mapped_field_inputs);";
    }    
	if (isset($mapped_passwords)) 
    {
        echo "var mapped_passwords = '".json_encode($mapped_passwords)."';";
        echo "$('#{$name}-crud_params_form #mapped_passwords').val(mapped_passwords);";
    }
    if (isset($restrictions)) 
    {
        echo "var restrictions = '".json_encode($restrictions)."';";
        echo "$('#{$name}-crud_params_form #restrictions').val(restrictions);";
    }
    if (isset($hidden_edit_fields)) 
    {
        echo "var hidden_edit_fields = '".json_encode($hidden_edit_fields)."';";
        echo "$('#{$name}-crud_params_form #hidden_edit_fields').val(hidden_edit_fields);";
    }    
	if (isset($disabled_edit_fields)) 
    {
        echo "var disabled_edit_fields = '".json_encode($disabled_edit_fields)."';";
        echo "$('#{$name}-crud_params_form #disabled_edit_fields').val(disabled_edit_fields);";
    }
    if (isset($disabled_table_fields)) 
    {
        echo "var disabled_table_fields = '".json_encode($disabled_table_fields)."';";
        echo "$('#{$name}-crud_params_form #disabled_table_fields').val(disabled_table_fields);";
    }
    if (isset($translations)) 
    {
        echo "var translations = '".json_encode($translations)."';";
        echo "$('#{$name}-crud_params_form #translations').val(translations);";
    }
    if (isset($crud_parent_id)) 
    {
        echo "var crud_parent_id = '".$crud_parent_id."';";
        echo "$('#{$name}-crud_params_form #crud_parent_id').val(crud_parent_id);";
    }
    if (isset($uploader_object_type)) 
    {
        echo "var uploader_object_type = '".$uploader_object_type."';";
        echo "$('#{$name}-crud_params_form #uploader_object_type').val(uploader_object_type);";
    }
    if (isset($add_editor_list))
    {
        echo "var add_editor_list = '".json_encode($add_editor_list)."';";
        echo "$('#{$name}-crud_params_form #add_editor_list').val(add_editor_list);";
    }
    if (isset($add_lq_button))
    {
        echo "var add_lq_button = '".json_encode($add_lq_button)."';";
        echo "$('#{$name}-crud_params_form #add_lq_button').val(add_lq_button);";
    }
    if (isset($before_save_method_path)) 
    {
        echo "var before_save_method_path = '".$before_save_method_path."';";
        echo "$('#{$name}-crud_params_form #before_save_method_path').val(before_save_method_path);";
    }
    if (isset($after_save_method_path)) 
    {
        echo "var after_save_method_path = '".$after_save_method_path."';";
        echo "$('#{$name}-crud_params_form #after_save_method_path').val(after_save_method_path);";
    }
	if (isset($after_load_method_path)) 
    {
        echo "var after_load_method_path = '".$after_load_method_path."';";
        echo "$('#{$name}-crud_params_form #after_load_method_path').val(after_load_method_path);";
    }	
	if (isset($after_delete_method_path)) 
    {
        echo "var after_delete_method_path = '".$after_delete_method_path."';";
        echo "$('#{$name}-crud_params_form #after_delete_method_path').val(after_delete_method_path);";
    }
    if (isset($override_orig_save)) 
    {
        echo "var override_orig_save = '".$override_orig_save."';";
        echo "$('#{$name}-crud_params_form #override_orig_save').val(override_orig_save);";
    }
    if (isset($crud_resource_types)) 
    {
        echo "var crud_resource_types = '".json_encode($crud_resource_types)."';";
        echo "$('#{$name}-crud_params_form #crud_resource_types').val(crud_resource_types);";
    }
    if (isset($unique_fields)) 
    {
        echo "var unique_fields = '".json_encode($unique_fields)."';";
        echo "$('#{$name}-crud_params_form #unique_fields').val(unique_fields);";
    }
    if (isset($additional_form_field))
    {
        echo "var additional_form_field = '".$additional_form_field."';";
        echo "$('#{$name}-crud_params_form #additional_form_field').val(additional_form_field);";
    }    
	if (isset($additional_form_table))
    {
        echo "var additional_form_table = '".$additional_form_table."';";
        echo "$('#{$name}-crud_params_form #additional_form_table').val(additional_form_table);";
    }
	if (isset($manual_search_format)) 
    {
        echo "var manual_search_format = '".json_encode($manual_search_format)."';";

        echo "$('#{$name}-crud_params_form #manual_search_format').val(manual_search_format);";
    }	
?>    
    <?php echo $name;?>_load();
});
function <?php echo $name;?>_load()
{
    $('#<?php echo $name;?>-crud_table').addClass('loading');
    $.ajax(
    {
        url:"<?php echo Backstage::gi()->portal_url;?>crud/load/",
        type: "POST",
        data: {
                crud_params_form: JSON.stringify($('#<?php echo $name;?>-crud_params_form').serializeJSON()), 
                crud_search_form: JSON.stringify($('#<?php echo $name;?>-crud_search_form').serializeJSON())
        },
        success:function(data)
        {
            $('#<?php echo $name;?>-crud_table').html(data);
            $('#<?php echo $name;?>-crud_table').removeClass('loading');
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}
</script>
<form name="<?php echo $name;?>-crud_params_form" id="<?php echo $name;?>-crud_params_form" method="post">
    <input type="hidden" name="name" id="name" value="<?php echo $name;?>"/>
    <input type="hidden" name="query" id="query" value=""/>
    <input type="hidden" name="where" id="where" value=""/>
    <input type="hidden" name="tables" id="tables" value=""/>
    <input type="hidden" name="field_names" id="field_names" value=""/>
    <input type="hidden" name="titles" id="titles" value=""/>	    
    <input type="hidden" name="mapped_values" id="mapped_values" value=""/>			
	<input type="hidden" name="mapped_values_f" id="mapped_values_f" value=""/>
    <input type="hidden" name="types" id="types" value=""/>
    <input type="hidden" name="totals" id="totals" value=""/>
    <input type="hidden" name="links" id="links" value=""/>
    <input type="hidden" name="buttons" id="buttons" value=""/>
    <input type="hidden" name="js_handlers" id="js_handlers" value=""/>
    <input type="hidden" name="format_rules" id="format_rules" value=""/>
    <input type="hidden" name="form_fields_dimensions" id="form_fields_dimensions" value=""/>
    <input type="hidden" name="ids" id="ids"  value=""/>
    <input type="hidden" name="order" id="order" value=""/>
    <input type="hidden" name="search" id="search" value=""/>
    <input type="hidden" name="mapped_fields" id="mapped_fields" value=""/>
    <input type="hidden" name="mapped_parents" id="mapped_parents" value=""/>
    <input type="hidden" name="mapped_search" id="mapped_search" value=""/>
    <input type="hidden" name="mapped_field_inputs" id="mapped_field_inputs" value=""/>
    <input type="hidden" name="mapped_passwords" id="mapped_passwords" value=""/>
    <input type="hidden" name="restrictions" id="restrictions" value=""/>
    <input type="hidden" name="hidden_edit_fields" id="hidden_edit_fields" value=""/>
    <input type="hidden" name="disabled_edit_fields" id="disabled_edit_fields" value=""/>
    <input type="hidden" name="disabled_table_fields" id="disabled_table_fields" value=""/>
    <input type="hidden" name="add_editor_list" id="add_editor_list" value=""/>
    <input type="hidden" name="add_lq_button" id="add_lq_button" value=""/>
    <input type="hidden" name="translations" id="translations" value=""/>
    <input type="hidden" name="crud_current_page" id="crud_current_page" value="1"/>
    <input type="hidden" name="crud_count_per_page" id="crud_count_per_page" value="10"/>
    <input type="hidden" name="before_save_method_path" id="before_save_method_path" value=""/>
    <input type="hidden" name="after_save_method_path" id="after_save_method_path" value=""/>
    <input type="hidden" name="after_load_method_path" id="after_load_method_path" value=""/>
    <input type="hidden" name="after_delete_method_path" id="after_delete_method_path" value=""/>
    <input type="hidden" name="override_orig_save" id="override_orig_save" value=""/>
    <input type="hidden" name="crud_parent_id" id="crud_parent_id" value="0"/>
    <input type="hidden" name="uploader_object_type" id="uploader_object_type" value=""/>
    <input type="hidden" name="crud_resource_types" id="crud_resource_types" value=""/>
    <input type="hidden" name="unique_fields" id="unique_fields" value=""/>
    <input type="hidden" name="additional_form_field" id="additional_form_field" value=""/>
    <input type="hidden" name="additional_form_table" id="additional_form_table" value=""/>
    <input type="hidden" name="manual_search_format" id="manual_search_format" value=""/>
	
</form>
<div id="<?php echo $name;?>-crud_table"></div>
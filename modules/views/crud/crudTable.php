<script type="text/javascript" charset="utf-8">
var <?php echo $name;?>_file_num = 0;
var <?php echo $name;?>_files = [];
var <?php echo $name;?>_current_id = 0;
var name = '<?php echo $name;?>';

$(function()
{
	// Clear popovers
	$('html').on('click', function(e) {
	  if (!$(e.target).is("button") &&
		 !$(e.target).parents().is('.popover.in')) {
		$('[data-original-title]').popover('destroy');
	  }
	});

	$('input.datetime').datetimepicker({language:'en-gb'});

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
    var <?php echo $name;?>_data = <?php echo json_encode($rows); ?>;
    $("#<?php echo $name;?>-crud_params_form #crud_current_page").val(<?php echo $crud_current_page;?>);

    $('.<?php echo $name;?>-crud_header').click(function(e)
    {
        if ($(this).attr("data-order") === '')
        {
            $(this).attr("data-order","asc")
        }
        else if ($(this).attr("data-order") === 'asc')
        {
            $(this).attr("data-order","desc");
        }
        else if ($(this).attr("data-order") === 'desc')
        {
            $(this).attr("data-order","");
        }

        order = '';
        $('.<?php echo $name;?>-crud_header').each(function(i, el)
        {
            if ($(this).attr("data-order") !== '')
                order += $(this).attr("data-column")+' '+$(this).attr("data-order")+',';
        });
        order = order.substring(0, order.length - 1);
        $('#<?php echo $name;?>-crud_params_form #order').val(order);

        <?php echo $name;?>_load();
        e.preventDefault();
        return false;
    });

	// Add form
    $('.<?php echo $name;?>-crud_add_btn').click(function(e)
    {
		<?php echo $name;?>_resetForm("#<?php echo $name;?>-crud_form");
		$("#<?php echo $name;?>-crud_form textarea").each(function(e)
		{
			$(this).val('');
		});
        $("#<?php echo $name;?>-crud_form input:text[name$=\\^id]").val('0');
        $("#<?php echo $name;?>-crud_form input.datetime").val('<?php echo date('Y-m-d H:i:s'); ?>');

		if ($("#<?php echo $name;?>-crud_params_form #crud_parent_id").val() > 0)
			$("#<?php echo $name;?>-crud_form select[id$=-<?php echo reset($mapped_parents); ?>]").val($("#<?php echo $name;?>-crud_params_form #crud_parent_id").val());

			// Here we will select the form of the parent element
		if ($("#<?php echo $name;?>-crud_params_form #crud_parent_id").val() > 0 && $("#<?php echo $name;?>-crud_params_form #additional_form_field").val() != '')
			$.ajax(
			{
				url:"<?php echo Backstage::gi()->portal_url;?>forms/getObjectFormID",
				type: "GET",
				data: {
					object_table: $("#<?php echo $name;?>-crud_params_form #additional_form_table").val(),
					object_id: $("#<?php echo $name;?>-crud_params_form #crud_parent_id").val()
				},
				success:function(data)
				{
					if (data != '')
					{
						$("#<?php echo $name;?>-crud_form select[id$=-<?php echo $additional_form_field; ?>]").val(data);
						$("#<?php echo $name;?>-crud_form select[id$=-<?php echo $additional_form_field; ?>]").change();
					}
				},
				error: function (request, status, error)
				{
					console.log(request.responseText);
				}
			});

		$("#<?php echo $name;?>-crud_params_form #crud_parent_id").val();


		// After we set the value initiate CKEDITOR
		$('.ckeditor_w').each(function(e)
		{
			CKEDITOR.replace(this);
		});
		/*
		// After we set the value initiate TinyMCE
		tinymce.init({
			selector: ".tinymce",
			plugins: [
			 "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			 "save table contextmenu directionality emoticons template paste textcolor codemirror"
			],
			force_br_newlines : false,
			force_p_newlines : false,
			forced_root_block : '',
			toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons | code",
			codemirror : {path: 'codemirror'}
		 });
		 */
    });

    $('.<?php echo $name;?>-crud_view_btn').click(function(e)
    {
        $.each(<?php echo $name;?>_data['id'+$(this).attr("data-id")], function(el, val)
        {
            $("#<?php echo $name;?>-crud_view_"+el).html(val);
        });
    });

	$('#<?php echo $name;?>-crud_edit_modal').on('shown.bs.modal', function()
	{
		$(document).off('focusin.modal');
	});

	// Edit form
    $('.<?php echo $name;?>-crud_edit_btn').click(function(e)
    {
		<?php echo $name;?>_resetForm("#<?php echo $name;?>-crud_form");
		<?php echo $name;?>_current_id = $(this).attr("data-id");
        $.each(<?php echo $name;?>_data['id'+$(this).attr("data-id")], function(el, value)
        {
			if (Object.prototype.toString.call(value) === '[object Array]' && el != 'uploader_files')
			{
				$.each(value, function(tr_el, tr_val)
				{
					$("#<?php echo $name;?>-crud_form input:text[name$=\\^"+el+"\\["+(tr_val.short)+"\\]]").val(tr_val.translation);
					$("#<?php echo $name;?>-crud_form textarea[name$=\\^"+el+"\\["+(tr_val.short)+"\\]]").val(tr_val.translation);
				});
			}/*
			else if (el === 'uploader_files')
			{
				$.each(value, function(fl_el, fl_val){
					$('#<?php echo $name;?>-crud_form #uploader_materials_div').append('<span style="padding-right: 7px;"><img id="file_'+<?php echo $name;?>_file_num+'" height="100" src="<?php echo Backstage::gi()->MATERIALS_URL;?><?php echo $uploader_object_type;?>/'+fl_val.object_id+'/thumbnail/'+fl_val.material_path+'" /><a href="#dummy" file_num="'+<?php echo $name;?>_file_num+'" onclick="<?php echo $name;?>_deleteFile('+fl_val.id+', this);"><span style="vertical-align: top;" class="glyphicon glyphicon-remove"></span></a></span>');
					<?php echo $name;?>_files[<?php echo $name;?>_file_num] = {id:fl_val.id, name: fl_val.material_path};
					<?php echo $name;?>_file_num++;
				});

			}*/
			else
			{
				if (value == 1)
					$("#<?php echo $name;?>-crud_form input:checkbox[name$=\\^"+el+"]").prop('checked', true);
				$("#<?php echo $name;?>-crud_form select[name$=\\^"+el+"]").val(value);
				$("#<?php echo $name;?>-crud_form select[name$=\\^"+el+"]").change(); // Trigger change event to activate form (if there is any)
				$("#<?php echo $name;?>-crud_form input:text[name$=\\^"+el+"]").val(value);
				$("#<?php echo $name;?>-crud_form textarea[name$=\\^"+el+"]").val(value);
			}
        });

			// Here we will select the form of the parent element
		if ($("#<?php echo $name;?>-crud_params_form #crud_parent_id").val() > 0 && $("#<?php echo $name;?>-crud_params_form #additional_form_field").val() != '' && $("#<?php echo $name;?>-crud_form select[id$=-<?php echo $additional_form_field; ?>]").val() == 0)
			$.ajax(
			{
				url:"<?php echo Backstage::gi()->portal_url;?>forms/getObjectFormID",
				type: "GET",
				data: {
					object_table: $("#<?php echo $name;?>-crud_params_form #additional_form_table").val(),
					object_id: $("#<?php echo $name;?>-crud_params_form #crud_parent_id").val()
				},
				success:function(data)
				{
					if (data != '')
					{
						$("#<?php echo $name;?>-crud_form select[id$=-<?php echo $additional_form_field; ?>]").val(data);
						$("#<?php echo $name;?>-crud_form select[id$=-<?php echo $additional_form_field; ?>]").change();
					}
				},
				error: function (request, status, error)
				{
					console.log(request.responseText);
				}
			});

		// After we set the value initiate CKEDITOR
		$('.ckeditor_w').each(function(e)
		{
			CKEDITOR.replace(this);
		});
		<?php
		if (!empty($uploader_object_type))
		{
			?>
				$.ajax(
				{
					url:"<?php echo Backstage::gi()->portal_url;?>materials/getFiles",
					type: "GET",
					data: {
						object_type: '<?php echo $uploader_object_type; ?>',
						object_id: <?php echo $name;?>_current_id
					},
					success:function(data)
					{
						$.each(JSON.parse(data), function(fl_el, fl_val){
							$('#<?php echo $name;?>-crud_form #uploader_materials_div').append('<span style="padding-right: 7px;"><img id="file_'+<?php echo $name;?>_file_num+'" height="100" src="<?php echo Backstage::gi()->MATERIALS_URL;?><?php echo $uploader_object_type;?>/'+fl_val.object_id+'/thumbnail/'+fl_val.material_path+'" /><a href="#dummy" file_num="'+<?php echo $name;?>_file_num+'" onclick="<?php echo $name;?>_deleteFile('+fl_val.id+', this);"><span style="vertical-align: top;" class="glyphicon glyphicon-remove"></span></a></span>');
							<?php echo $name;?>_files[<?php echo $name;?>_file_num] = {id:fl_val.id, name: fl_val.material_path};
							<?php echo $name;?>_file_num++;
						});
					},
					error: function (request, status, error)
					{
						console.log(request.responseText);
					}
				});
			<?php
		}
		?>
		/*
		// After we set the value initiate TinyMCE
		tinymce.init({
			selector: ".tinymce",
			plugins: [
			 "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			 "save table contextmenu directionality emoticons template paste textcolor codemirror"
			],
			force_br_newlines : false,
			force_p_newlines : false,
			forced_root_block : '',
			toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons | code",
			codemirror : {path: 'codemirror'}
		 });
		 */
    });

	$('#<?php echo $name;?>-crud_delete_all').click(function(e)
	{
		$("#<?php echo $name;?>-crud_search_form input[id^=crud_delete]").prop('checked', $(this).prop('checked'));
	});

    $('.<?php echo $name;?>-crud_delete_btn').click(function(e)
    {
        if (confirm("<?php echo Translations::gi()->are_you_sure; ?>"))
        {
			deleting_data = [];
			item = {
				id: $(this).attr("data-id"),
				table: $("#<?php echo $name;?>-crud_search_form input[id$=crud_delete_"+$(this).attr("data-id")+"]").attr("data-table")
			};
			deleting_data.push(item);
            $.ajax(
            {
                url:"<?php echo Backstage::gi()->portal_url;?>crud/delete/",
                type: "DELETE",
                data: {
					deleting_data: JSON.stringify(deleting_data),
					form_params: JSON.stringify($('#<?php echo $name;?>-crud_params_form').serializeJSON())
				},
                success:function(data)
                {
					<?php echo $name;?>_load();
					console.log(data);
                },
                error: function (request, status, error)
				{
                    console.log(request.responseText);
                }
            });
        }
    });
    $('.<?php echo $name;?>-crud_delete_all_btn').click(function(e)
    {
        if (confirm("Siz əminsiz?"))
        {
			deleting_data = [];
			$("#<?php echo $name;?>-crud_search_form input[id^=crud_delete]").each(function(el, val)
            {
				if ($(this).prop('checked'))
				{
					item = {
						id: $(this).attr("data-id"),
						table: $(this).attr("data-table")
					};
					deleting_data.push(item);
				}
            });

            $.ajax(
            {
                url:"<?php echo Backstage::gi()->portal_url;?>crud/delete/",
                type: "DELETE",
                data: {
					deleting_data: JSON.stringify(deleting_data),
					form_params: JSON.stringify($('#<?php echo $name;?>-crud_params_form').serializeJSON())
				},

                success:function(data)
                {
					<?php echo $name;?>_load();
                },
                error: function (request, status, error)
				{
                    console.log(request.responseText);
                }
            });
        }
    });

	$('#<?php echo $name;?>-crud_edit_modal').on('hidden.bs.modal', function ()
	{

		for(name in CKEDITOR.instances)
		{
			CKEDITOR.instances[name].destroy();
		}

		//tinyMCE.remove();
		<?php echo $name;?>_load();
	});

    function showPopover(element, msg)
    {
            $(element).popover({html: true, container: 'body', content: msg})
                .popover('show')
                .blur(function () {
                    $(this).popover('destroy');
                });
    }

    // Validate and Save the form    
    $('#<?php echo $name;?>-crud_save_btn').click(function(e)
    {
        $('#<?php echo $name;?>-crud_form').submit();
    });

    $('#<?php echo $name;?>-crud_form').validate({
        errorPlacement: function(error, element)
        {
            showPopover(element, error.html());
        },
        submitHandler: function(form)
        {
			<?php if (!empty($unique_fields))
			{
			?>
				$.ajax(
				{
					url:"<?php echo Backstage::gi()->portal_url;?>crud/validateUnique/",
					type: "POST",
					data: {
						form_values: JSON.stringify($(form).serializeJSON()),
						form_params: JSON.stringify($('#<?php echo $name;?>-crud_params_form').serializeJSON())
					},

					success:function(data)
					{
						if (data == 0)
							<?php echo $name;?>_submit_crud_form(form);
						else
							alert('The resource name is already busy.');
					},
					error: function (request, status, error)
					{
						console.log(request.responseText);
					}
				});
			<?php } else
				echo $name.'_submit_crud_form(form);'
			?>
        }
    });
    function <?php echo $name;?>_submit_crud_form(form)
    {

        for(var instanceName in CKEDITOR.instances)
		{
            CKEDITOR.instances[instanceName].updateElement();
		}

		//tinyMCE.triggerSave();
		$.ajax(
		{
			url: form.action,
			type: "POST",
			data: {
				form_values: JSON.stringify($(form).serializeJSON()),
				form_params: JSON.stringify($('#<?php echo $name;?>-crud_params_form').serializeJSON()),
				files: JSON.stringify(<?php echo $name;?>_files),
				additional_form: JSON.stringify($('#<?php echo $name;?>-crud_additional_form').serializeJSON())
			},

			success:function(data)
			{

		        $('#<?php echo $name;?>-crud_edit_modal').modal('hide');
				$('.modal-backdrop').remove();
		        <?php echo $name;?>_load();
			},
			error: function (request, status, error)
			{
				console.log(request.responseText);
			}
		});

    }

    $('.<?php echo $name;?>-crud_page').click(function(e)
    {
        $("#<?php echo $name;?>-crud_params_form #crud_current_page").val($(this).attr("data-page"));
        <?php echo $name;?>_load();
        e.preventDefault();
        return false;
    });

		// Search form
    $('#<?php echo $name;?>-crud_search_form').submit(function(e) {
        <?php echo $name;?>_load();
        e.preventDefault();
    });

    $('#<?php echo $name;?>-crud_search_clear').click(function(e) {
        <?php echo $name;?>_resetForm("#<?php echo $name;?>-crud_search_form");
        <?php echo $name;?>_load();
        e.preventDefault();
    });


		// Uploader form
	$('#<?php echo $name;?>-crud_form #uploader_materials').uploadify({
		'swf'      : '<?php echo Backstage::gi()->MATERIALS_URL; ?>temp/uploadify.swf',
		'uploader' : '<?php echo Backstage::gi()->MATERIALS_URL; ?>temp/uploadify.php?session_id=<?php echo session_id(); ?>',
		'multi'    : true,
		'wmode'      : 'transparent',
		'buttonImg': " ",
		'fileTypeExts' : '*.jpg; *.jpeg; *.png; *.gif',
		'fileTypeDesc' : 'JPG Image Files (*.jpg); JPEG Image Files (*.jpeg); PNG Image Files (*.png), GIF (*.gif)',
		'onUploadSuccess': function(file, data, response)
		{
			$('#<?php echo $name;?>-crud_form #uploader_materials_div').append('<span style="padding-right: 7px;"><img id="file_'+<?php echo $name;?>_file_num+'" height="100" src="<?php echo Backstage::gi()->MATERIALS_URL;?>/temp/files/<?php echo session_id(); ?>/'+file.name+'" /><a href="#dummy" file_num="'+<?php echo $name;?>_file_num+'" onclick="<?php echo $name;?>_deleteFile(0, this);"><span style="vertical-align: top;" class="glyphicon glyphicon-remove"></span></a></span>');
			<?php echo $name;?>_files[<?php echo $name;?>_file_num] = {id:0, name: file.name};
			<?php echo $name;?>_file_num++;
		}
	});

		// Count per page
    $('#<?php echo $name;?>-select_count_per_page').change(function(e){
        $("#<?php echo $name;?>-crud_params_form #crud_count_per_page").val($(this).val());
        <?php echo $name;?>_load();
        e.preventDefault();
    });
    $('#<?php echo $name;?>-select_count_per_page option[value="<?php echo $crud_count_per_page; ?>"]').prop('selected',true);

	<?php
	foreach ($fields as $field)
	{
		if (in_array($field['name'], $translations))
			echo "$('#".$field['table']."_".$field['name']."_tab a:first').tab('show');";
	}
	?>
});


// Get children by parent id
function <?php echo $name;?>_getChildren(id, obj)
{
	$("#<?php echo $name;?>-crud_params_form #crud_parent_id").val(id);
	<?php echo $name;?>_load();
};

function <?php echo $name;?>_deleteFile(file_id, obj)
{
	if (confirm("Siz əminsiz?"))
	{
		if (file_id > 0)
            $.ajax(
            {
                url:"<?php echo Backstage::gi()->portal_url;?>materials/deleteFile/",
                type: "DELETE",
                data: {id: file_id},
                success:function(data)
                {
					console.log(data);
                },
                error: function (request, status, error) {
                    console.log(request.responseText);
                }
            });
		$(obj).parent().remove();
		<?php echo $name;?>_files.splice(obj.file_num,1);
	}
}

function <?php echo $name;?>_resetForm(form)
{
	<?php echo $name;?>_current_id = 0;
	var field_types = {};
	var types = ['LONG', 'TINY'];
	<?php
		foreach ($fields as $field)
		{
			echo 'field_types["'.$field['name'].'"] = "'.$field['type'].'";';
		}
	?>

	$.each(field_types, function(el, value)
	{
		if ($.inArray(value, types) !== -1)
			$("#<?php echo $name;?>-crud_form input:text[name$=\\^"+el+"]").val('0');
		else
			$("#<?php echo $name;?>-crud_form input:text[name$=\\^"+el+"]").val('');
	});

	//$(form).find('input:text, input[type="hidden"], input:password, input:file').val('');
	$(form).find('select').prop('selectedIndex',0);
	$(form).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
	<?php echo $name;?>_file_num = 0;
	<?php echo $name;?>_files = [];
	$('#<?php echo $name;?>-crud_form #uploader_materials_div').html('');
}

// Export button
$(".<?php echo $name;?>-crud_export_btn").click(function(e) {
	data_clone = $('#<?php echo $name;?>_data').clone();
	console.log(data_clone);
	data_clone.find('input,button,select').remove();
	data_clone.find('a').contents().unwrap();
	//console.log($(data_clone).html());
    window.open('data:application/vnd.ms-excel;charset=UTF-8,\uFEFF' + encodeURIComponent($(data_clone).html()));
    e.preventDefault();
});

// Print button
$(".<?php echo $name;?>-crud_print_btn").click(function(e) {

	data_clone = $('#<?php echo $name;?>_data').clone();
	data_clone.find('input, button, select').remove();
	data_clone.find('a').contents().unwrap();
	data_clone.find('td, th').css('font-size', '10px');
	data_clone.find('td, th').css('border', '1px solid grey');
	data_clone.find('table').css('border-collapse', 'collapse');

	$(data_clone).print();
    e.preventDefault();
});

function <?php echo $name;?>_additionalFormOpen(obj, table)
{
	form_id = $(obj).val();
	row_id = <?php echo $name;?>_current_id;
	$.ajax(
	{
		url:"<?php echo Backstage::gi()->portal_url;?>forms/getFormValues/",
		type: "GET",
		data: "form_id="+form_id+"&row_id="+row_id+"&table_name="+table,
		success:function(data)
		{
			$('#<?php echo $name;?>-crud_additional_form_div').html(data);
			$('#<?php echo $name;?>-crud_additional_form_div .nav-tabs a:first').tab('show');
            $('input.datetime').datetimepicker({language:'en-gb'});
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}
	});

};



</script>
<div class="table-responsive">
    <?php if (!in_array('add', $restrictions)) { ?>
    <button type="button" data-id="0" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#<?php echo $name;?>-crud_edit_modal" class="<?php echo $name;?>-crud_add_btn btn btn-warning">
        <span class="glyphicon glyphicon-floppy-open"></span> <?php echo Translations::gi()->add_note; ?>
    </button>
    <?php } ?>
    <?php if (!in_array('export', $restrictions)) { ?>
    <button type="button" class="<?php echo $name;?>-crud_export_btn btn btn-info">
        <span class="glyphicon glyphicon-export"></span> Excel
    </button>
    <?php } ?>
	<?php if (!in_array('print', $restrictions)) { ?>
    <button type="button" class="<?php echo $name;?>-crud_print_btn btn btn-info">
        <span class="glyphicon glyphicon-print"></span> Print
    </button>
    <?php } ?>
    <?php if (!in_array('delete', $restrictions)) { ?>
    <button type="button" class="<?php echo $name;?>-crud_delete_all_btn btn btn-danger">
        <span class="glyphicon glyphicon-floppy-remove"></span> <?php echo Translations::gi()->delete; ?>
    </button>
    <?php } ?>
	<form name="<?php echo $name;?>-crud_search_form" id="<?php echo $name;?>-crud_search_form">
    <div id="<?php echo $name;?>_data">
    <table class="table table-hover">
        <?php
		$text_types = array("VAR_STRING", "FLOAT", "DOUBLE", "LONG", "TINYINT");
		$textarea_types = array("TEXT", "BLOB");
		$date_types = array("DATETIME", "DATE");

		// Header part of the table
		if (!empty($mapped_parents) && $parent_parent_id >= 0)
		{
			echo "<br/><a href='#dummy' onclick='".$name."_getChildren(\"".$parent_parent_id."\", this)'>< Back</a>";
		}
		echo '<thead><tr>';
			// Check all rows to be deleted
		echo '<th><input type="checkbox" data-target="'.$name.'" id="'.$name.'-crud_delete_all"/></th>';
		if (!empty($mapped_parents))
			echo '<th></th>';
		foreach ($fields as $field)
		{
			$data_order = '';
			$order_class = '';
			if ($order !== '' && preg_match('/(^|,)'.$field['name'].'[\s]+desc/', $order))
			{
				$data_order = 'desc';
				$order_class = 'glyphicon-sort-by-attributes-alt';
			}

			if ($order !== '' && preg_match('/(^|,)'.$field['name'].'[\s]+asc/', $order))
			{
				$data_order = 'asc';
				$order_class = 'glyphicon-sort-by-attributes';
			}
			if (!in_array($field['name'], $disabled_table_fields))
			{
				if (array_key_exists($field['name'], $titles))
					echo "<th><a href='#' class='$name-crud_header' data-column='{$field['name']}' data-order='$data_order'><span class='glyphicon $order_class'></span>{$titles[$field['name']]}</a></th>";
				else
					echo "<th><a href='#' class='$name-crud_header' data-column='{$field['name']}' data-order='$data_order'><span class='glyphicon $order_class'></span>{$field['name']}</a></th>";
			}
		}
		echo "<th></th>";
		echo '</tr></thead>';

		// Search panel
		if (isset($search))
		{
			echo '<tr>';
			echo '<td></td>';
			if (!empty($mapped_parents))
				echo '<td></td>';
			foreach ($fields as $field)
			{
				$value = '';
				$search_input = '';
				if (in_array($field['name'], $search) || $search[0] === '*')
				{
					if (isset($crud_search_form[$field['table'].'^'.$field['name']]))
						$value = $crud_search_form[$field['table'].'^'.$field['name']];

					if (array_key_exists($field['name'], $mapped_search))
					{
						$search_input_part1 = substr(base64_decode($mapped_search[$field['name']]), 0, strpos(base64_decode($mapped_search[$field['name']]), ':'));
						$search_input_part2 = substr(base64_decode($mapped_search[$field['name']]), strpos(base64_decode($mapped_search[$field['name']]), ':')+1);
						switch ($search_input_part1)
						{
							case 'select':
								$search_input = '<select id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" class="form-control input-sm">';
								$search_input .= '<option value="">-</option>';
								$select = json_decode($search_input_part2, true);
								foreach ($select as $option_value=>$option_desc)
								{
									$selected = '';
									if ($option_value === $value) $selected = 'selected';
									$search_input .= '<option value="'.$option_value.'" '.$selected.'>'.$option_desc.'</option>';
								}
								$search_input .= '</select>';
							break;
						}
					}
					else
						$search_input = '<input id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" value="'.$value.'" class="form-control input-sm" />';
				}
				if (!in_array($field['name'], $disabled_table_fields))
					echo '<td>'.$search_input.'</td>';
			}
			?>
			<td width="130px" class="text-center">
				<select name="search_condition">
					<option value="like" <?php if ($crud_search_condition === 'like') echo 'selected';?>><?php echo Translations::gi()->similar; ?></option>
					<option value="not like" <?php if ($crud_search_condition === 'not like') echo 'selected';?>><?php echo Translations::gi()->different; ?></option>
					<option value=">" <?php if ($crud_search_condition === '>') echo 'selected';?>><?php echo Translations::gi()->greater; ?></option>
					<option value="<" <?php if ($crud_search_condition === '<') echo 'selected';?>><?php echo Translations::gi()->greater; ?></option>
				</select>
                <br>
				<button type="submit" class="btn btn-info btn-sm">
					<span class="glyphicon glyphicon-search"></span>
				</button>
				<button type="reset" class="btn btn-danger btn-sm" id="<?php echo $name;?>-crud_search_clear">
					<span class="glyphicon glyphicon-remove"></span>
				</button>
			</td>
			<?php
			echo '</tr>';
		}

                // Rows
		foreach ($rows as $row)
		{
			$format_class = '';
			// Set CSS formatting by rules
			foreach ($format_rules as $rule_key=>$rule_val)
			{
				$rule_key_parts = preg_split("/([<=|>=|<|>|=]+)/", $rule_key, -1, PREG_SPLIT_DELIM_CAPTURE);
				switch (trim($rule_key_parts[1]))
				{
					case '>':
						if ($row->{trim($rule_key_parts[0])} > trim($rule_key_parts[2]))
							$format_class = $rule_val;
					break;
					case '<':
						if ($row->{trim($rule_key_parts[0])} < trim($rule_key_parts[2]))
							$format_class = $rule_val;
					break;
					case '>=':
						if ($row->{trim($rule_key_parts[0])} >= trim($rule_key_parts[2]))
							$format_class = $rule_val;
					break;
					case '<=':
						if ($row->{trim($rule_key_parts[0])} <= trim($rule_key_parts[2]))
							$format_class = $rule_val;
					break;
					case '=':
						if ($row->{trim($rule_key_parts[0])} == trim($rule_key_parts[2]))
							$format_class = $rule_val;
					break;
				}
			}

			echo "<tr id='row_".$row->{$ids[0]}."' class='$format_class'>";

				// Check for delete
			foreach ($fields as $field)
				if (strtoupper($field['name']) === strtoupper($ids[0]))
				{
					echo '<td><input type="checkbox" data-table="'.$field['table'].'" data-id="'.$row->{$ids[0]}.'" id="crud_delete_'.$row->{$ids[0]}.'" value="1"/></td>';
					break;
				}
				// Parent open cell
			if (!empty($mapped_parents))
				echo "<td class='text-center'><a href='#dummy' id='crud_parent_".$row->{key($mapped_parents)}."' onclick='".$name."_getChildren(\"".$row->{key($mapped_parents)}."\", this)'>+ (COUNT:".$row->child_count.")</a></td>";
			foreach ($fields as $field)
			{
					// Field cells
				if (!in_array($field['name'], $disabled_table_fields))
				{
					if (is_array($row->{$field['name']}))
						echo "<td>".$row->{$field['name']}[0]->translation."</td>";
					else
					{
						if (array_key_exists($field['name'], $mapped_values_f))
						{
		                    $functionp = explode("#", (base64_decode($mapped_values_f->{$field['name']})));
	                        $function = create_function($functionp[0], $functionp[1]);
	                        $value = $function($row->{$field['name']});
						}
						elseif (array_key_exists($field['name'], $mapped_values) && array_key_exists($row->{$field['name']}, $mapped_values[$field['name']])) 
						{
	                        $value = $mapped_values[$field['name']][$row->{$field['name']}];
						}
						else
	                        $value = $row->{$field['name']};

						if (array_key_exists($field['name'], $links))
							echo "<td><a href='#dummy' id='crud_link_".$row->{$ids[0]}."' onclick='".$links[$field['name']]."(".$row->{$ids[0]}.", \"".$value."\", this)'>".$value."</a></td>";
						else
							echo "<td>".$value."</td>";
					}
				}
			}
			echo '<td width="130px">';
			if (!in_array('view', $restrictions))
			{
				// View button
				echo '<button type="button" data-id="'.$row->{$ids[0]}.'" data-toggle="modal" data-target="#'.$name.'-crud_view_modal" class="'.$name.'-crud_view_btn btn btn-info btn-xs">';
				echo '<span class="glyphicon glyphicon-floppy-disk"></span>';
				echo '</button>';
			}
			if (!in_array('edit', $restrictions))
			{
				// Edit button
				echo '<button type="button" data-id="'.$row->{$ids[0]}.'" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#'.$name.'-crud_edit_modal" class="'.$name.'-crud_edit_btn btn btn-warning btn-xs">';
				echo '<span class="glyphicon glyphicon-floppy-save"></span>';
				echo '</button>';
			}
			if (!in_array('delete', $restrictions))
			{
				// Delete button
				echo '<button type="button" data-id="'.$row->{$ids[0]}.'" class="'.$name.'-crud_delete_btn btn btn-danger btn-xs">';
				echo '<span class="glyphicon glyphicon-floppy-remove"></span>';
				echo '</button>';
			}
				// Custom buttons
			foreach ($buttons as $button => $button_icon)
			{
				echo '<button type="button" data-id="'.$row->{$ids[0]}.'" onclick="'.$button.'('.$row->{$ids[0]}.', this)" class="'.$name.'-crud_'.$button.'_btn btn btn-info btn-xs">';
				echo '<span class="'.$button_icon.'"></span>';
				echo '</button>';
			}
			echo '</td>';
			echo '</tr>';
		}
                if (!empty($totals))
                {
                    echo '<tr>';
                    foreach ($fields as $field)
                    {
                        if (array_key_exists($field['name'], $totals))
                            echo '<td><b>'.$totals[$field['name']].'</b></td>';
                        else
                            echo '<td></td>';
                    }
                    echo '<td></td></tr>';
                }
		?>
    </table>
    </div>
	</form>
        <div>
	<?php // Pagination part
				echo '<div class="col-md-1"><b>Total count: '.$crud_total_count.'</b></div>';

                echo '<div class="col-xs-2"><select id="'.$name.'-select_count_per_page" name="'.$name.'-select_count_per_page" class="form-control">';
                echo '<option value="5">5</option>';
                echo '<option value="10">10</option>';
                echo '<option value="50">50</option>';
                echo '<option value="0">Bütün</option>';
                echo '</select></div>';
		if ($crud_pages_count > 1)
		{
			echo '<div class="col-md-10"><ul class="pagination" style="margin: 0 0;">';
			if ($crud_current_page != 1)
				echo '<li><a href="#" data-page="1" class="'.$name.'-crud_page">&laquo;</a></li>';
			for ($i=1;$i<=$crud_pages_count;$i++)
			{
				if ($i == $crud_current_page)
					echo '<li class="active"><a href="#" data-page="'.$i.'" class="'.$name.'-crud_current_page">'.$i.' <span class="sr-only">(current)</span></a></li>';
				else
					echo '<li><a href="#" data-page="'.$i.'" class="'.$name.'-crud_page">'.$i.'</a></li>';
			}
			if ($crud_current_page != $i-1)
				echo '<li><a href="#" data-page="'.($i-1).'" class="'.$name.'-crud_page">&raquo;</a></li>';
			echo '</ul></div>';
		}
	?>
        </div>
</div>

<!-- Modal edit -->
<div class="modal fade" id="<?php echo $name;?>-crud_edit_modal" tabindex="-1" role="dialog" aria-labelledby="edit_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="edit_label">Redakte etmek</h4>
      </div>
      <div class="modal-body">
          <form name="<?php echo $name;?>-crud_form" id="<?php echo $name;?>-crud_form" role="form" class="form-horizontal" action="<?php echo Backstage::gi()->portal_url;?>crud/save/" method="post">
          <?php
				foreach ($fields as $field)
                {
                    if ( $field['name'] == 'child_count'){
                        continue;
                    }
                    $readonly = '';
                    $hidden = '';
                    $ckeditor = '';
                    $style = '';
                    $js_handler = '';

                    if (in_array($field['name'],$ids) || in_array($field['name'], $disabled_edit_fields))
                        $readonly = 'readonly';

                    if (in_array($field['name'], $hidden_edit_fields))
                        $style .= 'visibility:hidden; ';

                    if (in_array($field['name'],$ids) || in_array($field['name'], $add_editor_list))
                        $ckeditor = 'ckeditor_w';

                    if (array_key_exists($field['name'], $js_handlers))
                        $js_handler = $js_handlers[$field['name']]['event'].'="'.$js_handlers[$field['name']]['handler'].'(this)"';

					if (strtoupper($field['name']) === strtoupper($additional_form_field))
                        $js_handler = 'onchange="'.$name.'_additionalFormOpen(this, \''.$additional_form_table.'\');"';

					if (array_key_exists($field['name'], $form_fields_dimensions))
					{
						$dims = explode(',',$form_fields_dimensions[$field['name']]);
                        $style .= "width: ".trim($dims[0])."px; height: ".trim($dims[1])."px;";
					}



                    echo '<div class="form-group">';


					if (!in_array($field['name'], $hidden_edit_fields))
					{
						if (array_key_exists($field['name'], $titles))
							echo '<label for="'.$field['name'].'" class="col-sm-2 control-label">'.$titles[$field['name']].'</label>';
						else
							echo '<label for="'.$field['name'].'" class="col-sm-2 control-label">'.$field['name'].'</label>';
                    }

                    echo '<div class="col-xs-9">';
                    if (array_key_exists($field['name'], $mapped_field_inputs))
                    {

                        $field_input_part1 = substr(base64_decode($mapped_field_inputs[$field['name']]), 0, strpos(base64_decode($mapped_field_inputs[$field['name']]), ':'));
                        $field_input_part2 = substr(base64_decode($mapped_field_inputs[$field['name']]), strpos(base64_decode($mapped_field_inputs[$field['name']]), ':')+1);

						if ($readonly == 'readonly')
							$readonly = 'disabled';
                        switch ($field_input_part1)
                        {
                            case 'select':
                                echo '<select id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" class="form-control input-sm" '.$readonly.' style="'.$style.'" '.$js_handler.'>';
                                echo '<option value="0" selected>-</option>';
                                $select = json_decode($field_input_part2, true);

                                foreach ($select as $option_value=>$option_desc)
                                {
                                    $selected = '';
                                    //echo $value;
                                    //if ($option_value === $value) echo 'selected';
                                    echo '<option value="'.$option_value.'" '.$selected.'>'.$option_desc.'</option>';
                                }
                                echo '</select>';
							break;
							case 'checkbox':
                                echo '<input type="hidden" name="'.$field['table'].'^'.$field['name'].'" value="0">';
                                echo '<input type="checkbox" id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" '.$readonly.' style="'.$style.'" value="'.$field_input_part2.'" '.$js_handler.'>';
							break;
                        }
                    }
					elseif (in_array($field['name'], $translations))	// Translations for the field are set
                    {

						$langs = explode(',', Backstage::gi()->portal_langs);
						echo '<ul class="nav nav-tabs" id="'.$field['table'].'_'.$field['name'].'_tab">';
						foreach ($langs as $key => $lang)
						{
							echo "<li><a href='#".$field['table'].'_'.$field['name']."_".$lang."' data-toggle='tab'>".$lang."</a></li>";
						}
						echo '</ul>';
                        echo '<div class="tab-content">';

						foreach ($langs as $key => $lang)
						{
                            // LQ Button For Translations
                            if (in_array($field['name'], $add_lq_button)){
                                $buttonLQ = '<button type="button" class="addLQbtn btn btn-primary btn-sm" rel="'.$field['table'].'^'.$field['name'].'['.($lang).']" alt="' . $field['type'] . '">Add LQ</button>';
                            } else {
                                $buttonLQ = '';
                            }


							echo "<div class='tab-pane' id='".$field['table'].'_'.$field['name']."_". $lang."'>";
							if (in_array(strtoupper($field['type']), $textarea_types))
								echo '<textarea id="'.$field['table'].'-'.$field['name'].'_'.$lang.'" name="'.$field['table'].'^'.$field['name'].'['.($lang).']" class="form-control input-sm '.$ckeditor.'" '.$readonly.' style="'.$style.'" '.$js_handler.'></textarea>' . $buttonLQ;
							elseif (in_array(strtoupper($field['type']), $text_types))
								echo '<input id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'['.($lang).']" class="form-control input-sm ui-autocomplete-input" '.$readonly.' style="'.$style.'" '.$js_handler.'/>' . $buttonLQ;
							elseif (in_array(strtoupper($field['type']), $date_types))
								echo '<input id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'['.($lang).']" class="datetime form-control input-sm" value="'.date('yyyy-mm-dd hh24:mi:ss').'" '.$readonly.' style="'.$style.'"/ '.$js_handler.'>';
							else
								echo '<input id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'['.($lang).']" class="form-control input-sm" '.$readonly.' style="'.$style.'" '.$js_handler.'/>';

							echo '</div>';
						}
                        echo '</div>';
					}
					else
					{
                        // LQ Button
                        if (in_array($field['name'], $add_lq_button)){
                            $buttonLQ = '<button type="button" class="addLQbtn btn btn-primary btn-sm" rel="'.$field['table'].'^'.$field['name'].'" alt="' . $field['type'] . '">Add LQ</button>';
                        } else {
                            $buttonLQ = '';
                        }

						if (array_key_exists($field['name'], $mapped_passwords))
							echo '<input type="password" id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" class="form-control input-sm" '.$readonly.' style="'.$style.'" '.$js_handler.'/>';
						elseif (in_array(strtoupper($field['type']), $textarea_types))
							echo '<textarea id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" class="form-control input-sm '.$ckeditor.'" '.$readonly.' style="'.$style.'" '.$js_handler.'></textarea>' . $buttonLQ;
						elseif (in_array(strtoupper($field['type']), $text_types))
							echo '<input id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" class="form-control input-sm ui-autocomplete-input" '.$readonly.' style="'.$style.'" '.$js_handler.'/>' . $buttonLQ;
						elseif (in_array(strtoupper($field['type']), $date_types))
							echo '<input id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" class="datetime form-control input-sm" '.$readonly.' style="'.$style.'" '.$js_handler.'/>';
						else
							echo '<input id="'.$field['table'].'-'.$field['name'].'" name="'.$field['table'].'^'.$field['name'].'" class="form-control input-sm" '.$readonly.' style="'.$style.'" '.$js_handler.'/>';
					}
                    echo '</div>';
                    echo '<div class="form_hint"><span class="validation"></span></div>';
                    echo '</div>';
                }
					// Materials uploader
				if (!empty($uploader_object_type))
				{
					?>
					<div class="form-group">
						<label class="col-sm-2 control-label">Uploaded files</label>
						<div class="col-xs-9">
							<div id="uploader_materials_div"></div>
							<br/>
							<input type="file" id="uploader_materials" name="uploader_materials" multiple="multiple" style="visibility:hidden;"/>
						</div>
					</div>
					<?php
				}
          ?>
			</form>
			<form name="<?php echo $name;?>-crud_additional_form" id="<?php echo $name;?>-crud_additional_form" role="form" class="form-horizontal" method="post">
				<div id="<?php echo $name;?>-crud_additional_form_div">
				</div>
			</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Translations::gi()->close; ?></button>
        <button type="button" class="btn btn-primary" id="<?php echo $name;?>-crud_save_btn"><?php echo Translations::gi()->save; ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal view -->
<div class="modal fade" id="<?php echo $name;?>-crud_view_modal" tabindex="-1" role="dialog" aria-labelledby="view_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="view_label">Baxmaq</h4>
      </div>
      <div class="modal-body">
		<dl class="dl-horizontal">
		<?php
			foreach ($fields as $field)
			{
				if (array_key_exists($field['name'], $titles))
					echo '<dt>'.$titles[$field['name']].':</dt> <dd class="clearfix"><span id="'.$name.'-crud_view_'.$field['name'].'"></span></dd>';
				else
					echo '<dt>'.$field['name'].':</dt> <dd class="clearfix"><span id="'.$name.'-crud_view_'.$field['name'].'"></span></dd>';
			}
		?>
		</dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Translations::gi()->close; ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal view -->
<div class="modal fade" id="insertLQModal" tabindex="-1" role="dialog" aria-labelledby="view_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="view_label">Add LQ</h4>
            </div>
            <div class="modal-body">
                <div role="tabpanel">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="lqTypes" class="active"><a href="#LQcontainer" aria-controls="LQcontainer" role="tab" data-toggle="tab">Container</a></li>
                        <li role="lqTypes"><a href="#LQmodule" aria-controls="LQmodule" role="tab" data-toggle="tab">Module</a></li>
                        <li role="lqTypes"><a href="#LQTranslation" aria-controls="LQTranslation" role="tab" data-toggle="tab">Translation</a></li>
                        <li role="lqTypes"><a href="#LQconstant" aria-controls="LQconstant" role="tab" data-toggle="tab">Constant</a></li>
                        <li role="lqTypes"><a href="#LQparameter" aria-controls="LQparameter" role="tab" data-toggle="tab">Parameter</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content lqBlocks">
                        <div role="tabpanel" class="tab-pane active" id="LQcontainer">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="containerName" class="col-sm-2 control-label">Container name</label>
                                    <div class="col-sm-7">
                                        <input type="containerName" class="form-control" id="containerName" placeholder="Name">
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="getLQ btn btn-primary" rel="container">Get LQ</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="LQmodule">
                            <b>Select module</b>
                            <?php
                                $modulesArray = array('Contents' => 'contents', 'Catalogs' => 'catalogs');
                                foreach ($modulesArray as $moduleName=>$moduleID){
                                    echo '<div class="radio">
                                                <label>
                                                    <input type="radio" class="moduleType" name="moduleType" value="'.$moduleID.'">
                                                    '.$moduleName.'
                                                </label>
                                            </div>';
                                }
                            ?>
                            <div class="form-group" style="display:none;">
                                <label for="moduleName">Select module name</label>
                                <select id="moduleName" class="moduleName form-control" name="moduleName">
                                </select>
                            </div>
                            <div class="form-group" style="display:none;">
                                <label for="catalogItems">Select catalog items (or leave it empty if you want to show all)</label>
                                <select multiple id="catalogItems" class="catalogItems form-control" name="catalogItems">
                                </select>
                            </div>
                            <input type="hidden" class="actionModule" id="actionModule">
                            <div class="form-group" style="display:none;">
                                <label for="designName">Select design</label>
                                <select id="designName" class="designName form-control" name="designName">
                                </select>
                            </div>
                            <div class="form-group" style="display:none;">
                                <label for="materialDesignName">Select material design</label>
                                <select id="materialDesignName" class="materialDesignName form-control" name="materialDesignName">
                                </select>
                            </div>
                            <div class="form-group" style="display:none;">
                                <div class="col-sm-6">
                                    <label for="countNavigation" class="control-label">Items per page</label>
                                    <input type="countNavigation" class="form-control countNavigation" id="countNavigation" placeholder="10">
                                </div>
                                <div class="col-sm-6">
                                    <label for="countNavigation" class="control-label">Navigation design</label>
                                    <select id="navigationDesignName" class="navigationDesignName form-control" name="navigationDesignName">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="display:none;">
                                <br><br><br><br>
                                <label for="materialDesignName">Write container name (or create it)</label>
                                    <div class="input-group">
                                      <span class="input-group-addon">
                                        Create <input type="checkbox" class="containerIsExist">
                                      </span>
                                    </div><!-- /input-group -->
                                <input type="text" class="containerName form-control" id="containerName" name="containerName">
                            </div>
                            <div class="col-sm-12 text-center">
                                <button type="button" class="getLQ btn btn-primary" rel="module">Get LQ</button>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="LQTranslation">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="translationName" class="col-sm-2 control-label">Translation name</label>
                                    <div class="col-sm-7">
                                        <input type="translationName" class="form-control" id="translationName" placeholder="Name">
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="getLQ btn btn-primary" rel="translation">Get LQ</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="LQconstant">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="constantName" class="col-sm-2 control-label">Constant name</label>
                                    <div class="col-sm-7">
                                        <input type="constantName" class="form-control" id="constantName" placeholder="Constant">
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="getLQ btn btn-primary" rel="constant">Get LQ</button>
                                    </div>
                                    <div class="col-sm-12">
                                        <br><br>
                                        <?php
                                        $constantsArray = array(
                                            '$portal_url' => 'Portal URL',
                                            '$TEMPLATE_URL' => 'Template directory URL',
                                            '$EXTERNAL_URL' => 'External directory URL',
                                            'navigation' => 'Pagination (for catalogs)',
                                            'pnum' => 'Number of page, pagination (for catalogs)',
                                            'structure' => 'Structure of block',
                                            'files' => 'Get all files',
                                            'first_file' => 'Get first file'
                                        )
                                        ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th>LQ</th>
                                                    <th>Description</th>
                                                </tr>
                                                <?php
                                                foreach($constantsArray as $constantName=>$constantDesc){
                                                    echo '<tr>
                                                                <td style="line-height:0.5px"><a class="lqRowConstants" href="#">[[' . $constantName . ']]</a></td>
                                                                <td style="line-height:0.5px">' . $constantDesc . '</td>
                                                            </tr>';                                                }
                                                ?>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="LQparameter">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="parameterName" class="col-sm-2 control-label">Parameter name</label>
                                    <div class="col-sm-7">
                                        <input type="parameterName" class="form-control" id="parameterName" placeholder="Parameter">
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="getLQ btn btn-primary" rel="parameter">Get LQ</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="text-center">
                        <a class="lqRow" href="#"></a>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
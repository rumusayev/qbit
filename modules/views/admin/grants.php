<?php
require_once('header.php');
echo '<h1>Grants</h1>';
echo '<hr/>';
echo '<h3>Grant resource types</h3>';
echo $crud_grant_resource_types;
echo '<br/><br/><hr/>';
echo '<h3>For users</h3>';
echo $crud_grants;
echo '<br/><br/><hr/>';
echo '<h3>For roles</h3>';
echo $crud_roles;

require_once('footer.php');
?>
<script type="text/javascript" charset="utf-8">
function getUserGrants(id, obj)
{
	$.ajax(
	{
		url:"<?php echo Backstage::gi()->portal_url;?>grants/getUserGrants/",
		type: "GET",
		data: "user_id="+id,
		success:function(data)
		{
			$('#grants_edit_modal').modal();
			$('#grants_edit_modal_body').html(data);
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}                        
	}); 	
}

function getUserResourceGrants(id, obj)
{
	$.ajax(
	{
		url:"<?php echo Backstage::gi()->portal_url;?>grants/getUserResourceGrants/",
		type: "GET",
		data: "object_id="+id,
		success:function(data)
		{
			$('#grants_edit_modal').modal();
			$('#grants_edit_modal_body').html(data);
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}                        
	}); 	
}

function getRoleGrants(id, obj)
{
	$.ajax(
	{
		url:"<?php echo Backstage::gi()->portal_url;?>grants/getRoleGrants/",
		type: "GET",
		data: "role_id="+id,
		success:function(data)
		{
			$('#grants_edit_modal').modal();
			$('#grants_edit_modal_body').html(data);
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}                        
	}); 	
}

function getRoleResourceGrants(id, obj)
{
	$.ajax(
	{
		url:"<?php echo Backstage::gi()->portal_url;?>grants/getRoleResourceGrants/",
		type: "GET",
		data: "object_id="+id,
		success:function(data)
		{
			$('#grants_edit_modal').modal();
			$('#grants_edit_modal_body').html(data);
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}                        
	}); 	
}

function getUserActionsGrants(id, obj)
{
	$.ajax(
	{
		url:"<?php echo Backstage::gi()->portal_url;?>grants/getActionsGrants/",
		type: "GET",
		data: "object_id="+id+"&object_type=user",
		success:function(data)
		{
			$('#grants_edit_modal').modal();
			$('#grants_edit_modal_body').html(data);
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}                        
	}); 	
}

function getRoleActionsGrants(id, obj)
{
	$.ajax(
	{
		url:"<?php echo Backstage::gi()->portal_url;?>grants/getActionsGrants/",
		type: "GET",
		data: "object_id="+id+"&object_type=role",
		success:function(data)
		{
			$('#grants_edit_modal').modal();
			$('#grants_edit_modal_body').html(data);
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}                        
	}); 	
}

$(function()
{
    $('#grants_save_btn').click(function(e) 
	{
		$.ajax(
		{
			url:"<?php echo Backstage::gi()->portal_url;?>grants/saveGrants/",
			type: "PUT",
			data: {
				params_form: JSON.stringify($('#params_form').serializeJSON()), 
				roles_form: JSON.stringify($('#roles_form').serializeJSON()), 
                grants_form: JSON.stringify($('#grants_form').serializeJSON())
			},
			success:function(data)
			{
				console.log(data);
				
				$('#grants_edit_modal').modal('hide');
				$('.modal-backdrop').remove();
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}                        
		});
    });
});

</script>
<!-- Modal view -->
<div class="modal fade" id="grants_edit_modal" tabindex="-1" role="dialog" aria-labelledby="view_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="view_label">Baxmaq</h4>
      </div>
      <div class="modal-body" id="grants_edit_modal_body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Bağlamaq</button>
        <button type="button" class="btn btn-primary" id="grants_save_btn">Yaddaşa vermək</button>		
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
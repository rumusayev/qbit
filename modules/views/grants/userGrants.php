<?php
echo '<form name="params_form" id="params_form" method="post">';
echo '<input type="hidden" name="type" id="type" value="user"/>';
echo '<input type="hidden" name="object_id" id="object_id" value="'.$request->parameters['user_id'].'"/>';
echo '</form>';

echo '<h2>Roles:</h2>';
echo '<form name="roles_form" id="roles_form" role="form" class="form-horizontal" method="post">';
foreach ($roles as $role)
{
	echo '<div class="form-group">';	
	echo '<label for="role_'.$role->id.'" class="col-sm-2 control-label">'.$role->role_name.'</label>';
	$checked = '';
	if ($role->is_checked == 1)
		$checked = 'checked';
	echo '<div class="col-xs-9">';		
	echo '<input type="checkbox" id="role_'.$role->id.'" name="role['.$role->id.']" '.$checked.' value="1" />';
	echo '</div>';
	echo '</div>';
}
echo '</form>';
echo '<hr/>';

echo '<h2>Module grants:</h2>';
echo '<form name="grants_form" id="grants_form" role="form" class="form-horizontal" method="post">';
echo '<table class="table table-hover">';
echo '<thead><tr>';
echo '<th>Grant name</th><th>Create</th><th>Read</th><th>Update</th><th>Delete</th>';
echo '</tr></thead>';

foreach ($grants as $grant_key=>$grant)
{
	echo '<tr>';
	echo '<td>'.$grant_key.'</td>';

	$checked = '';
	if ($grant['POST']->is_checked == 1)
		$checked = 'checked';
	echo '<td><input type="checkbox" id="grant_'.$grant['POST']->id.'" name="grant['.$grant['POST']->id.']" '.$checked.' value="1" /></td>';
	$checked = '';
	if ($grant['GET']->is_checked == 1)
		$checked = 'checked';
	echo '<td><input type="checkbox" id="grant_'.$grant['GET']->id.'" name="grant['.$grant['GET']->id.']" '.$checked.' value="1" /></td>';
	$checked = '';
	if ($grant['PUT']->is_checked == 1)
		$checked = 'checked';
	echo '<td><input type="checkbox" id="grant_'.$grant['PUT']->id.'" name="grant['.$grant['PUT']->id.']" '.$checked.' value="1" /></td>';
	$checked = '';
	if ($grant['DELETE']->is_checked == 1)
		$checked = 'checked';
	echo '<td><input type="checkbox" id="grant_'.$grant['DELETE']->id.'" name="grant['.$grant['DELETE']->id.']" '.$checked.' value="1" /></td>';

	echo '</tr>';
}
echo '</table>';
echo '<hr/>';

echo '<h2>Catalogs:</h2>';
echo '<div class="row"><div class="col-md-4"><b>Catalog name</b></div><div class="col-md-2"><b>Create</b></div><div class="col-md-2"><b>Read</b></div><div class="col-md-2"><b>Update</b></div><div class="col-md-2"><b>Delete</b></div></div>';
echo $catalogs_grants;
echo '<hr/>';
echo '<h2>Contents:</h2>';
echo '<div class="row"><div class="col-md-4"><b>Content name</b></div><div class="col-md-2"><b>Create</b></div><div class="col-md-2"><b>Read</b></div><div class="col-md-2"><b>Update</b></div><div class="col-md-2"><b>Delete</b></div></div>';
echo $contents_grants;
echo '<hr/>';
echo '</form>';

?>
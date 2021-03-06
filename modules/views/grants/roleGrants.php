<?php
echo '<form name="params_form" id="params_form" method="post">';
echo '<input type="hidden" name="type" id="type" value="role"/>';
echo '<input type="hidden" name="resource_type" id="resource_type" value="modules"/>';
echo '<input type="hidden" name="object_id" id="object_id" value="'.$request->parameters['role_id'].'"/>';
echo '</form>';

echo '<form name="roles_form" id="roles_form" role="form" class="form-horizontal" method="post">';
echo '</form>';

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
echo '</form>';

?>
<form name="params_form" id="params_form" method="post">
<input type="hidden" name="type" id="type" value="<?php echo $request->parameters['object_type']; ?>"/>
<input type="hidden" name="resource_type" id="resource_type" value="resources"/>
<input type="hidden" name="object_id" id="object_id" value="<?php echo $request->parameters['object_id']; ?>"/>
</form>
<form name="roles_form" id="roles_form" role="form" class="form-horizontal" method="post"></form>

<h1>Actions grants</h1>
<form name="grants_form" id="grants_form" role="form" class="form-horizontal" method="post">
<table class="table table-hover">
<thead><tr><th>Resource name</th><th>Grant types</th></tr></thead>
<?php
	foreach ($grants as $grant)
	{
		echo '<tr>';
		echo '<td>'.$grant->resource_name.'</td>';
		echo '<td><input type="checkbox" id="grant_'.$grant->id.'" name="grant['.$grant->id.']" value="1" />'.$grant->grant_type.'</td>';
		echo '</tr>';
	}
?>
</table>
</form>
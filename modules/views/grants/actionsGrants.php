<form name="actions_form" id="actions_form" role="form" class="form-horizontal" method="post">
<table class="table table-hover">
<thead><tr><th>Resource name</th><th>Grant types</th></tr></thead>
<?php
foreach($structure as $resource_name=>$grant_types)
{
	echo '<tr><td>'.$resource_name.'</td>';
	echo '<td>';
	foreach ($grant_types as $grant_type)
	{
		echo '<div><input type="checkbox" id="grant_" name="grant[]" value="1" />'.$grant_type.'</div>';
	}
	echo '</td>';
	echo '</tr>';
}
?>
</table>
</form>
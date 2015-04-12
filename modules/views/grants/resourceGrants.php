<?php
echo '<form name="params_form" id="params_form" method="post">';
echo '<input type="hidden" name="type" id="type" value="'.$object_type.'"/>';
echo '<input type="hidden" name="resource_type" id="resource_type" value="resources"/>';
echo '<input type="hidden" name="object_id" id="object_id" value="'.$request->parameters['object_id'].'"/>';
echo '</form>';

echo '<form name="grants_form" id="grants_form" role="form" class="form-horizontal" method="post">';
echo '<h2>Resources:</h2>';
echo '<div class="row"><div class="col-md-4"><b>Resource name</b></div><div class="col-md-2"><b>Create</b></div><div class="col-md-2"><b>Read</b></div><div class="col-md-2"><b>Update</b></div><div class="col-md-2"><b>Delete</b></div></div>';
echo $grants;
echo '</form>';

?>
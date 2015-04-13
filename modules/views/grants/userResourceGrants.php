<?php
echo '<form name="params_form" id="params_form" method="post">';
echo '<input type="hidden" name="type" id="type" value="user"/>';
echo '<input type="hidden" name="object_id" id="object_id" value="'.$request->parameters['user_id'].'"/>';
echo '</form>';

echo '<h2>Module grants:</h2>';
echo '<form name="grants_form" id="grants_form" role="form" class="form-horizontal" method="post">';
echo '<div class="row"><div class="col-md-4"><b>Name</b></div><div class="col-md-2"><b>Create</b></div><div class="col-md-2"><b>Read</b></div><div class="col-md-2"><b>Update</b></div><div class="col-md-2"><b>Delete</b></div></div>';
echo $grants;
echo '</form>';


?>
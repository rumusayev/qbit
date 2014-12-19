<?php

	echo '<li class="list-group-item">';
	// If this is an object then it has at least one CRUD item
	echo '<div class="row">';
	echo '<div class="col-md-4">'.$grant['resource_name'].'</div>';
	
	$checked = '';
	if ($grant['POST']->is_checked == 1)
		$checked = 'checked';
	echo '<div class="col-md-2"><input type="checkbox" id="grant_'.$grant['POST']->id.'" name="grant['.$grant['POST']->id.']" '.$checked.' value="1" /></div>';
	$checked = '';
	if ($grant['GET']->is_checked == 1)
		$checked = 'checked';
	echo '<div class="col-md-2"><input type="checkbox" id="grant_'.$grant['GET']->id.'" name="grant['.$grant['GET']->id.']" '.$checked.' value="1" /></div>';
	$checked = '';
	if ($grant['PUT']->is_checked == 1)
		$checked = 'checked';
	echo '<div class="col-md-2"><input type="checkbox" id="grant_'.$grant['PUT']->id.'" name="grant['.$grant['PUT']->id.']" '.$checked.' value="1" /></div>';
	$checked = '';
	if ($grant['DELETE']->is_checked == 1)
		$checked = 'checked';
	echo '<div class="col-md-2"><input type="checkbox" id="grant_'.$grant['DELETE']->id.'" name="grant['.$grant['DELETE']->id.']" '.$checked.' value="1" /></div>';
	
	echo '</div>';
	echo '</li>';
?>
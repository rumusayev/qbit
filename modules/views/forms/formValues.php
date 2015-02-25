<?php
foreach ($items as $key=>$item)
{
					
	echo '<div class="form-group">';
	echo '<label for="'.$item->field_name.'" class="col-sm-2 control-label">'.$item->field_title.'</label>';
	echo '<div class="col-xs-9">';
	
	echo '<input type="hidden" id="fieldid_'.$item->field_name.'_'.$item->id.'" name="fieldid_'.$item->field_name.'_'.$item->id.'" class="form-control input-sm" value="'.$item->id.'"/>';
	echo '<input type="hidden" id="valueid_'.$item->field_name.'_'.$item->value_id.'" name="valueid_'.$item->field_name.'_'.$item->value_id.'" class="form-control input-sm" value="'.$item->value_id.'"/>';

	if (is_array($item->value))	// Translations for the field are set
	{
		echo '<ul class="nav nav-tabs" id="'.$form_type.$item->field_name.'_tab">';
		foreach ($item->value as $translation) 
		{
			echo "<li><a href='#".$form_type.$item->field_name."_".$translation->short."' data-toggle='tab'>".$translation->short."</a></li>";
		}
		echo '</ul>';
		echo '<div class="tab-content">';

		foreach ($item->value as $translation) 
		{
			echo "<div class='tab-pane' id='".$form_type.$item->field_name."_".$translation->short."'>";
			// Field type handling
			switch ($item->type_name)
			{
				case 'text':
					echo '<input type="text" id="'.$item->field_name.'" name="'.$item->field_name.'['.$translation->short.']" class="form-control input-sm" value="'.$translation->translation.'"/>';
				break;
				case 'textarea':
					echo '<textarea id="'.$item->field_name.'" name="'.$item->field_name.'['.$translation->short.']" class="form-control input-sm">'.$translation->translation.'</textarea>';
				break;
				case 'button':
					echo '<button id="'.$item->field_name.'" name="'.$item->field_name.'['.$translation->short.']" class="btn">'.$translation->translation.'</button>';
				break;
			}
			echo '</div>';
		}
		echo '</div>';
	}
	else
		switch ($item->type_name)
		{
			case 'text':
                if (isset($item->datetime) && $item->datetime == 1){
                    echo '<input type="text" id="'.$item->field_name.'" name="'.$item->field_name.'" class="datetime form-control input-sm" value="'.$item->value.'"/>';
                } else {
                    echo '<input type="text" id="'.$item->field_name.'" name="'.$item->field_name.'" class="form-control input-sm" value="'.$item->value.'"/>';
                }
			break;
			case 'textarea':
				echo '<textarea id="'.$item->field_name.'" name="'.$item->field_name.'" class="form-control input-sm">'.$item->value.'</textarea>';
			break;
			case 'button':
				echo '<button id="'.$item->field_name.'" name="'.$item->field_name.'" class="btn">'.$item->value.'</button>';
			break;
		}		
	echo '</div>';
	echo '</div>';
}
?>
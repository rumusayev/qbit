<?php

	echo '<tr id="row_'.$field->num.'">';
?>
	<td><input type="checkbox" data-id="<?php echo $field->id; ?>" data-num="<?php echo $field->num; ?>" id="fields_delete_<?php echo $field->id; ?>" value="1"/></td>
    <td><input type="hidden" id="id" name="id[<?php echo $field->num; ?>]" value="<?php echo $field->id; ?>">
	<input type="text" id="ordering" name="ordering[<?php echo $field->num; ?>]" value="<?php echo $field->ordering; ?>" class="form-control"></td>
    <td><input type="text" id="field_name" name="field_name[<?php echo $field->num; ?>]" value="<?php echo $field->field_name; ?>" class="form-control"></td>
    <td><?php
		if (is_array($field->field_title))	// Translations for the field are set
		{
			$langs = explode(',', Backstage::gi()->portal_langs);
			echo '<ul class="nav nav-tabs" id="field_title_'.$field->num.'_tab">';
			foreach ($field->field_title as $translation)
			{
				echo "<li><a href='#field_title_".$field->num."_".$translation->short."' data-toggle='tab'>".$translation->short."</a></li>";
			}
			echo '</ul>';
			echo '<div class="tab-content">';

			foreach ($field->field_title as $translation)
			{
				echo "<div class='tab-pane' id='field_title_".$field->num."_".$translation->short."'>";
				echo '<input name="field_title['.$field->num.']['.$translation->language_id.']" class="form-control" value="'.$translation->translation.'"/>';
				echo '</div>';
			}
			echo '</div>';
		}
		else 
			echo '<input type="text" id="field_title" name="field_title['.$field->num.']" value="'.$field->field_title.'" class="form-control">'  
	?>
    </td>
    <td>
		<select name="field_type_id[<?php echo $field->num; ?>]" onchange="typeChange(<?php echo $field->num; ?>, this);" class="form-control">
             <?php
             foreach ($form_field_types as $field_type)
             {
                 $selected = '';
                 if ($field_type->id == $field->field_type_id) $selected = 'selected';
                 echo '<option value="'.$field_type->id.'" '.$selected.'>'.$field_type->type_name.'</option>';
             }
             ?>
        </select>
    </td>
    <td id="selects">
    <?php
			echo '<select name="field_select_id['.$field->num.']" class="form-control">';
			foreach ($form_field_selects as $field_select)
			{
				$selected = '';
				if ($field_select->id == $field->field_select_id) $selected = 'selected';
				echo '<option value="'.$field_select->id.'" '.$selected.'>'.$field_select->select_name.'</option>';
			}
			echo '</select>';
        ?>
    </td>
    <td id="linked_field">
        <?php
            echo '<select name="linked_field_id['.$field->num.']" class="form-control">';
            echo '<option value="0">-</option>';
            foreach ($form_field_linked_fields as $linked_field)
            {
                $selected = '';
                if ($linked_field->id == $field->linked_field_id) $selected = 'selected';
                echo '<option value="'.$linked_field->id.'" '.$selected.'>'.$linked_field->field_name.'</option>';
            }
            echo '</select>';
        ?>
    </td>
    <td><input type="text" name="field_width[<?php echo $field->num; ?>]" value="<?php echo $field->field_width; ?>" class="form-control"></td>    
    <?php $checked = ''; if ($field->translation == 1) $checked = 'checked'; ?>
    <?php $required = ''; if ($field->required == 1) $required = 'checked'; ?>
    <?php $datetime = ''; if ($field->datetime == 1) $datetime = 'checked'; ?>
    <td>
		<input type="hidden" id="translation" name="translation[<?php echo $field->num; ?>]" value="0"/>
		<input type="checkbox" id="translation" name="translation[<?php echo $field->num; ?>]" value="1" <?php echo $checked;?>></td>
    <td>
        <input type="hidden" id="required" name="required[<?php echo $field->num; ?>]" value="0"/>
        <input type="checkbox" id="required" name="required[<?php echo $field->num; ?>]" value="1" <?php echo $required;?>></td>
    <td>
        <input type="hidden" id="datetime" name="datetime[<?php echo $field->num; ?>]" value="0"/>
        <input type="checkbox" id="datetime" name="datetime[<?php echo $field->num; ?>]" value="1" <?php echo $datetime;?>></td>
    <td><button type="button" data-id="<?php echo $field->id; ?>" data-num="<?php echo $field->num; ?>" onclick="deleteField(this);" class="form_field_delete_btn btn btn-danger btn-xs"><span class="glyphicon glyphicon-floppy-remove"></span></button></td>
<?php	
	echo '</tr>';
?>
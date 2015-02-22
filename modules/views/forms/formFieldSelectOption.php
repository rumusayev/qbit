<?php

	echo '<tr id="row_'.$option->num.'">';
?>
	<td><input type="checkbox" data-id="<?php echo $option->id; ?>" data-num="<?php echo $option->num; ?>" id="options_delete_<?php echo $option->id; ?>" value="1"/></td>
    <td><input type="hidden" id="id" name="id[<?php echo $option->num; ?>]" value="<?php echo $option->id; ?>">
	<input type="text" id="ordering" name="ordering[<?php echo $option->num; ?>]" value="<?php echo $option->ordering; ?>" class="form-control"></td>
    <td><?php
		if (is_array($option->option_title))	// Translations for the field are set
		{
			$langs = explode(',', Backstage::gi()->portal_langs);
			echo '<ul class="nav nav-tabs" id="option_title_'.$option->num.'_tab">';
			foreach ($option->option_title as $translation)
			{
				echo "<li><a href='#option_title_".$option->num."_".$translation->short."' data-toggle='tab'>".$translation->short."</a></li>";
			}
			echo '</ul>';
			echo '<div class="tab-content">';

			foreach ($option->option_title as $translation)
			{
				echo "<div class='tab-pane' id='option_title_".$option->num."_".$translation->short."'>";
				echo '<input name="option_title['.$option->num.']['.$translation->short.']" class="form-control" value="'.$translation->translation.'"/>';
				echo '</div>';
			}
			echo '</div>';
		}
		else 
			echo '<input type="text" id="option_title" name="option_title['.$option->num.']" value="'.$option->option_title.'" class="form-control">'  
	?>
    </td>
    <td><input type="text" name="option_value[<?php echo $option->num; ?>]" value="<?php echo $option->option_value; ?>" class="form-control"></td>    
    <?php $selected = ''; if ($option->selected == 1) $selected = 'checked'; ?>
    <td>
		<input type="hidden" id="selected" name="selected[<?php echo $option->num; ?>]" value="0"/>
		<input type="checkbox" id="selected" name="selected[<?php echo $option->num; ?>]" value="1" <?php echo $selected;?>></td>        
    <td><button type="button" data-id="<?php echo $option->id; ?>" data-num="<?php echo $option->num; ?>" onclick="deleteOption(this);" class="form_option_delete_btn btn btn-danger btn-xs"><span class="glyphicon glyphicon-floppy-remove"></span></button></td>
<?php	
	echo '</tr>';
?>
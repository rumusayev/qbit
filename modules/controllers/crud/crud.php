<?php
class cCrud extends controller
{	
	public function execute()
	{
		$this->data['view_name'] = 'crud';
		$this->data['body'] = Loader::gi()->getView($this->data);

		return $this->data;
	}	

	public function load()
	{
			// Get crud native parameters data
		$crud_params_form = json_decode($this->data['request']->parameters['crud_params_form'], true);
		if (isset($crud_params_form['after_load_method_path']))
			$after_load_method_path = $crud_params_form['after_load_method_path'];
		if (isset($crud_params_form['name']))
			$this->data['name'] = $crud_params_form['name'];
		if (isset($crud_params_form['where']))
			$this->data['where'] = $crud_params_form['where'];
		if (isset($crud_params_form['query']))
			$this->data['query'] = base64_decode($crud_params_form['query']);
		if (isset($crud_params_form['tables']))
			$this->data['tables'] = $crud_params_form['tables'];	
		if (isset($crud_params_form['ids']))
			$this->data['ids'] = json_decode($crud_params_form['ids']);
		if (isset($crud_params_form['links']))
			$this->data['links'] = json_decode($crud_params_form['links'], true);
		if (isset($crud_params_form['buttons']))
			$this->data['buttons'] = json_decode($crud_params_form['buttons'], true);
		if (isset($crud_params_form['js_handlers']))
			$this->data['js_handlers'] = json_decode($crud_params_form['js_handlers'], true);
		if (isset($crud_params_form['format_rules']))
			$this->data['format_rules'] = json_decode($crud_params_form['format_rules'], true);
		if (isset($crud_params_form['form_fields_dimensions']))
			$this->data['form_fields_dimensions'] = json_decode($crud_params_form['form_fields_dimensions'], true);
		if (isset($crud_params_form['field_names']))
			$this->data['field_names'] = $crud_params_form['field_names'];
		if (isset($crud_params_form['order']))
			$this->data['order'] = $crud_params_form['order'];
		if (isset($crud_params_form['search']))
			$this->data['search'] = json_decode($crud_params_form['search']);
		if (isset($crud_params_form['titles']))
			$this->data['titles'] = json_decode($crud_params_form['titles'], true);
		if (isset($crud_params_form['mapped_values']))
			$this->data['mapped_values'] = json_decode($crud_params_form['mapped_values'], true);
		if (isset($crud_params_form['mapped_values_f']))
			$this->data['mapped_values_f'] = json_decode($crud_params_form['mapped_values_f']);
		if (isset($crud_params_form['types']))
			$this->data['types'] = json_decode($crud_params_form['types'], true);
		if (isset($crud_params_form['totals']))
			$this->data['totals'] = json_decode($crud_params_form['totals'], true);
		if (isset($crud_params_form['mapped_fields']))
			$this->data['mapped_fields'] = json_decode($crud_params_form['mapped_fields']);
		if (isset($crud_params_form['mapped_parents']))
			$this->data['mapped_parents'] = json_decode($crud_params_form['mapped_parents'], true);
		if (isset($crud_params_form['mapped_search']))
			$this->data['mapped_search'] = json_decode($crud_params_form['mapped_search'], true);
		if (isset($crud_params_form['mapped_field_inputs']))
			$this->data['mapped_field_inputs'] = json_decode($crud_params_form['mapped_field_inputs'], true);
		if (isset($crud_params_form['mapped_passwords']))
			$this->data['mapped_passwords'] = json_decode($crud_params_form['mapped_passwords'], true);
		if (isset($crud_params_form['restrictions']))
			$this->data['restrictions'] = json_decode($crud_params_form['restrictions']);
		if (isset($crud_params_form['hidden_edit_fields']))
			$this->data['hidden_edit_fields'] = json_decode($crud_params_form['hidden_edit_fields'], true);
		if (isset($crud_params_form['disabled_edit_fields']))
			$this->data['disabled_edit_fields'] = json_decode($crud_params_form['disabled_edit_fields'], true);
		if (isset($crud_params_form['disabled_table_fields']))
			$this->data['disabled_table_fields'] = json_decode($crud_params_form['disabled_table_fields'], true);
        if (isset($crud_params_form['translations']))
            $this->data['translations'] = json_decode($crud_params_form['translations'], true);
        if (isset($crud_params_form['add_editor_list']))
            $this->data['add_editor_list'] = json_decode($crud_params_form['add_editor_list'], true);
        if (isset($crud_params_form['add_lq_button']))
            $this->data['add_lq_button'] = json_decode($crud_params_form['add_lq_button'], true);
        if (isset($crud_params_form['uploader_object_type']))
            $this->data['uploader_object_type'] = $crud_params_form['uploader_object_type'];
        if (isset($crud_params_form['crud_resource_types']))
            $this->data['crud_resource_types'] = json_decode($crud_params_form['crud_resource_types'], true);
        if (isset($crud_params_form['unique_fields']))
            $this->data['unique_fields'] = json_decode($crud_params_form['unique_fields'], true);
		if (isset($crud_params_form['additional_form_field']))
			$this->data['additional_form_field'] = $crud_params_form['additional_form_field'];		
		if (isset($crud_params_form['additional_form_table']))
			$this->data['additional_form_table'] = $crud_params_form['additional_form_table'];
		if (isset($crud_params_form['manual_search_format']))
			$this->data['manual_search_format'] = json_decode($crud_params_form['manual_search_format'], true);
			
		$this->data['crud_current_page'] = isset($crud_params_form['crud_current_page'])?$crud_params_form['crud_current_page']:1;                
		$this->data['crud_count_per_page'] = isset($crud_params_form['crud_count_per_page'])?$crud_params_form['crud_count_per_page']:10;                
		
		$this->data['crud_parent_id'] = $crud_params_form['crud_parent_id'];
		
			// Get search fields
		if (!empty($this->data['search']))
		{
			$this->data['crud_search_form'] = json_decode($this->data['request']->parameters['crud_search_form'], true);
			$search_fields = array();
			if (!isset($this->data['crud_search_form']['search_condition']))
				$this->data['crud_search_form']['search_condition'] = 'like';
			$this->data['crud_search_condition'] = $this->data['crud_search_form']['search_condition'];
			unset($this->data['crud_search_form']['search_condition']);			
			foreach ($this->data['crud_search_form'] as $el=>$val)
			{
				if (strpos($el, '^') === 0)
					$el = str_replace('^', '', $el);
				else
					$el = str_replace('^', '.', $el);
				if ($val !== '')
				{
					if (strpos($val, ',') > 0)
					{
						$val_parts = explode(',', $val);
						$search_fields_parts = array();
						foreach ($val_parts as $val_part)
						{
							if (in_array($this->data['crud_search_condition'], array('like', 'not like')) && !in_array(substr($el, strpos($el,'.')+1), $this->data['manual_search_format']))
								$val_part = '%'.$val_part.'%';
							$search_fields_parts[] = $el.' '.$this->data['crud_search_condition'].' \''.$val_part.'\'';
						}
						$search_fields[] = ' ('.implode(' or ', $search_fields_parts).') ';
					}
					else
					{
						if (in_array($this->data['crud_search_condition'], array('like', 'not like')) && !in_array(substr($el, strpos($el,'.')+1), $this->data['manual_search_format']))
							$val = '%'.$val.'%';
						$search_fields[] = $el.' '.$this->data['crud_search_condition'].' \''.$val.'\'';
					}
				}
			}
			$this->data['search_fields'] = implode(' and ', $search_fields);
		}
		
		// Model loading
		$this->data = Loader::gi()->getModel($this->data);
	
		$this->data['crud_pages_count'] = $this->data['crud_count_per_page']==0?1:ceil($this->data['crud_total_count']/$this->data['crud_count_per_page']);
		
		$this->data['view_name']  = 'crudTable';
		$this->data['body'] = Loader::gi()->getView($this->data);
		
		if ($after_load_method_path !== '')
			$out = Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.$after_load_method_path, $this->data['request']->parameters);
		
		return $this->data;
	}
	
	public function validateUnique()
	{
		$form_params = json_decode($this->data['request']->parameters['form_params'], true);
		$form_values = json_decode($this->data['request']->parameters['form_values'], true);
		if (isset($form_params['mapped_fields']))
			$mapped_fields = json_decode($form_params['mapped_fields']);		
		if (isset($form_params['ids']))
			$ids = json_decode($form_params['ids']);

		$this->data['unique_fields'] = json_decode($form_params['unique_fields']);

		$where = array();
		$values = array();
			// Collecting values and table names
		foreach ($form_values as $el=>$val)
		{
			$el_parts = explode('^', $el);
			$el_field = $el_parts[1];
			if (in_array($el_parts[1], $ids))
			{
				if ($val !== '' && $val > 0)
				{
					$where[$el_parts[0]] = "{$el_field} != {$val}";
				}
			}
			if (in_array($el_field, $this->data['unique_fields']))			
				$values[$el_parts[0]][$el_field] = "{$val}";
		}
		
		$this->data['values'] = $values;
		$this->data['where'] = $where;

		// Model loading
		$this->data = Loader::gi()->getModel($this->data);
		$this->data['body'] = $this->data['status'];
		
		return $this->data;		
	}
	
	public function save()
	{
		$form_params = json_decode($this->data['request']->parameters['form_params'], true);
		$form_values = json_decode($this->data['request']->parameters['form_values'], true);
		$files = json_decode($this->data['request']->parameters['files'], true);
		
		if (isset($form_params['ids']))
			$ids = json_decode($form_params['ids']);
		if (isset($form_params['mapped_fields']))
			$mapped_fields = json_decode($form_params['mapped_fields']);		
		$this->data['mapped_parents'] = json_decode($form_params['mapped_parents'], true);
		$this->data['crud_parent_id'] = $form_params['crud_parent_id'];
		$this->data['crud_resource_types'] = json_decode($form_params['crud_resource_types'], true);
		$translations = json_decode($form_params['translations']);
		$mapped_passwords = json_decode($form_params['mapped_passwords'], true);

			// Additional access right checks
		$method = 'PUT';
		foreach ($this->data['crud_resource_types'] as $crud_resource_types)
		{
					// In future we should change this behavior in case of multi-ids
			$method = 'PUT';
			if ($form_values[$crud_resource_types.'^'.$ids[0]] == 0)
				$method = 'POST';
			if (!Pretorian::gi()->check($crud_resource_types, $method, $form_values[$crud_resource_types.'^'.$ids[0]]))
			{	
				switch($method)  
				{
					case 'POST':  
						throw new QException(array('ER-00011'));
						break;  
					case 'PUT':  
						throw new QException(array('ER-00012'));
						break;
				}
			}
		}
		
		if (isset($form_params['before_save_method_path']))
			$before_save_method_path = $form_params['before_save_method_path'];
		if (isset($form_params['after_save_method_path']))
			$after_save_method_path = $form_params['after_save_method_path'];
		if (isset($form_params['after_load_method_path']))
			$after_load_method_path = $form_params['after_load_method_path'];
		if (isset($form_params['override_orig_save']))
			$override_orig_save = (boolean)$form_params['override_orig_save'];		
		if (isset($form_params['crud_resource_types']))
			$this->data['crud_resource_types'] = json_decode($form_params['crud_resource_types'], true);
		
		if (isset($before_save_method_path) && $before_save_method_path !== '')
		{
			$out = Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.$before_save_method_path, $this->data['request']->parameters);
			if ($override_orig_save)
				return $this->data;
		}
		
		$where = array();
		$values = array();

			// Collecting values and table names
		foreach ($form_values as $el=>$val)
		{
			$el_parts = explode('^', $el);
			
			$el_field = $el_parts[1];
			if (!empty($mapped_fields) && array_key_exists($el_field, $mapped_fields))
				$el_field = $mapped_fields[$el_field];  
					
			if (in_array($el_parts[1], $ids))
			{
				if ($val !== '' && $val > 0)
				{
					$values[$el_parts[0]]['reserved_id'] = $val;
					$where[$el_parts[0]] = "{$el_field} = {$val}";
				}
			}
			elseif (in_array($el_field, $translations))	// Parsing translations
			{
				unset($val[0]);
				$values[$el_parts[0]]['translations'][$el_field] = $val;
				$values[$el_parts[0]][$el_field] = '';
			}
			elseif (array_key_exists($el_field, $mapped_passwords))	// Parsing passwords
			{
				if ($mapped_passwords[$el_field] === 'md5' && $val != '')
					$values[$el_parts[0]][$el_field] = md5($val); 
			}			
			else
				$values[$el_parts[0]][$el_field] = "{$val}";
		}
		
		$this->data['where'] = $where;
		$this->data['values'] = $values;

		$this->data = Loader::gi()->getModel($this->data);	
		
			// Saving files		
		if ($form_params['uploader_object_type'] != '')
		{
			$file_par['files'] = $files;
			$file_par['session_id'] = session_id();
			$file_par['object_type'] = $form_params['uploader_object_type'];
			$file_par['object_id'] = $this->data['id'];
			$data = Loader::gi()->callModule($method, 'materials/saveFiles', $file_par);
		}
		
			// Saving form values (if a form is actiaved)		
		if ($form_params['additional_form_field'] != '' && isset($this->data['request']->parameters['additional_form']))
		{
			$form_par['fields'] = json_decode($this->data['request']->parameters['additional_form'], true);
			$form_par['form_id'] = $form_values[$crud_resource_types.'^'.$form_params['additional_form_field']];
			$form_par['table_name'] = $form_params['additional_form_table'];
			$form_par['row_id'] = $this->data['id'];
			$data = Loader::gi()->callModule($method, 'forms/saveFormFieldValues', $form_par);
		}
		
		$this->data['request']->parameters['id'] = $this->data['id'];
		
		if ($after_save_method_path !== '')
			$data = Loader::gi()->callModule($method, $after_save_method_path, $this->data['request']->parameters);
		// Saving map
		$map_data = Loader::gi()->callModule('POST', 'search/saveMap', array('fields'=>$values, 'container_id'=>$this->data['id']));
			
		$this->data['body'] = $this->data['status'];
		return $this->data;
	}
        
	public function delete()
	{
		// Collecting values and table names
		$form_params = json_decode($this->data['request']->parameters['form_params'], true);
		$this->data['deleting_data'] = json_decode($this->data['request']->parameters['deleting_data'], true);
		$this->data['translations'] = json_decode($form_params['translations']);
		$this->data['mapped_parents'] = json_decode($form_params['mapped_parents'], true);
		$this->data['additional_form_table'] = $form_params['additional_form_table'];
		$this->data['crud_resource_types'] = json_decode($form_params['crud_resource_types'], true);
		$after_delete_method_path = $form_params['after_delete_method_path'];
		
		$this->data = Loader::gi()->getModel($this->data);
		
		if (isset($after_delete_method_path) && $after_delete_method_path !== '')
			$data = Loader::gi()->callModule('DELETE', $after_delete_method_path, $this->data['request']->parameters);
		
		$this->data['body'] = $this->data['status'];
		return $this->data;
	}
	
}
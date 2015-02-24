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
		$crud_data = json_decode(base64_decode($this->data['request']->parameters['crud_data']), true);
		
		$this->data = array_merge($this->data, $crud_data);
		$this->data = array_merge($this->data, $crud_params_form);
		$this->data['crud_data'] = $this->data['request']->parameters['crud_data'];
		$after_load_method_path = $crud_data['after_load_method_path'];
							
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
		$crud_data = json_decode(base64_decode($this->data['request']->parameters['crud_data']), true);
		$form_values = json_decode($this->data['request']->parameters['form_values'], true);
		if (isset($crud_data['mapped_fields']))
			$mapped_fields = $crud_data['mapped_fields'];
		if (isset($crud_data['ids']))
			$ids = $crud_data['ids'];

		$this->data['unique_fields'] = $crud_data['unique_fields'];

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
		$crud_data = json_decode(base64_decode($this->data['request']->parameters['crud_data']), true);	
		$form_params = json_decode($this->data['request']->parameters['crud_params_form'], true);
		$form_values = json_decode($this->data['request']->parameters['form_values'], true);
		$files = json_decode($this->data['request']->parameters['files'], true);
		
		if (isset($crud_data['ids']))
			$ids = $crud_data['ids'];
		if (isset($crud_data['mapped_fields']))
			$mapped_fields = $crud_data['mapped_fields'];		
		$this->data['mapped_parents'] = $crud_data['mapped_parents'];
		$this->data['crud_parent_id'] = $crud_data['crud_parent_id'];
		$this->data['crud_parent_table'] = $crud_data['crud_parent_table'];
		$this->data['crud_resource_types'] = $crud_data['crud_resource_types'];
		$this->data['disabled_saving_tables'] = $crud_data['disabled_saving_tables'];
		$translations = $crud_data['translations'];
		$mapped_passwords = $crud_data['mapped_passwords'];

			// Additional access right checks
		$method = $this->data['request']->method;
		foreach ($this->data['crud_resource_types'] as $crud_resource_types)
		{
					// In future we should change this behavior in case of multi-ids
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
		
		if (isset($crud_data['before_save_method_path']))
			$before_save_method_path = $crud_data['before_save_method_path'];
		if (isset($crud_data['after_save_method_path']))
			$after_save_method_path = $crud_data['after_save_method_path'];
		if (isset($crud_data['after_load_method_path']))
			$after_load_method_path = $crud_data['after_load_method_path'];
		if (isset($crud_data['override_orig_save']))
			$override_orig_save = (boolean)$crud_data['override_orig_save'];		
		if (isset($crud_data['crud_resource_types']))
			$this->data['crud_resource_types'] = $crud_data['crud_resource_types'];
		
		if (isset($before_save_method_path) && $before_save_method_path !== '')
		{
			$data = Loader::gi()->callModule($method, $before_save_method_path, $this->data['request']->parameters);
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
			if (!in_array($el_parts[0], $this->data['disabled_saving_tables']))
			{
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
		}
		
		$this->data['where'] = $where;
		$this->data['values'] = $values;

		$this->data = Loader::gi()->getModel($this->data);	
		
			// Saving files		
		if ($crud_data['uploader_object_type'] != '')
		{
			$file_par['files'] = $files;
			$file_par['session_id'] = session_id();
			$file_par['object_type'] = $crud_data['uploader_object_type'];
			$file_par['object_id'] = $this->data['id'];
			$data = Loader::gi()->callModule($method, 'materials/saveFiles', $file_par);
		}
		
			// Saving form values (if a form is actiaved)		
		if ($crud_data['additional_form_field'] != '' && isset($this->data['request']->parameters['additional_form']))
		{
			$form_par['fields'] = json_decode($this->data['request']->parameters['additional_form'], true);
			$form_par['form_id'] = $form_values[$crud_resource_types.'^'.$crud_data['additional_form_field']];
			$form_par['table_name'] = $crud_data['additional_form_table'];
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
		$crud_data = json_decode(base64_decode($this->data['request']->parameters['crud_data']), true);	
		$form_params = json_decode($this->data['request']->parameters['crud_params_form'], true);
		$this->data['deleting_data'] = json_decode($this->data['request']->parameters['deleting_data'], true);
		$this->data['translations'] = $crud_data['translations'];
		$this->data['mapped_parents'] = $crud_data['mapped_parents'];
		$this->data['additional_form_table'] = $crud_data['additional_form_table'];
		$this->data['crud_resource_types'] = $crud_data['crud_resource_types'];
		$after_delete_method_path = $crud_data['after_delete_method_path'];
		
		$this->data = Loader::gi()->getModel($this->data);
		
		if (isset($after_delete_method_path) && $after_delete_method_path !== '')
			$data = Loader::gi()->callModule('DELETE', $after_delete_method_path, $this->data['request']->parameters);
		
		$this->data['body'] = $this->data['status'];
		return $this->data;
	}
	
}
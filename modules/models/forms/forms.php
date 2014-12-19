<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mForms extends model
{
	public function getForm()
	{
        $this->data['item'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'forms a LEFT JOIN '.Backstage::gi()->db_table_prefix.'designs b ON  b.id = a.design_id')
            ->fields('a.*, b.design_name')
            ->where('a.form_name = "'.$this->data['request']->parameters['lq']['name'].'"')
            ->getScalar();
		return $this->data;
	}
	
	public function getFormFields()
	{
        $this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_fields')
            ->fields('*')
            ->where('form_id = '.$this->data['request']->parameters['form_id'])
            ->select();
			// Get translations
		foreach ($this->data['items'] as $key => $item) 
		{
			$this->data['items'][$key]->num = $key;
			$translations = Translations::gi()->getTranslations('form_fields', $item->id);
			if (!empty($translations)) {
				foreach ($translations as $translation_field => $translation)
					$this->data['items'][$key]->$translation_field = $translation;
			}
		}			
		return $this->data;
	}        
	
	public function getFormFieldTypes()
	{
		$this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_types')
            ->fields('*')
            ->order($this->data['request']->parameters['order'])
            ->select();
		return $this->data;
	}		
	
	public function getFormFieldSelects()
	{
		$this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_selects')
            ->fields('*')
            ->order($this->data['request']->parameters['order'])
            ->select();
		return $this->data;
	}	
	
	public function getFormFieldLinkedFields()
	{
		$this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_fields')
            ->fields('*')
            ->order($this->data['request']->parameters['order'])
            ->select();
		return $this->data;
	}	
        
	public function saveFormFields()
	{
		$table = Backstage::gi()->db_table_prefix.'form_fields';
		foreach ($this->data['items'] as $item)
		{
			if ($item['id'] != 0)
			{
				$this->data['status'] = $this->dbmanager->tables($table)
															->values($item)
															->update();			
				$last_id = $item['id'];
			}
			else
			{
				$this->data['status'] .= $this->dbmanager->tables($table)
															->values($item)
															->insert();
                $last_id = $this->dbmanager->getLastID();
			}
			
			if (isset($item['translations'])) 
			{
				Translations::gi()->setTranslations($item['translations'], $table, $last_id);
				unset($translations);
			}			
		}
		
		return $this->data;
	}
        		
    public function deleteFormFields()
    {
		$this->data['status'] = '';
		if (isset($this->data['request']->parameters['fields']))
		{
			$this->data['fields'] = json_decode($this->data['request']->parameters['fields'], true);		
			foreach ($this->data['fields'] as $item)
			{
				$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_fields')
					->where('id = '.$item['id'])
					->delete();				
				
				$field_values = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_values')
					->fields('*')
					->where('field_id = '.$item['id'])
					->select();		
					
				$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_values')
					->where('field_id = '.$item['id'])
					->delete();		
					
					// Delete all field translations
				//if (!empty($this->data['translations']))
					$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
						->where('table_name = "form_fields" and row_id = '.$item['id'])
						->delete();
				foreach ($field_values as $field_value)
				{
						// Delete all field values translations
					//if (!empty($this->data['translations']))
						$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
							->where('table_name = "form_field_values" and row_id = '.$field_value->id)
							->delete();					
				}
			}
		}
		elseif (isset($this->data['request']->parameters['deleting_data']))
		{
			foreach ($this->data['request']->parameters['deleting_data'] as $item)
			{
				if (isset($item['id']))
				{					
					$fields = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_fields')
						->fields('*')					
						->where('form_id = '.$item['id'])
						->select();		
						
					$field_values = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_values')
						->fields('*')					
						->where('form_id = '.$item['id'])
						->select();		
					
					$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_fields')
						->where('form_id = '.$item['id'])
						->delete();				

					$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_values')
						->where('form_id = '.$item['id'])
						->delete();
						
					foreach ($fields as $field)
					{
							// Delete all fields translations
						//if (!empty($this->data['translations']))
							$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
								->where('table_name = "form_fields" and row_id = '.$field->id)
								->delete();					
					}				
					
					foreach ($field_values as $field_value)
					{
							// Delete all field values translations
						//if (!empty($this->data['translations']))
							$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
								->where('table_name = "form_field_values" and row_id = '.$field_value->id)
								->delete();					
					}
					
				}
			}		
		}
		
        return $this->data;
    }		

    public function deleteFormFieldValues()
    {
		$field_values = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_values')
			->fields('*')
			->where('row_id = '.$this->data['request']->parameters['object_id'].' and table_name = "'.$this->data['request']->parameters['object_table'].'"')
			->select();		
			
		$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_values')
			->where('row_id = '.$this->data['request']->parameters['object_id'].' and table_name = "'.$this->data['request']->parameters['object_table'].'"')
			->delete();		
			
		foreach ($field_values as $field_value)
		{
			// Delete all field values translations
			$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
				->where('table_name = "form_field_values" and row_id = '.$field_value->id)
				->delete();					
		}		
	}
	
	public function getFormFieldSelectOptions()
	{
        $this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_select_options')
            ->fields('*')
            ->where('field_select_id = '.$this->data['request']->parameters['field_select_id'])
            ->select();
			// Get translations
		foreach ($this->data['items'] as $key => $item) 
		{
			$this->data['items'][$key]->num = $key;
			$translations = Translations::gi()->getTranslations('form_field_select_options', $item->id);
			if (!empty($translations)) {
				foreach ($translations as $translation_field => $translation)
					$this->data['items'][$key]->$translation_field = $translation;
			}
		}			
		return $this->data;
	}
		
	public function saveFormFieldSelectOptions()
	{
		$table = Backstage::gi()->db_table_prefix.'form_field_select_options';
		foreach ($this->data['items'] as $item)
		{
			if ($item['id'] != 0)
			{
				$this->data['status'] = $this->dbmanager->tables($table)
															->values($item)
															->update();			
				$last_id = $item['id'];
			}
			else
			{
				$this->data['status'] .= $this->dbmanager->tables($table)
															->values($item)
															->insert();
                $last_id = $this->dbmanager->getLastID();
			}
			
			if (isset($item['translations'])) 
			{
				Translations::gi()->setTranslations($item['translations'], $table, $last_id);
				unset($translations);
			}			
		}
		
		return $this->data;
	}
		
    public function deleteFormFieldSelects()
    {
		$this->data['status'] = '';
		if (isset($this->data['request']->parameters['options']))
		{
			$this->data['options'] = json_decode($this->data['request']->parameters['options'], true);		
			foreach ($this->data['options'] as $item)
			{
				$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_select_options')
					->where('id = '.$item['id'])
					->delete();				
					
					// Delete all field translations
				//if (!empty($this->data['translations']))
					$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
						->where('table_name = "form_field_select_options" and row_id = '.$item['id'])
						->delete();
			}
		}
		elseif (isset($this->data['request']->parameters['deleting_data']))
		{
			foreach ($this->data['request']->parameters['deleting_data'] as $item)
			{
				if (isset($item['id']))
				{					
					$options = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_select_options')
						->fields('*')					
						->where('field_select_id = '.$item['id'])
						->select();		
					
					$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_field_select_options')
						->where('field_select_id = '.$item['id'])
						->delete();				
						
					foreach ($options as $option)
					{
							// Delete all fields translations
						//if (!empty($this->data['translations']))
							$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
								->where('table_name = "form_field_select_options" and row_id = '.$option->id)
								->delete();					
					}				
				}
			}
		}
		
        return $this->data;
    }

	public function getFormValues()
	{
		$this->data['form_id'] = $this->data['request']->parameters['form_id'];
		$this->data['row_id'] = isset($this->data['request']->parameters['row_id'])?$this->data['request']->parameters['row_id']:0;
		$this->data['table_name'] = isset($this->data['request']->parameters['table_name'])?$this->data['request']->parameters['table_name']:'';
		
		$this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'form_fields a LEFT JOIN '.Backstage::gi()->db_table_prefix.'form_field_values b ON a.id = b.field_id AND b.table_name = "'.$this->data['table_name'].'" and b.row_id = '.$this->data['row_id'].', '.Backstage::gi()->db_table_prefix.'form_field_types c')
						->fields('a.*, b.value, ifnull(b.id, 0) value_id, c.id type_id, c.type_name')
						->where("a.form_id = {$this->data['form_id']} and a.field_type_id = c.id")
						->select();
		foreach($this->data['items'] as $key=>$value)
		{
			$translations = Translations::gi()->getTranslations('form_fields', $value->id, Backstage::gi()->portal_current_lang);
			if (!empty($translations)) 
			{
				foreach ($translations as $translation_field => $translation)
					$this->data['items'][$key]->$translation_field = $translation->translation;
			}			
			
			if ($value->translation == 1)
			{
				$translations = Translations::gi()->getTranslations('form_field_values', $value->value_id);
				if (!empty($translations)) 
				{
					foreach ($translations as $translation_field => $translation)
						$this->data['items'][$key]->$translation_field = $translation;
				}
			}			
		}
        return $this->data;	
	}
	
	public function saveFormFieldValues()
	{
		$this->data['status'] = '';
		$table = Backstage::gi()->db_table_prefix.'form_field_values';
		foreach ($this->data['items'] as $item)
		{
			if ($item['id'] != 0)
			{
				$this->data['status'] = $this->dbmanager->tables($table)
															->values($item)
															->update();			
				$last_id = $item['id'];
			}
			else
			{
				$this->data['status'] .= $this->dbmanager->tables($table)
															->values($item)
															->insert();
                $last_id = $this->dbmanager->getLastID();
			}
			
			if (isset($item['translations'])) 
			{
				Translations::gi()->setTranslations($item['translations'], $table, $last_id);
				unset($translations);
			}
		}			
		return $this->data;		
	}
	
    public function getObjectFormID()
    {
        $this->data['item'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.$this->data['request']->parameters['object_table'])
            ->fields('form_id')
            ->where('id = '.$this->data['request']->parameters['object_id'])
            ->getScalar();
        return $this->data;
	}    	
}
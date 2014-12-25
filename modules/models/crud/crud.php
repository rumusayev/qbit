<?php
class mCrud extends model
{
    // replace last occurance in the string
    private function str_lreplace($search, $replace, $subject)
    {
        $pos = strripos($subject, $search);
        $open_brackets_cnt = substr_count($subject, '(', $pos);
        $closed_brackets_cnt = substr_count($subject, ')', $pos);
        if ($pos && ($closed_brackets_cnt - $open_brackets_cnt) === 0) 
		{
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    public function load()
    {

        if ($this->data['query'] !== '') 
		{
            if (isset($this->data['search_fields']) && !empty($this->data['search_fields'])) 
			{
                if (stristr($this->data['query'], 'where'))
                    $this->data['query'] = $this->str_lreplace('where', 'where (' . $this->data['search_fields'] . ') and ', $this->data['query']);
                else
                    $this->data['query'] .= ' where (' . $this->data['search_fields'] . ')';
            }
			if (!empty($this->data['mapped_parents']))
			{
				$parent_field_name = reset($this->data['mapped_parents']);
				$child_field_name = key($this->data['mapped_parents']);
				
                if (stristr($this->data['query'], 'where'))
                    $this->data['query'] = $this->str_lreplace('where', 'where ('.$parent_field_name.' = "'.$this->data['crud_parent_id'].'") and ', $this->data['query']);
                else
                    $this->data['query'] .= ' where ('.$parent_field_name.' = "'.$this->data['crud_parent_id'].'")';

				$this->data['parent_parent_id'] = $this->dbmanager->getScalarByQuery("select $parent_field_name from ({$this->data['query']}) a where $child_field_name = {$this->data['crud_parent_id']}");
				if ($this->data['parent_parent_id'])
					$this->data['parent_parent_id'] = $this->data['parent_parent_id']->{$parent_field_name};
				else
					$this->data['parent_parent_id'] = -1;
					
			}
            if ($this->data['order'] !== '')
                $this->data['query'] .= ' order by ' . $this->data['order'];
            // Return totals under the table
            if (!empty($this->data['totals'])) 
			{
                $totals = array();
                foreach ($this->data['totals'] as $total_key => $total_val)
                    $totals[] = "sum($total_key) as $total_key";
                $totals = $this->dbmanager->getScalarByQuery("select " . implode(',', $totals) . " from ({$this->data['query']}) a");
                foreach ($this->data['totals'] as $total_key => $total_val)
                    $this->data['totals'][$total_key] = $totals->$total_key;
            }
            $this->data['crud_total_count'] = $this->dbmanager->getScalarByQuery("select count(*) cnt from ({$this->data['query']}) a");
            $this->data['crud_total_count'] = $this->data['crud_total_count']->cnt;
            if ($this->data['crud_count_per_page'] == 0 || $this->data['crud_current_page'] - 1 > $this->data['crud_total_count'] / $this->data['crud_count_per_page'])
                $this->data['crud_current_page'] = 1;
            $from = ($this->data['crud_current_page'] - 1) * $this->data['crud_count_per_page'];

            if ($this->data['crud_count_per_page'] > 0) {
                $limit = $this->data['crud_count_per_page'];
                $this->data['query'] .= " limit {$from},{$limit}";
            }
            $this->data['rows'] = $this->dbmanager->selectByQuery($this->data['query']);
        } 
		else 
		{
            $conditions = array();
            $conditions['where'] = $this->data['where'];
            $conditions['order'] = '';
            $conditions['limit'] = '';
            $conditions['fields'] = '';

            if (isset($this->data['search_fields']) && !empty($this->data['search_fields'])) 
			{
                $conditions['where'] .= ' AND ('.$this->data['search_fields'].')';
            } 
				// Load parents
			if (!empty($this->data['mapped_parents']))
			{
				$parent_field_name = reset($this->data['mapped_parents']);
				$child_field_name = key($this->data['mapped_parents']);
				$conditions['where'] .= ' AND ('.$parent_field_name.' = "'.$this->data['crud_parent_id'].'")';
				
				$this->data['parent_parent_id'] = $this->dbmanager->tables($this->data['tables'])
				->fields($parent_field_name)
				->where($child_field_name.' = '.$this->data['crud_parent_id'])
				->getScalar();
				if ($this->data['parent_parent_id'])
					$this->data['parent_parent_id'] = $this->data['parent_parent_id']->{$parent_field_name};
				else
					$this->data['parent_parent_id'] = -1;
				//if (isset($this->data['field_names']))
				//	$this->data['field_names'] = ", (select count(*) from {$this->data['tables']} where {$parent_field_name} = a.) parent_count";
			}
			
			$this->data['crud_total_count'] = $this->dbmanager->tables($this->data['tables'])
				->where($conditions['where'])
				->count();
					
            if ($this->data['order'] !== '')
                $conditions['order'] = $this->data['order'];
            $conditions['fields'] = isset($this->data['field_names']) ? $this->data['field_names'] : '*';

            if ($this->data['crud_count_per_page'] == 0 || $this->data['crud_current_page'] - 1 > $this->data['crud_total_count'] / $this->data['crud_count_per_page'])
                $this->data['crud_current_page'] = 1;
            $from = ($this->data['crud_current_page'] - 1) * $this->data['crud_count_per_page'];
            if ($this->data['crud_count_per_page'] > 0) {
                $limit = $this->data['crud_count_per_page'];
                $conditions['limit'] = "{$from},{$limit}";
            }

            if (strpos($conditions['fields'], 'parent_id') !== false ){
                $conditions['fields'] .= ", (SELECT count(*) FROM ".$this->data['tables']." ch WHERE ch.parent_id=".$this->data['tables'].".id) as child_count";
            }

            $this->data['rows'] = $this->dbmanager->tables($this->data['tables'])
                ->fields($conditions['fields'])
                ->where($conditions['where'])
                ->order($conditions['order'])
                ->limit($conditions['limit'])
                ->select();

        }
		
        foreach ($this->data['rows'] as $key => $row) 
		{
            $this->data['rows']['id' . $row->{$this->data['ids'][0]}] = $row;
            unset($this->data['rows'][$key]);
        }

        // Enrichment of fields
        $this->data['fields'] = $this->dbmanager->getFields();

        foreach ($this->data['rows'] as $row => $field) 
		{
            foreach ($field as $key => $value) 
			{
                foreach ($this->data['mapped_values'] as $nrow => $nfield) 
				{
                    if ($key == $nrow) 
					{
                        foreach ($nfield as $nkey => $nvalue) 
						{
                            if ($value == $nkey) 
							{
                                $this->data['rows'][$row]->{$key} = $nvalue;
                            }
                        }
                    }
                }
            }
			
				// Get files
			/* This functionality was transferred to crudTable because of performance issues
			*
			if (!empty($this->data['uploader_object_type']))
			{
				$data = Loader::gi()->callModule('GET', 'materials', array('where'=>'object_type = "'.$this->data['uploader_object_type'].'" AND object_id = '.$field->{$this->data['ids'][0]}));
				$this->data['rows'][$row]->uploader_files = $data['items'];
			}
			*/
				// Get translations
            foreach ($this->data['fields'] as $tr_field) 
            {

                if (in_array($tr_field['name'], $this->data['ids'])) {
                    $translations = Translations::gi()->getTranslations($tr_field['table'], $field->{$tr_field['name']});

                    if (!empty($translations)) {
                        foreach ($translations as $translation_field => $translation)
                            $this->data['rows'][$row]->$translation_field = $translation;
                    }
                }
            }
        }

        return $this->data;
    }

	public function validateUnique()
	{
        foreach ($this->data['values'] as $table_name => $table_values) 
		{
			if (in_array(key($table_values), $this->data['unique_fields']))
			{
				$this->data['status'] = $this->dbmanager->tables($table_name)
					->where(key($table_values).' = "'.reset($table_values).'" and '.(isset($this->data['where'][$table_name])?$this->data['where'][$table_name]:'1=1'))
					->count();
			}
		}

		return $this->data;		
	}
	
    public function save()
    {
        foreach ($this->data['values'] as $table_name => $table_values) 
		{
            if (isset($table_values['translations'])) 
			{
                $translations = $table_values['translations'];
                unset($table_values['translations']);
            }
            if (isset($this->data['where'][$table_name])) 
			{
                $this->data['status'] = $this->dbmanager->tables($table_name)
                    ->values($table_values)
                    ->where($this->data['where'][$table_name])
                    ->update();
                $this->data['id'] = $table_values['reserved_id'];
            } 
			else 
			{
                $this->data['status'] = $this->dbmanager->tables($table_name)
                    ->values($table_values)
                    ->insert();
                $this->data['id'] = $this->dbmanager->getLastID();
				if (!empty($this->data['crud_resource_types']))
					foreach ($this->data['crud_resource_types'] as $resource_type)
						if ($resource_type == $table_name)
						{
							$grant_values['resource_type'] = $table_name;
							$grant_values['resource_id'] = $this->data['id'];
							$grant_values['resource_name'] = '-';
							$grant_values['grant_type'] = 'GET';
							$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
								->values($grant_values)
								->insert();
							$last_grant_ids['GET'] = $this->dbmanager->getLastID();
							$grant_values['grant_type'] = 'PUT';
							$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
								->values($grant_values)
								->insert();
							$last_grant_ids['PUT'] = $this->dbmanager->getLastID();
							$grant_values['grant_type'] = 'POST';
							$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
								->values($grant_values)
								->insert();
							$last_grant_ids['POST'] = $this->dbmanager->getLastID();								
							$grant_values['grant_type'] = 'DELETE';
							$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
								->values($grant_values)
								->insert();
							$last_grant_ids['DELETE'] = $this->dbmanager->getLastID();
							
							if (!empty($this->data['mapped_parents']))
							{	
								// id => parent_id
								$parent_field_name = reset($this->data['mapped_parents']);
								$child_field_name = key($this->data['mapped_parents']);
								
								// Check if there are user grants for the parent object
								$this->data['user_grants'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'grants a', Backstage::gi()->db_table_prefix.'user_grants b')
									->fields('a.id, a.grant_type, b.user_id')
									->where('a.resource_id = 7 and a.resource_type = "'.$table_name.'" and a.id = b.grant_id and b.user_id is not null')
									->select();
								foreach ($this->data['user_grants'] as $user_grant)
								{
									$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'user_grants`')
										->values(array('id'=>'0', 'user_id'=>$user_grant->user_id, 'grant_id'=>$last_grant_ids[$user_grant->grant_type]))
										->insert();									
								}
								
								// Check if there are role grants for the parent object
								$this->data['role_grants'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'grants a', Backstage::gi()->db_table_prefix.'role_grants b')
									->fields('a.id, a.grant_type, b.role_id')
									->where('a.resource_id = 7 and a.resource_type = "'.$table_name.'" and a.id = b.grant_id')
									->select();
								foreach ($this->data['role_grants'] as $role_grant)
								{
									$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'role_grants`')
										->values(array('id'=>'0', 'role_id'=>$role_grant->role_id, 'grant_id'=>$last_grant_ids[$role_grant->grant_type]))
										->insert();									
								}
							}									
						}
            }

            if (isset($translations)) 
			{
                Translations::gi()->setTranslations($translations, $table_name, $this->data['id']);
                unset($translations);
            }
        }
        return $this->data;
    }

    public function delete()
    {
		$this->data['status'] = '';
		foreach ($this->data['deleting_data'] as $item)
		{
			if (isset($item['id']))
			{
					// Additional access right checks
				foreach ($this->data['crud_resource_types'] as $crud_resource_types)
				{
						// In future we should change this behavior in case of multi-ids
					$method = 'DELETE';
					if (!Pretorian::gi()->check($crud_resource_types, $method, $item['id']))
						continue;
				}

					// Delete all children
				if (!empty($this->data['mapped_parents']))
					$this->delete_children($item['table'], $item['id']);
				else
				{
					$this->data['status'] = $this->dbmanager->tables($item['table'])
						->where('id = '.$item['id'])
						->delete();		
						// Delete all translations
					if (!empty($this->data['translations']))
						$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
							->where('table_name = "'.$item['table'].'" and row_id = '.$item['id'])
							->delete();
						// Delete all grants (if there is any)
					if (!empty($this->data['crud_resource_types']))
					{
						$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'user_grants`')
							->where('grant_id in (select id from '.Backstage::gi()->db_table_prefix.'grants where resource_type = "'.$item['table'].'" and resource_id = '.$item['id'].')')
							->delete();
						$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'role_grants`')
							->where('grant_id in (select id from '.Backstage::gi()->db_table_prefix.'grants where resource_type = "'.$item['table'].'" and resource_id = '.$item['id'].')')
							->delete();			
						$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
							->where('resource_type = "'.$item['table'].'" and resource_id = '.$item['id'])
							->delete();
					}
						// Delete all materials (if there is any)
					$parameters['object_id'] = $item['id'];
					$parameters['object_type'] = $item['table'];
					$materials = Loader::gi()->callModule('DELETE', 'materials/deleteFiles', $parameters);							
					
						// Delete all form field values (if there is any)
					if (!empty($this->data['additional_form_table']))
					{
						$parameters['object_id'] = $item['id'];
						$parameters['object_table'] = $this->data['additional_form_table'];
						$form_field_values = Loader::gi()->callModule('DELETE', 'forms/deleteFormFieldValues', $parameters);	
					}
				}
			}
		}
        return $this->data;
    }
	
	private function delete_children($table, $parent_id)
	{
		$parent_field_name = reset($this->data['mapped_parents']);
		$child_field_name = key($this->data['mapped_parents']);	
		$children = $this->dbmanager->tables($table)
			->fields($child_field_name)
			->where($parent_field_name.' = '.$parent_id)
			->select();
		foreach ($children as $child)
		{
				// Delete all translations (if there is any)
			if (!empty($this->data['translations']))
				$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
					->where('table_name = "'.$table.'" and row_id = '.$child->{$child_field_name})
					->delete();
				// Delete all grants (if there is any)
			if (!empty($this->data['crud_resource_types']))
			{
				$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'user_grants`')
					->where('grant_id in (select id from '.Backstage::gi()->db_table_prefix.'grants where resource_type = "'.$table.'" and resource_id = '.$child->{$child_field_name}.')')
					->delete();
				$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'role_grants`')
					->where('grant_id in (select id from '.Backstage::gi()->db_table_prefix.'grants where resource_type = "'.$table.'" and resource_id = '.$child->{$child_field_name}.')')
					->delete();			
				$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
					->where('resource_type = "'.$table.'" and resource_id = '.$child->{$child_field_name})
					->delete();
			}
				// Delete all materials (if there is any)
			$parameters['object_id'] = $child->{$child_field_name};
			$parameters['object_type'] = $table;
			$materials = Loader::gi()->callModule('DELETE', 'materials/deleteFiles', $parameters);
			
				// Delete all form field values (if there is any)
			if (!empty($this->data['additional_form_table']))
			{
				$parameters['object_id'] = $child->{$child_field_name};
				$parameters['object_table'] = $this->data['additional_form_table'];
				$form_field_values = Loader::gi()->callModule('DELETE', 'forms/deleteFormFieldValues', $parameters);	
			}			
		
			$this->delete_children($table, $child->{$child_field_name});
		}
		$this->data['status'] = $this->dbmanager->tables($table)
			->where($child_field_name.' = '.$parent_id)
			->delete();		
		$this->data['status'] = $this->dbmanager->tables($table)
			->where($parent_field_name.' = '.$parent_id)
			->delete();
			// Delete all translations
		if (!empty($this->data['translations']))
			$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations')
				->where('table_name = "'.$table.'" and row_id = '.$parent_id)
				->delete();
			// Delete all grants (if there is any)
		if (!empty($this->data['crud_resource_types']))
		{
			$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'user_grants`')
				->where('grant_id in (select id from '.Backstage::gi()->db_table_prefix.'grants where resource_type = "'.$table.'" and resource_id = '.$parent_id.')')
				->delete();
			$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'role_grants`')
				->where('grant_id in (select id from '.Backstage::gi()->db_table_prefix.'grants where resource_type = "'.$table.'" and resource_id = '.$parent_id.')')
				->delete();			
			$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
				->where('resource_type = "'.$table.'" and resource_id = '.$parent_id)
				->delete();
		}
			// Delete all materials (if there is any)
		$parameters['object_id'] = $parent_id;
		$parameters['object_type'] = $table;
		$materials = Loader::gi()->callModule('DELETE', 'materials/deleteFiles', $parameters);

			// Delete all form field values (if there is any)
		if (!empty($this->data['additional_form_table']))
		{
			$parameters['object_id'] = $parent_id;
			$parameters['object_table'] = $this->data['additional_form_table'];
			$form_field_values = Loader::gi()->callModule('DELETE', 'forms/deleteFormFieldValues', $parameters);	
		}			
		
	}	
}
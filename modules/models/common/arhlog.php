<?php

/**
 * @package    Arhlog
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mArhlog extends model
{
	function commit()
	{
		$update_data = array();
		$where = '';
		if ($this->data['type'] === 'u')
		{
			$where = $this->data['where'];
		}
		else if ($this->data['type'] === 'i')
			$where = 'id = '.$this->dbmanager->lastInsertedId();                    
		
		if ($this->data['data'] = $this->dbmanager->tables($this->data['table_name'])->fields('*')->where($where)->select())
		{
			if ($this->data['type'] === 'u')
			{
				$update_data['table_name'] = $this->data['table_name'];
				$update_data['id'] = $this->data['data'][0]->id;
				$update_data['fields'] = array();
				foreach($this->data['new_values'] as $key=>$value)
				{
					$update_data['fields'][$key] = new stdClass();
					$update_data['fields'][$key]->old_value = $this->data['data'][0]->$key;
					$update_data['fields'][$key]->new_value = (String)$value;
				}
				$this->data['data'] = json_encode($update_data);
			}
			else
				$this->data['data'] = json_encode($this->data['data']);
			
			$this->data['conditions'] = str_replace("'", "\'", $this->data['where']);
			$this->data['reg_date'] = date("Y-m-d H:i:s");
			$escaped_conditions = str_replace("'", "\\\'", $this->data['conditions']);
			//$this->data['session_id'] = session_id();  // Session based arhlog
			$this->data['session_id'] = @Backstage::gi()->user->login;  // User based arhlog
			
			// Condition part is needed when there is an additional version checking condition
			$condition_part = '';
			if (isset($this->data['condition_part']))
				$condition_part = " and exists (select hash_group from arhlog b where a.resource_name=b.resource_name and a.session_id = b.session_id and a.hash_group = b.hash_group  and actuality = 1 and b.conditions like '%{$this->data['condition_part']}%' limit 1)";
			$version = $this->dbmanager->tables(Backstage::gi()->db_table_prefix."arhlog a")
										->fields('version')
										->where("hash_group != '{$this->data['hash_group']}' and type='{$this->data['type']}' and session_id = '{$this->data['session_id']}' and table_name = '{$this->data['table_name']}' and resource_name = '{$this->data['resource_name']}' and conditions = '{$escaped_conditions}' {$condition_part}")
										->order('reg_date desc')
										->getScalar();

			$this->data['version'] = $version?$version->version + 1:1;
			$this->dbmanager->tables(Backstage::gi()->db_table_prefix.'arhlog')->values($this->data)->insert();
			$this->data['status'] = 'OK';
		}
		else
			$this->data['status'] = 'Data was empty';
		return $this->data;
	}

	function rollback()
	{
		//$session_id = session_id();       // Session based arhlog
		$session_id = Backstage::gi()->user->login;    // User based arhlog
		// Condition part is needed when there is an additional version checking condition
		$condition_part = '';
		if (isset($this->data['condition_part']))
			$condition_part = " and conditions like '%{$this->data['condition_part']}%'";
		   // $condition_part = " and exists (select hash_group from arhlog b where a.resource_name=b.resource_name and a.session_id = b.session_id and a.hash_group = b.hash_group and b.conditions like '%{$this->data['condition_part']}%' limit 1)";
		
		$max_date_hash = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'arhlog')
										->fields('hash_group')
										->where("reg_date = (select max(reg_date) from ".Backstage::gi()->db_table_prefix."arhlog where session_id = '{$session_id}' and resource_name = '{$this->data['resource_name']}' {$condition_part} and actuality = 1) and session_id = '{$session_id}' and resource_name = '{$this->data['resource_name']}' {$condition_part} and actuality = 1")
										->getScalar();
		//$max_date_hash = $this->dbmanager->getScalar(Backstage::gi()->db_table_prefix.'arhlog', array('fields'=>'hash_group, max(reg_date) reg_date','where'=>"session_id = '{$session_id}' and resource_name = '{$this->data['resource_name']}' {$condition_part} and actuality = 1",'order'=>'id desc'));
		if ($logs = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'arhlog')
									->fields('*')
									->where("hash_group = '{$max_date_hash->hash_group}' AND actuality = 1")
									->order('id desc')
									->select())
		{
			foreach ($logs as $log)
			{
				$log->data = json_decode($log->data, true);
				switch ($log->type)
				{
					case 'i':
						$this->dbmanager->tables(Backstage::gi()->db_table_prefix.$log->table_name)->where('id = '.$log->data[0]['id'])->delete();
					break;
					case 'u':
						$values = array();
						$values['id'] = $log->data['id'];
						foreach ($log->data['fields'] as $field_name=>$field_values)
						{
							if (preg_match('/^-?(?:\d+|\d*\.\d+)$/', $field_values['old_value']) && preg_match('/^-?(?:\d+|\d*\.\d+)$/', $field_values['new_value']))
								$values[$field_name] = '++'.($field_values['old_value'] - $field_values['new_value']);
							else
								$values[$field_name] = $field_values['old_value'];
						}
						$this->dbmanager->tables($log->data['table_name'])->values($values)->update();
						//foreach ($log->data as $data_row)
						//    $this->dbmanager->update(Backstage::gi()->db_table_prefix.$log->table_name, $data_row, array('where'=>'id = '.$data_row['id']));
					break;
				}
				//$this->dbmanager->delete(Backstage::gi()->db_table_prefix.'arhlog', 'id='.$log->id);
				$value['actuality'] = 0;
				$this->dbmanager->tables(Backstage::gi()->db_table_prefix.'arhlog')->values($value)->where('id = '.$log->id)->update();
				$this->data['version'] = 1;
				$this->data['session_id'] = $session_id;
				$this->data['hash_group'] = $max_date_hash->hash_group;
				$this->data['reg_date'] = date("Y-m-d H:i:s");
				$this->data['resource_name'] = 'rollback';
				$this->data['priority'] = '1';
				$this->data['type'] = 'i';
				$this->data['table_name'] = 'arhlog';
				$this->data['conditions'] = 'Rollbacked action with id = '.$log->id;
				$this->dbmanager->tables(Backstage::gi()->db_table_prefix.'arhlog')->values($this->data)->insert();
			}
		}
		else
			$this->data['error'] = 'Köhnə verilənlər tapılmadı.';
			
		return $this->data;
    }   
	
	function log()
	{
		$this->data['data'] = json_encode($this->data['log_data']);
		$this->data['version'] = 1;
		$this->data['reg_date'] = date("Y-m-d H:i:s");		
		$this->data['session_id'] = @Backstage::gi()->user->login;  // User based arhlog
		$this->data['priority'] = '1';
				
		$this->dbmanager->tables(Backstage::gi()->db_table_prefix.'arhlog')->values($this->data)->insert();
		$this->data['status'] = 'OK';		
		return $this->data;
	}	
}
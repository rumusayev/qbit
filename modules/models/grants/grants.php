<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mGrants extends model
{
    public function getUserGrants()
    {
        $this->data['roles'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'roles a left join '.Backstage::gi()->db_table_prefix.'user_grants b on a.id = b.role_id and user_id = '.$this->data['request']->parameters['user_id'])
            ->fields('a.id, a.role_name, b.user_id, case b.user_id when '.$this->data['request']->parameters['user_id'].' then 1 else 0 end is_checked')
            ->where('lower(a.role_name)!= "public"')
            ->select();
			
        $this->data['grants'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants` a left join '.Backstage::gi()->db_table_prefix.'user_grants b on a.id = b.grant_id and user_id = '.$this->data['request']->parameters['user_id'])
            ->fields('a.id, a.resource_name, a.resource_id, a.grant_type, a.resource_type, case b.user_id when '.$this->data['request']->parameters['user_id'].' then 1 else 0 end is_checked')
            ->where('resource_type = "modules"')
            ->select();
								
        return $this->data;
    }        
	
	public function getUserResourceGrants()
    {
		$this->data['reqource_types'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grant_resource_types`')
            ->fields('*')
            ->select();

			$this->data['grants'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants` a left join '.Backstage::gi()->db_table_prefix.'user_grants b on a.id = b.grant_id and user_id = '.$this->data['request']->parameters['object_id'])
            ->fields('a.id, a.resource_name, a.resource_id, a.grant_type, a.resource_type, case b.user_id when '.$this->data['request']->parameters['object_id'].' then 1 else 0 end is_checked')
            ->where('resource_type = "modules"')
            ->select();
								
        return $this->data;
    }    
	
	public function getRoleGrants()
    {
        $this->data['grants'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants` a left join '.Backstage::gi()->db_table_prefix.'role_grants b on a.id = b.grant_id and role_id = '.$this->data['request']->parameters['role_id'])
            ->fields('a.id, a.resource_name, a.resource_id, a.grant_type, a.resource_type, case b.role_id when '.$this->data['request']->parameters['role_id'].' then 1 else 0 end is_checked')
            ->where('resource_type = "modules"')
            ->select();		
		
        return $this->data;
    }
	
	public function getRoleResourceGrants()
    {
		$this->data['reqource_types'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grant_resource_types`')
            ->fields('*')
            ->select();

        $this->data['grants'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants` a left join '.Backstage::gi()->db_table_prefix.'role_grants b on a.id = b.grant_id and role_id = '.$this->data['request']->parameters['object_id'])
            ->fields('a.id, a.resource_name, a.resource_id, a.grant_type, a.resource_type, case b.role_id when '.$this->data['request']->parameters['object_id'].' then 1 else 0 end is_checked')
            ->where('resource_type = "modules"')
            ->select();		
		
        return $this->data;
    }    	
	
	public function getResourceGrantsList()
	{
		$where = '';
		if (isset($this->data['parent_id']))
			$where = 'c.parent_id = '.$this->data['parent_id'];
		if (!empty($this->data['resource_field_name']))
			$resource_field_name = $this->data['resource_field_name'];
		else
			$resource_field_name = substr($this->data['resource_name'], 0, -1).'_name';
		switch ($this->data['object_type'])
		{
			case 'user':
			$this->data['resource_grants'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.$this->data['resource_name'].'` c left join `'.Backstage::gi()->db_table_prefix.'grants` a on a.resource_id = c.id and a.resource_type = "'.$this->data['resource_name'].'" left join '.Backstage::gi()->db_table_prefix.'user_grants b on a.id = b.grant_id and user_id = '.$this->data['object_id'])
				->fields('c.id resource_id, c.'.$resource_field_name.' resource_name, a.id, a.grant_type, "'.$this->data['resource_name'].'" resource_type, case b.user_id when '.$this->data['object_id'].' then 1 else 0 end is_checked')
				->where($where)
				->select();
			break;
			case 'role':
			$this->data['resource_grants'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.$this->data['resource_name'].'` c left join `'.Backstage::gi()->db_table_prefix.'grants` a on a.resource_id = c.id and a.resource_type = "'.$this->data['resource_name'].'" left join '.Backstage::gi()->db_table_prefix.'role_grants b on a.id = b.grant_id and role_id = '.$this->data['object_id'])
				->fields('c.id resource_id, c.'.$resource_field_name.' resource_name, a.id, a.grant_type, "'.$this->data['resource_name'].'" resource_type, case b.role_id when '.$this->data['object_id'].' then 1 else 0 end is_checked')
				->where($where)
				->select();
			break;
		}
					
        return $this->data;		
	}
	
	public function saveGrants()
	{
		if ($this->data['params_form']['resource_type'] === 'resources')
			$where = 'resource_type != "modules"';
		elseif($this->data['params_form']['resource_type'] === 'modules')
			$where = 'resource_type = "modules"';
		switch ($this->data['params_form']['type'])
		{
			case 'user':
				$roles = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'roles a left join '.Backstage::gi()->db_table_prefix.'user_grants b on a.id = b.role_id and user_id = '.$this->data['params_form']['object_id'])
					->fields('a.id, a.role_name, b.user_id, case b.user_id when '.$this->data['params_form']['object_id'].' then 1 else 0 end is_checked')
					->where('lower(a.role_name)!= "public"')
					->select();
				foreach ($roles as $role)
				{
					if (isset($this->data['roles_form']['role'][$role->id]))
					{
						if ($role->is_checked == 0)
							$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'user_grants')
							->values(array('user_id'=>$this->data['params_form']['object_id'],'role_id'=>$role->id))
							->insert();
					}
					else
					{
						$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'user_grants')
						->where('role_id = '.$role->id.' and user_id = '.$this->data['params_form']['object_id'])
						->delete();
					}
				}	
				
				$grants = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants` a left join '.Backstage::gi()->db_table_prefix.'user_grants b on a.id = b.grant_id and user_id = '.$this->data['params_form']['object_id'])
					->fields('a.id, a.resource_name, a.resource_id, a.grant_type, a.resource_type, case b.user_id when '.$this->data['params_form']['object_id'].' then 1 else 0 end is_checked')
					->where($where)
					->select();

				foreach ($grants as $grant)
				{
					if (isset($this->data['grants_form']['grant'][$grant->id]))
					{
						if ($grant->is_checked == 0)
							$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'user_grants')
							->values(array('user_id'=>$this->data['params_form']['object_id'],'grant_id'=>$grant->id))
							->insert();
					}
					else
					{
						$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'user_grants')
						->where('grant_id = '.$grant->id.' and user_id = '.$this->data['params_form']['object_id'])
						->delete();
					}
				}		
				
			break;
			case 'role':
				$grants = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants` a left join '.Backstage::gi()->db_table_prefix.'role_grants b on a.id = b.grant_id and role_id = '.$this->data['params_form']['object_id'])
					->fields('a.id, a.resource_name, a.resource_id, a.grant_type, a.resource_type, case b.role_id when '.$this->data['params_form']['object_id'].' then 1 else 0 end is_checked')
					->where($where)
					->select();

				foreach ($grants as $grant)
				{
					if (isset($this->data['grants_form']['grant'][$grant->id]))
					{
						if ($grant->is_checked == 0)
							$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'role_grants')
							->values(array('role_id'=>$this->data['params_form']['object_id'],'grant_id'=>$grant->id))
							->insert();
					}
					else
					{
						$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'role_grants')
						->where('grant_id = '.$grant->id.' and role_id = '.$this->data['params_form']['object_id'])
						->delete();
					}
				}					
			break;			
		}		
        return $this->data;		
	}	
}
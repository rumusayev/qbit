<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cGrants extends controller
{

	/**
	 * Used to load grants and roles to the viewport (client)
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
    public function getUserGrants()
    {
		$this->data = Loader::gi()->getModel($this->data);
		$grants = array();
		
		foreach ($this->data['grants'] as $grant)
		{
			$grants[$grant->resource_name][$grant->grant_type] = new stdClass();
			$grants[$grant->resource_name][$grant->grant_type]->id = $grant->id;
			$grants[$grant->resource_name][$grant->grant_type]->is_checked = $grant->is_checked;
		}
		$this->data['grants'] = $grants;
		
		// Resource grants
		$this->data['catalogs_grants'] = $this->getResourceGrantsList('catalogs', 'user', $this->data['request']->parameters['user_id']);
		$this->data['contents_grants'] = $this->getResourceGrantsList('contents', 'user', $this->data['request']->parameters['user_id'], null);
		
        $this->data['view_name'] = 'userGrants';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}    
	
	public function getRoleGrants()
    {
		$this->data = Loader::gi()->getModel($this->data);
		$grants = array();
		
		foreach ($this->data['grants'] as $grant)
		{
			$grants[$grant->resource_name][$grant->grant_type] = new stdClass();
			$grants[$grant->resource_name][$grant->grant_type]->id = $grant->id;
			$grants[$grant->resource_name][$grant->grant_type]->is_checked = $grant->is_checked;
		}
		$this->data['grants'] = $grants;
		
		// Resource grants
		$this->data['catalogs_grants'] = $this->getResourceGrantsList('catalogs', 'role', $this->data['request']->parameters['role_id']);
		$this->data['contents_grants'] = $this->getResourceGrantsList('contents', 'role', $this->data['request']->parameters['role_id'], null);
		
        $this->data['view_name'] = 'roleGrants';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}
	
	private function getResourceGrantsList($resource_name, $object_type, $object_id, $parent_id = 0)
	{
		$out = '';
		$res_data['request'] = new stdClass();
		$res_data['request']->module_name = 'grants';
		$res_data['request']->controller_name = 'grants';
		$res_data['request']->action_name = 'getResourceGrantsList';
		if ($parent_id !== null)
			$res_data['parent_id'] = $parent_id;
		$res_data['resource_name'] = $resource_name;
		$res_data['object_type'] = $object_type;
		$res_data['object_id'] = $object_id;
		$res_data = Loader::gi()->getModel($res_data);
		
		$this->data['resource_name'] = $resource_name;
		$this->data['resource_grants'] = $res_data['resource_grants'];
		
		$grants = array();
		foreach ($this->data['resource_grants'] as $grant)
		{
			$grants[$grant->resource_id][$grant->grant_type] = new stdClass();
			$grants[$grant->resource_id][$grant->grant_type] = $grant;
			$grants[$grant->resource_id]['resource_name'] = $grant->resource_name;
			$grants[$grant->resource_id]['resource_type'] = $grant->resource_type;
			$grants[$grant->resource_id]['resource_id'] = $grant->resource_id;
		}
				
		foreach ($grants as $key=>$grant)
		{
			$this->data['grant'] = $grant;
			$this->data['view_name'] = 'resourceItemGrants';
			$out .= Loader::gi()->getView($this->data);
			if ($parent_id !== null)
				$out .= $this->getResourceGrantsList($resource_name, $object_type, $object_id, $key);
		}
		$this->data['body'] = $out;
		$this->data['view_name'] = 'resourceGrants';
		return Loader::gi()->getView($this->data);
	}
	
	public function saveGrants()
	{
		$this->data['params_form'] = json_decode($this->data['request']->parameters['params_form'], true);
		$this->data['roles_form'] = json_decode($this->data['request']->parameters['roles_form'], true);
		$this->data['grants_form'] = json_decode($this->data['request']->parameters['grants_form'], true);
		$this->data = Loader::gi()->getModel($this->data);
		$this->data['body'] = $this->data['status'];
        return $this->data;		
	}
}
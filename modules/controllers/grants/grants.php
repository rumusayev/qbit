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
		
        $this->data['view_name'] = 'userGrants';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}   	
	
	/**
	 * Used to load grants and roles of resources to the viewport (client)
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
    public function getUserResourceGrants()
    {
		$this->data = Loader::gi()->getModel($this->data);
		$this->data['grants'] = '';
		$this->data['object_type'] = 'user';
			// Resource grants
		foreach ($this->data['reqource_types'] as $resource_type)
		{
			$parent_id = 0;
			$resource_field_name = '';
			if ($resource_type->has_children == 0)
				$parent_id = null;
			$this->data['grants'] .= $this->getResourceGrantsList($resource_type->resource_type, $this->data['object_type'], $this->data['request']->parameters['object_id'], $parent_id, $resource_type->field_name);
		}
        $this->data['view_name'] = 'resourceGrants';
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
				
        $this->data['view_name'] = 'roleGrants';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}	
	
	public function getRoleResourceGrants()
    {
		$this->data = Loader::gi()->getModel($this->data);
		$this->data['grants'] = '';
		$this->data['object_type'] = 'role';
			// Resource grants
		foreach ($this->data['reqource_types'] as $resource_type)
		{
			$parent_id = 0;
			$resource_field_name = '';
			if ($resource_type->has_children == 0)
				$parent_id = null;
			$this->data['grants'] .= $this->getResourceGrantsList($resource_type->resource_type, $this->data['object_type'], $this->data['request']->parameters['object_id'], $parent_id, $resource_type->field_name);
		}
        $this->data['view_name'] = 'resourceGrants';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}
	
	public function getActionsGrants()
    {
		$controllers = array();
		$models = array();
		$iterator = new RecursiveIteratorIterator(
						new RecursiveDirectoryIterator(Backstage::gi()->MODULES_DIR, FilesystemIterator::UNIX_PATHS), 
					RecursiveIteratorIterator::SELF_FIRST);

		foreach($iterator as $file) 
		{
			if(!$file->isDir()) 
			{
				$fp = fopen($file, 'r');
				$method = $buffer = '';
				//echo '<br/>'.str_ireplace('.php', '', $file->getFilename()).'->>><br/>';
				while (!feof($fp)) 
				{
					$buffer .= fread($fp, 2048);
				}			
				if (preg_match_all('/public\s+function\s+(\w+)/i', $buffer, $matches)) 
				{
					if (stristr($file, Backstage::gi()->CONTROLLERS_DIR))
						$controllers[str_ireplace('.php', '', $file->getFilename())] = $matches[1];
					elseif (stristr($file, Backstage::gi()->MODELS_DIR))
						$models[str_ireplace('.php', '', $file->getFilename())] = $matches[1];					
				}
			}
		}
		$combined = array_merge_recursive($controllers, $models);
		array_walk($combined, function(&$data, $key)
		{
			$data = array_unique($data);
		});
		
        $this->data['structure'] = $combined;		
		$this->data = Loader::gi()->getModel($this->data);
        $this->data['view_name'] = 'actionsGrants';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}
	
	private function getResourceGrantsList($resource_name, $object_type, $object_id, $parent_id = 0, $resource_field_name = '')
	{
		$out = '';
		if ($parent_id === null || $parent_id === 0)
			$out .= '<h2>'.$resource_name.'</h2>';

		$res_data['request'] = new stdClass();
		$res_data['request']->module_name = 'grants';
		$res_data['request']->controller_name = 'grants';
		$res_data['request']->action_name = 'getResourceGrantsList';
		if ($parent_id !== null)
			$res_data['parent_id'] = $parent_id;
		$res_data['resource_name'] = $resource_name;
		$res_data['object_type'] = $object_type;
		$res_data['object_id'] = $object_id;
		$res_data['resource_field_name'] = $resource_field_name;
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
			{
				$parent_resources = $this->getResourceGrantsList($resource_name, $object_type, $object_id, $key, $resource_field_name);
				if ($parent_resources != '')
					$out .= "<div style='padding-left:10px'>$parent_resources</div>";
			}
		}
		$this->data['body'] = $out;
		return $out;
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
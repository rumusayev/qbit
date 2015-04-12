<?php

/**
 * @package    filterChain
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Pretorian
{
    protected static $instance = null;  // object instance

    function __construct() {}
    private function __clone()     {  }
    private function __wakeup()    {  }

    public static function gi()
    {
        if (is_null(self::$instance)) 
		{
			self::$instance = new Pretorian;
		}
        return self::$instance;
    }

	/**
	 * Checks permissions
	 *
	 * @param string Resource name
	 * @param string Grant types, separated by comma or given as array (GET, POST, PUT, DELETE)
	 * @param string Resource ID
	 * @param string Check type (p - for permission checks, f - to filter resources)
	 * @return boolean
	 */	
    public function check($resource_name, $grant_types, $resource_id = 0, $check_type = 'p')
    {
		$data['module_name'] = 'common';
		$data['model_name'] = 'pretorian';
		$data['action_name'] = 'check';
		$data['resource_name'] = $resource_name;
		$data['resource_id'] = $resource_id;
		$data['check_type'] = $check_type;
		$data['grant_types'] = $grant_types;
		$data = Loader::gi()->getModel($data);
		
		if ($data['check_count'] == 0) return false;
		return true;
    }
	
	/**
	 * Filters resources that a user doesn't has access to
	 *
	 * @param Array Resources
	 * @param string Table name
	 * @param string Resource field name
	 * @param string Grant type (GET, PUT, POST, DELETE)
	 * @return Array Filtered resources
	 */	
	public function filter($resource_arr, $table, $resource_field_name, $grant_type = 'GET')
	{
        foreach ($resource_arr as $key=>$resource_item)
        {
            if (!Pretorian::gi()->check($table, $grant_type, $resource_item->{$resource_field_name}, 'f'))
                unset($resource_arr[$key]);
        }  		
		return $resource_arr;
	}
}
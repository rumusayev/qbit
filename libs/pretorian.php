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

    public function check($resource_name, $grant_types, $resource_id = 0)
    {
		$data['module_name'] = 'common';
		$data['model_name'] = 'pretorian';
		$data['action_name'] = 'check';
		$data['resource_name'] = $resource_name;
		$data['resource_id'] = $resource_id;
		$data['grant_types'] = $grant_types;
		$data = Loader::gi()->getModel($data);
		
		if ($data['check_count'] == 0) return false;
		return true;
    }
}
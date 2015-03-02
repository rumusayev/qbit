<?php

/**
 * @package    Arhlog
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Arhlog 
{
	
	private static $instance = null;

	private function __construct() 
	{	
	}
	
	/** SINGLETON PATTERN */
	public static function gi() 
	{	
		if (is_null(self::$instance))
		{
			self::$instance = new Arhlog();
		}
		return self::$instance;	
	}       
	
	/**
	 * Commit saving data to arhlog
	 *
	 * @table_name string Table name
	 * @resource_name string Resource name
	 * @hash_group string Hash group for grouping one-package logs
	 * @condition_part string Is needed when there is an additional version checking condition (e.g. account_number by which records should be rollbacked then)
	 * @type string i - insert, u - update, d - delete
	 * @where string Additional where clause
	 * @new_values string Used to store new values with old values, only applicable for the "u" type
	 * @priority int Priority of the operation
	 * @return string Status
	 */	 
	public static function commit($table_name, $resource_name, $hash_group, $condition_part = null, $type = 'u', $where = '', $new_values = null, $priority = 1)
	{
		$data['module_name'] = 'common';
		$data['model_name'] = 'arhlog';
		$data['action_name'] = 'commit';
		$data['table_name'] = $table_name;
		$data['resource_name'] = $resource_name;
		$data['hash_group'] = $hash_group;
		$data['condition_part'] = $condition_part;
		$data['type'] = $type;
		$data['where'] = $where;
		$data['new_values'] = $new_values;
		$data['priority'] = $priority;
        $data = Loader::gi()->getModel($data);
		return $data['status'];
	}        
        
	/**
	 * Rolling back saved data to its old state
	 *
	 * @resource_name string Resource name
	 * @condition_part string Is needed when there is an additional version checking condition (e.g. account_number by which records should be rollbacked then)
	 * @return string Status
	 */	 
	public static function rollback($resource_name, $condition_part = null)
	{
		$data['module_name'] = 'common';
		$data['model_name'] = 'arhlog';
		$data['action_name'] = 'rollback';
		$data['resource_name'] = $resource_name;
		$data['condition_part'] = $condition_part;
        $data = Loader::gi()->getModel($data);
		return $data['status'];
	}        
}
<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

class Model
{
	protected $dbmanager;
	protected $data = array();
	
	function __construct($data)
	{
		$this->data = $data;
		if (!isset($this->data['db_adapter']))
			$this->data['db_adapter'] = Backstage::gi()->db_adapter;
		$db_class_name = $this->data['db_adapter'];
		if (isset($data['update_adapter']) && $data['update_adapter'])
		{
			//if (version_compare(phpversion(), '5.3') < 0)
			//{
				$gi = call_user_func(array($db_class_name, 'gi'));	// PHP < 5.3
				$gi->destroy();
			//}
			//else
			//	$db_class_name::gi()->destroy();	// PHP >= 5.3
		}
		if (version_compare(phpversion(), '5.3.0', '<') == true)
			$gi = call_user_func(array($db_class_name, 'gi'));	// PHP < 5.3
		else
			$gi = $db_class_name::gi();	// PHP >= 5.3
		$this->dbmanager = new DBManager($gi);
	}

	/**
	 * @description		Returns the resource
	 */	
	public function get()
	{	
		if (!isset($this->data['request']->data_type))
			$this->data['request']->data_type = Backstage::gi()->default_data_type;
		if (!isset($this->data['request']->resource_name))
			$this->data['request']->resource_name = $this->data['request']->model_name;
			
		$where = '';
		$order = 'id';
		
		if (isset($this->data['request']->parameters['where']) && $this->data['request']->parameters['where'] != '')
			$where = $this->strict($this->data['request']->parameters['where']);

		if (isset($this->data['request']->parameters['order']) && $this->data['request']->parameters['order'] != '')
			$order = $this->strict($this->data['request']->parameters['order']);

        if (isset($this->data['request']->parameters['id']))
        {
            if ($where != '')
                $where .= ' AND ';
            $where .= 'id = '.$this->strict($this->data['request']->parameters['id']);
        }
        if (isset($this->data['request']->parameters['parent_id']))
        {
            if ($where != '')
                $where .= ' AND ';
            $where .= 'parent_id = '.$this->strict($this->data['request']->parameters['parent_id']);
        }
		$this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.$this->data['request']->resource_name)
												->fields('*')
												->where($where)
												->order($order)
												->select();
        foreach ($this->data['items'] as $key=>$item)
        {
			$translations = Translations::gi()->getTranslations($this->data['request']->resource_name, $item->id, Backstage::gi()->portal_current_lang);
			if (!empty($translations)) 
			{
				foreach ($translations as $field => $translation)
					$this->data['items'][$key]->$field = $translation->translation;
			}				
        }
		
		if ($this->data['request']->data_type === 'json')
			$this->data['items'] = json_encode($this->data['items']);
		$this->data['body'] = $this->data['items'];
		
		return $this->data;
	}	
	
	/**
	 * @description		Returns the global array $data
	 */		
	function getData()
	{
		return $this->data;
	}
	
	/**
	 * @description		Returns the dbmanager object used by a model
	 */		
	function getDBManager()
	{
		return $this->dbmanager;
	}
	
	
	/**
	 * @description		Used to strict incoming parameter from injections
	 */		
	protected function strict($param)
	{
		$needle = array('select', 'update', 'delete', 'create', 'where', 'union', 'set', ';', '--');
		if (is_array($param))
			foreach ($param as $par)
			{
				
			}
		else
			$param = str_ireplace($needle, '', $param);
		return $param;
	}	
		
}
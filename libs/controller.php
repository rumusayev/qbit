<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

class Controller 
{
	protected $data = array();
    
    function __construct($data)
    {
		$this->data = $data;
    }

	/**
	 * @description		Returns the resource
	 */	
	public function get()
	{	
		if (!isset($this->data['request']->data_type))
			$this->data['request']->data_type = Backstage::gi()->default_data_type;
		if (!isset($this->data['request']->resource_name))
			$this->data['request']->resource_name = $this->data['request']->module_name;	
		$this->data = Loader::gi()->getModel($this->data);
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
}
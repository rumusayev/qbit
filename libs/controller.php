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
		$this->data = Loader::gi()->getModel($this->data);

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
<?php

/**
 * @package    filterChain
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class FilterChain
{
	private $data = array();
	private $response;
	
	function __construct($data) 
	{	
		$this->data = $data;
		$this->response = new stdClass();
		$this->response->status = 200;
		$this->run();
	}
	
	private function run()
	{			
			// Check if the resource exists
		if (!isset($this->data['request']->controller_name))
			$this->data['request']->controller_name = $this->data['request']->module_name;
		if (!isset($this->data['request']->module_name))
			$this->data['request']->module_name = $this->data['request']->controller_name;

		$controller_file = Backstage::gi()->CONTROLLERS_DIR.$this->data['request']->module_name.Backstage::gi()->DR.$this->data['request']->controller_name.'.php';
		$model_file = Backstage::gi()->MODELS_DIR.$this->data['request']->module_name.Backstage::gi()->DR.$this->data['request']->controller_name.'.php';
		
			// Check for permissions
		if(!file_exists($controller_file) && !file_exists($model_file)) 
			throw new QException(array('ER-00014', $controller_file));
		
		if (Backstage::gi()->pretorian_check_level === 'a')
			$permitted = Pretorian::gi()->check($this->data['request']->controller_name, $this->data['request']->method)&&Pretorian::gi()->check($this->data['request']->controller_name, $this->data['request']->action_name);
		else
			$permitted = Pretorian::gi()->check($this->data['request']->controller_name, $this->data['request']->method);
		
		if (!$permitted)
		{
			if ($this->data['request']->routing === 'static')
			{
				if ($this->data['request']->module_name === 'admin')
					$action_name = 'get';
				else 
					$action_name = 'getPublic';
				//Logger::getLogger('main')->warn('Goto auth page from filterchain');
				$this->data['show_messages'] = '-';
				$this->data['WARNING'] = Translations::gi()->access_denied;
				$this->data['request']->module_name = 'auth';
				$this->data['request']->controller_name = 'auth';
				$this->data['request']->action_name = $action_name;
			}
			else
				switch($this->data['request']->method)  
				{
					case 'GET':  
						throw new QException(array('ER-00010'));
						break;  
					case 'POST':  
						throw new QException(array('ER-00011'));
						break;  
					case 'PUT':  
						throw new QException(array('ER-00012'));
						break;
					case 'DELETE':  
						throw new QException(array('ER-00013'));
						break;  
				}
		}

		if(file_exists($controller_file))
		{
			$this->data = Loader::gi()->getController($this->data);
				// Let's not return error when internal resources are used
			if (!isset($this->data['body']) && $this->data['request']->routing !== 'internal')
				throw new QException(array('ER-00016', $controller_file));
		}
		else
		{
			$this->data = Loader::gi()->getModel($this->data);
				// Let's not return error when internal resources are used
			if (!isset($this->data['body']) && $this->data['request']->routing !== 'internal')
				throw new QException(array('ER-00016', $model_file));
		}
		
		if (!isset($this->data['body']) && $this->data['request']->routing === 'internal')
			$this->data['body'] = '';
		
		$this->response->body = $this->data['body'];
		$this->data['response'] = $this->response;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
}
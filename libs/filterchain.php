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
			// Check for permissions
		if (($this->data['request']->module_name === 'admin' || $this->data['request']->module_name === 'cms') && !Pretorian::gi()->check($this->data['request']->module_name, $this->data['request']->method))
		{
			$this->data['request']->module_name = 'auth';
			$this->data['request']->controller_name = 'auth';
			$this->data['request']->action_name = 'get';
		}
		
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
			
		if (!Pretorian::gi()->check($this->data['request']->module_name, $this->data['request']->method))
			{
			Logger::getLogger('main')->warn('goto auth page from filterchain');
			$this->data['request']->module_name = 'auth';
			$this->data['request']->controller_name = 'auth';
			$this->data['request']->action_name = 'get';
			}
			// switch($this->data['request']->method)  
			// {
			// 	case 'GET':  
			// 		throw new QException(array('ER-00010'));
			// 		break;  
			// 	case 'POST':  
			// 		throw new QException(array('ER-00011'));
			// 		break;  
			// 	case 'PUT':  
			// 		throw new QException(array('ER-00012'));
			// 		break;
			// 	case 'DELETE':  
			// 		throw new QException(array('ER-00013'));
			// 		break;  
			// }
			
		if(file_exists($controller_file))
		{
			$this->data = Loader::gi()->getController($this->data);
			if (!isset($this->data['body']))
				throw new QException(array('ER-00016', $controller_file));
		}
		else
		{
			$this->data = Loader::gi()->getModel($this->data);
			if (!isset($this->data['body']))
				throw new QException(array('ER-00016', $model_file));
		}
			
		$this->response->body = $this->data['body'];
		$this->data['response'] = $this->response;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
}

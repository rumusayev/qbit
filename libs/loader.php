<?php
/**
 * @package    MVC
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

class Loader
{
    protected static $instance = null;  // object instance
    private static $vars = array();
	
    private function __construct() {  }
    private function __clone()     {  }
    private function __wakeup()    {  }
    public static function gi()
	{
        if (is_null(self::$instance)) 
		{
            self::$instance = new Loader;
        }
        return self::$instance;
    }
	
	public function getController($data) 
	{
		if (!isset($data['request']))
			$data['request'] = new stdClass();
			
		if (isset($data['controller_name']))
			$data['request']->controller_name = $data['controller_name'];

		if (isset($data['module_name']))
			$data['request']->module_name = $data['module_name'];		
			
		if (isset($data['action_name']))
			$data['request']->action_name = $data['action_name'];
			
		if (!isset($data['request']->controller_name) && !isset($data['request']->module_name))
			throw new QException(array('ER-00004'));
			
		if (!isset($data['request']->controller_name))
			$data['request']->controller_name = $data['request']->module_name;
		if (!isset($data['request']->module_name))
			$data['request']->module_name = $data['request']->controller_name;
			
		$controller_file = Backstage::gi()->CONTROLLERS_DIR.$data['request']->module_name.Backstage::gi()->DR.$data['request']->controller_name.'.php';
		
		if(!file_exists($controller_file)) 
			throw new QException(array('ER-00007', $controller_file));

		$controller_name = Backstage::gi()->controller_prefix.$data['request']->controller_name;
		require_once($controller_file); //******************************************VQMOD HERE*************************************8
		
		if(!class_exists($controller_name))
			throw new QException(array('ER-00015', $controller_name, $controller_file));		

		$controller = new $controller_name($data);

		$action_name = $data['request']->action_name;
		if(!method_exists($controller, $action_name))
			throw new QException(array('ER-00009', $action_name, $controller_file));
			
		$data = $controller->$action_name();		
		return $data;
	}
	
	public function getModel($data) 
	{
		if (!isset($data['request']))
			$data['request'] = new stdClass();
			
		if (isset($data['model_name']))
			$data['request']->model_name = $data['model_name'];

		if (isset($data['module_name']))
			$data['request']->module_name = $data['module_name'];
			
		if (isset($data['action_name']))
			$data['request']->action_name = $data['action_name'];
				
		if (!isset($data['request']->model_name) && !isset($data['request']->module_name))
			throw new QException(array('ER-00005'));
			
		if (!isset($data['request']->model_name))
			$data['request']->model_name = $data['request']->module_name;
		if (!isset($data['request']->module_name))
			$data['request']->module_name = $data['request']->model_name;

		$model_file = Backstage::gi()->MODELS_DIR.$data['request']->module_name.Backstage::gi()->DR.$data['request']->model_name.'.php';
		
		if(!file_exists($model_file)) 
			throw new QException(array('ER-00008', $model_file));

		$model_name = Backstage::gi()->model_prefix.$data['request']->model_name;
		require_once($model_file); //*********************************************** VQMOD HERE ****************************************

		if(!class_exists($model_name))
			throw new QException(array('ER-00015', $model_name, $model_file));	
			
		$model = new $model_name($data);
		
		$action_name = $data['request']->action_name;
		if(!method_exists($model, $action_name))
			throw new QException(array('ER-00009', $action_name, $model_file));
			
		$data = $model->$action_name();
		return $data;		
	}	

	public function getView($data) 
	{
		if (!isset($data['request']))
			$data['request'] = new stdClass();
			
		if (isset($data['view_name']))
			$data['request']->view_name = $data['view_name'];

		if (isset($data['module_name']))
			$data['request']->module_name = $data['module_name'];
				
		if (!isset($data['request']->view_name) && !isset($data['request']->module_name))
			throw new QException(array('ER-00017'));
			
		if (!isset($data['request']->view_name))
			$data['request']->view_name = $data['request']->module_name;
		if (!isset($data['request']->module_name))
			$data['request']->module_name = $data['request']->view_name;

		$view_file = Backstage::gi()->VIEWS_DIR.$data['request']->module_name.Backstage::gi()->DR.$data['request']->view_name.'.php';
		
		if(!file_exists($view_file)) 
			throw new QException(array('ER-00018', $view_file));

		if(is_array($data))
		{
			extract($data);	// array to variables
		}			
		ob_start();
		
		if(isset($data['show_messages']))
		{
			$messages_viewer_file = Backstage::gi()->VIEWS_DIR.'common/messages.php';
			include($messages_viewer_file);	
		}
		
		include($view_file); //*************************************************VQMOD HERE*****************************************8
		return ob_get_clean();		
	}	

	public function getLQ($data) 
	{
		$lq = new LQ($data);
		$lq->handleData();
		$data = $lq->getData();		
		
		return $data;		
	}
	
	public function parseLQ($data) 
	{
		$lq = new LQ($data);
		$lq->parseData();
		$data = $lq->getData();		
		
		return $data;		
	}
	
	/**
	 * @description		Returns the resource (ATTENTION - use of this method could lead to system outages)
	 * @method	POST, PUT, GET, DELETE
	 * @url		Acceptable request url
	 * @data 	Data that will be sent as parameters: array("param" => "value") ==> index.php?param=value
	 */	
	public function callAPI($method, $url, $data = array())
	{
		$curl = curl_init();
			// Core parameters that are needed to be passed
		if (!isset($data['lang']))
			$data['lang'] = Backstage::gi()->portal_current_lang;
			
		switch ($method)
		{
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);
				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}

		// Optional Authentication:
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "username:password");

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		return curl_exec($curl);
	}
	
	/**
	 * @description		Calls a module without need to call a controller or a model from the source
	 * @method	POST, PUT, GET, DELETE
	 * @url		Acceptable request url
	 * @data 	Data that will be sent as parameters: array("param" => "value") ==> index.php?param=value
	 */	
	public function callModule($method, $url, $parameters = array())
	{	
		$routes = explode('/', $url);
		$data['request'] = new stdClass();		
		$data['request']->module_name = $routes[0];
		$data['request']->controller_name = $routes[0];
		if (isset($routes[1]))
			$data['request']->action_name = $routes[1];
		else
			$data['request']->action_name = $method;
		
		$data['request']->parameters = $parameters;
		$data['request']->method = $method;
		

			// Action name - parsed from the url as action{.data_type} where data_type could be json, xml and etc (default is text).
			// If action name is empty we should use the request method and data_type is taken from the module name as module{.data_type}
		$data['request']->data_type = Backstage::gi()->default_data_type;
			
		if (isset($routes[1]))
		{
			$data['request']->action_name = $routes[1];
			if (strstr($data['request']->action_name, '.'))
			{
				$action_name_parts = explode('.',$data['request']->action_name);
				$data['request']->action_name = $action_name_parts[0];
				$data['request']->data_type = $action_name_parts[1];
			}			
		}
		else
		{
			$data['request']->action_name = strtolower($method);
			if (strstr($data['request']->module_name, '.'))
			{
				$module_name_parts = explode('.', $data['request']->module_name);
				$data['request']->module_name = $module_name_parts[0];
				$data['request']->data_type = $module_name_parts[1];
			}				
		}

			// Check if the resource exists
		if (!isset($data['request']->controller_name))
			$data['request']->controller_name = $data['request']->module_name;
		if (!isset($data['request']->module_name))
			$data['request']->module_name = $data['request']->controller_name;

		$controller_file = Backstage::gi()->CONTROLLERS_DIR.$data['request']->module_name.Backstage::gi()->DR.$data['request']->controller_name.'.php';
		$model_file = Backstage::gi()->MODELS_DIR.$data['request']->module_name.Backstage::gi()->DR.$data['request']->controller_name.'.php';
		
			// Check for permissions
		if(!file_exists($controller_file) && !file_exists($model_file)) 
			throw new QException(array('ER-00014', $controller_file));
			
		if (!Pretorian::gi()->check($data['request']->module_name, $data['request']->method))
			{
			$this->data['show_messages']='-';
            $this->data['WARNING']=Translations::gi()->access_denied;
			$data['request']->module_name = 'auth';
			$data['request']->controller_name = 'auth';
			$data['request']->action_name = 'getPublic';
			}
			// switch($data['request']->method)  
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
			$data = Loader::gi()->getController($data);
		else
			$data = Loader::gi()->getModel($data);
			
		return $data;
	}	
}

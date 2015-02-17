<?php

/**
 * @package    front
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Core 
{
	private $data = array();
	private $request;
	
	function __construct() 
	{
			// Child directories
		define('TEMPLATE_DIR', TEMPLATES_DIR.Backstage::gi()->template_name.DR);
		define('CONTROLLERS_DIR', MODULES_DIR.'controllers'.DR);
		define('MODELS_DIR', MODULES_DIR.'models'.DR);
		define('VIEWS_DIR', MODULES_DIR.'views'.DR);
		
		define('MATERIALS_URL', str_replace(ROOT_DIR, Backstage::gi()->portal_url, MATERIALS_DIR));                
		define('EXTERNAL_URL', str_replace(ROOT_DIR, Backstage::gi()->portal_url, EXTERNAL_DIR));                
		define('TEMPLATE_URL', str_replace(ROOT_DIR, Backstage::gi()->portal_url, TEMPLATE_DIR));

		Backstage::gi()->DR = DR;
		Backstage::gi()->ROOT_DIR = ROOT_DIR;
		Backstage::gi()->LIBS_DIR = LIBS_DIR;
		Backstage::gi()->CONFIG_DIR = CONFIG_DIR;
		Backstage::gi()->LANGS_DIR = LANGS_DIR;
		Backstage::gi()->EXTERNAL_DIR = EXTERNAL_DIR;
		Backstage::gi()->MODULES_DIR = MODULES_DIR;
		Backstage::gi()->TEMPLATES_DIR = TEMPLATES_DIR;
		Backstage::gi()->TEMPLATE_DIR = TEMPLATE_DIR;
		Backstage::gi()->CGI_DIR = CGI_DIR;
		Backstage::gi()->MATERIALS_DIR = MATERIALS_DIR;

		Backstage::gi()->CONTROLLERS_DIR = CONTROLLERS_DIR;
		Backstage::gi()->MODELS_DIR = MODELS_DIR;
		Backstage::gi()->VIEWS_DIR = VIEWS_DIR;

		Backstage::gi()->MATERIALS_URL = MATERIALS_URL;
		Backstage::gi()->EXTERNAL_URL = EXTERNAL_URL;
		Backstage::gi()->TEMPLATE_URL = TEMPLATE_URL;
		
		$_SESSION['EXTERNAL_URL'] = EXTERNAL_URL;
		$_SESSION['EXTERNAL_DIR'] = EXTERNAL_DIR;

		$this->request = new stdClass();
		
			// Let's run initiation
		$this->run();
	}

	private function run() 
	{
		// Parse the request, make first data and pass it to the filter chain in next steps
		try
		{
			$this->data = $this->parseRequest();
		}
			// Let's catch any qBit exception and return it with responser
		catch (QException $e)
		{
			$this->data['excepion'] = new stdClass();
			$this->data['excepion']->message = $e;
			$this->data['excepion']->code = $e->getErrorCode();
		}
		catch (Exception $e)
		{
			throw $e;
		}
		new Responser($this->data);
	}
	
		// Get full url address (http://www.name.com/urlpart1/urlpart2?get_params)
	private function getFullURL()
	{
		$page_url = "http";
		if (isset($_SERVER["HTTPS"]) && !Backstage::gi()->development_mode)
			$page_url .= "s";
		elseif (Backstage::gi()->development_mode)
			$page_url .= "s";
		
		$page_url .= "://";
		if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" && !Backstage::gi()->development_mode)
		{
			$page_url .= @$_SERVER["SERVER_NAME"].":".@$_SERVER["SERVER_PORT"].@$_SERVER["REQUEST_URI"];
		} 
		else 
		{
			$page_url .= @$_SERVER["SERVER_NAME"].@$_SERVER["REQUEST_URI"];
		}
		return $page_url;
	}	

		// Get clean URL (e.g. urlpart1/urlpart2?get_params)
	private function getCleanURL()
	{
		global $argc, $argv;	// Global variables used when qBit is running as a daemon process
	
		if (@$argc > 1)
			$clean_url = $argv[1];
		else
		{
			$curr_url = explode(Backstage::gi()->portal_url, $this->request->full_url);
			$curr_url = $curr_url[1];
		}
		if ($curr_url=='' || $curr_url=='/') return '';		// Clean URL is empty in this case
		$clean_url = $this->toValidURL($curr_url);			// Format URL string from unnecessary symbols
		$first_symb = substr($curr_url,0,1);
		if ($first_symb=='/')
			$curr_url = substr($curr_url,1);
		$last_symb = substr($clean_url,-1);
		if ($last_symb=='/')
			$clean_url = substr($clean_url,0,-1);
		return $clean_url;
	}
	
		// Format URL string from unnecessary symbols (e.g. http://www.name.com//subname??param/// -> http://www.name.com/subname--param/)
	private function toValidURL($curr_url)
	{
		$edited_url = preg_replace("/[^\w\/\?\.=&]+/", "-", $curr_url);	// replace all non-secure symbols with "-"
		$edited_url = trim($edited_url);
		if ($edited_url=='' || $edited_url=='/') return '';
			// Clean unwanted '/'-es
		if (!strstr($edited_url,'/'))
		{
			$new_url_arr[] = $edited_url;
		} 
		else 
		{
			$url_arr = explode('/', $edited_url);
			foreach($url_arr as $url_item) 
			{
				if($url_item != '')
					$new_url_arr[] = $url_item;
			}
		}
		$edited_url = implode('/',$new_url_arr);
		$last_item_index = count($new_url_arr)-1;
		$last_url_item = $new_url_arr[$last_item_index] ;
		if (!strstr($last_url_item, '.'))
		{
			$edited_url = $edited_url.'/';
		}
		return $edited_url;
	}	
	
		// Parse full URL to an array
	private function parseURL()
	{
			// Get full url address (http://www.name.com/urlpart1/urlpart2/)
		$this->request->full_url = $this->getFullURL();
		Backstage::gi()->full_url = $this->request->full_url;	
	
			// Get clean url address (from the address string or shell) (urlpart1/urlpart2)
		$this->request->clean_url = $this->getCleanURL();	
		Backstage::gi()->clean_url = $this->request->clean_url;	
	
		$url = $this->request->clean_url;
		$url_parts = explode('?', $url);
		
			// Parameters url after ? 
		Backstage::gi()->parameters_url = '';
		if (isset($url_parts[1]))
		{
			$this->request->parameters_url = $url_parts[1];
			Backstage::gi()->parameters_url = $this->request->parameters_url;
		}
		$url_arr = explode('/', $url_parts[0]);
		
			// Exceptions with wrong request (pls. see errors.xml) - depricated
		/*
		if (count($url_arr) != 2 && $this->request->clean_url != '')
			throw new QException(array('ER-00001', $this->request->clean_url));
		*/
		
			// Module and action names
		if ($this->request->clean_url == '' || $this->request->clean_url[0]=='?')
		{
			$url_arr[0] = Backstage::gi()->default_module_name;
			$url_arr[1] = Backstage::gi()->default_action_name;			
		}
		
		if (empty($url_arr[0]))
			throw new QException(array('ER-00002'));
			
		$this->request->module_name = $url_arr[0];
			
			// Action name - parsed from the url as action{.data_type} where data_type could be json, xml and etc (default is text).
			// If action name is empty we should use the request method and data_type is taken from the module name as module{.data_type}
		$this->request->data_type = Backstage::gi()->default_data_type;
			
		if (empty($url_arr[1]))
		{
			//throw new QException(array('ER-00003'));
			$this->request->action_name = strtolower($this->request->method);
			if (strstr($this->request->module_name, '.'))
			{
				$module_name_parts = explode('.', $this->request->module_name);
				$this->request->module_name = $module_name_parts[0];
				$this->request->data_type = $module_name_parts[1];
			}			
		}
		else
		{
			$this->request->action_name = $url_arr[1];		
			if (strstr($this->request->action_name, '.'))
			{
				$action_name_parts = explode('.', $this->request->action_name);
				$this->request->action_name = $action_name_parts[0];
				$this->request->data_type = $action_name_parts[1];
			}
		}
	}	
	
	private function parseRequest()
	{
			// Defining type of the request
		$this->request->method = $_SERVER['REQUEST_METHOD'];
		switch($this->request->method)  
		{
			case 'GET':  
				$this->request->parameters = $_GET;
				break;  
			case 'POST':  
				$this->request->parameters = $_POST;
				break;  
			case 'PUT':  
				parse_str(file_get_contents('php://input'), $this->request->parameters);
				break;
			case 'DELETE':  
				parse_str(file_get_contents('php://input'), $this->request->parameters);
				break;  
			default:  
				throw new QException(array('ER-00006', $this->method));
		}
		
			// Parsing the URL
		$this->parseURL();
		$this->request->request_url = $this->request->module_name.'/'.$this->request->action_name.'/';
		Backstage::gi()->request_url = $this->request->request_url;
		
			// Route ajax requests
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
		{
				// AJAX ROUTING
			$this->request->routing = 'ajax';
		}
		else
		{
				// NON-AJAX ROUTING
			$this->request->routing = 'static';
		}
		
			// Identify user data
		$this->request->user = $this->getUserData();
		Backstage::gi()->user = $this->request->user;

			// Identify language		
		Backstage::gi()->portal_current_lang = $this->getCurrentLang();
		$data['request'] = $this->request;
		return $data;
	}

	
	private function verifyCookie() 
	{
		if (!isset($_COOKIE['AUTH']) || empty($_COOKIE['AUTH']))
			return false;

		list($login, $expiration, $hmac) = explode('|', $_COOKIE['AUTH']);

		$expired = $expiration;

		if ($expired < time())
			return false;

		$key = hash_hmac('md5', $login . $expiration, 'blabla');
		$hash = hash_hmac('md5', $login . $expiration, $key);

		if ($hmac != $hash)
			return false;
		return $login;
	}	
	
	private function getUserData()
	{
		$user_data = new stdClass();
		if ($login = $this->verifyCookie())
		{
			$user_data->login = $login;
		}
		else
		{
			$user_data->login = '';
			$user_data->password = '';			
		}
		/*
		if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']))
		{
			$user_data->login = '';
			$user_data->password = '';
		}
		else
		{
			$user_data->login = $_SERVER['PHP_AUTH_USER'];
			//$user_data->password = $_SERVER['PHP_AUTH_PW'];
		}
		*/
		return $user_data;
	}
	
	private function getCurrentLang()
	{
        $portal_langs = explode(',', Backstage::gi()->portal_langs);
		if (isset($this->request->parameters['lang']) && in_array($this->request->parameters['lang'], $portal_langs))
		{
			setcookie("portal_current_lang", $this->request->parameters['lang'], time() + 864000, '/');
			$current_lang = strtolower($this->request->parameters['lang']);
		}
		else
		{
			if (!isset($_COOKIE['portal_current_lang']))
			{
				setcookie("portal_current_lang", Backstage::gi()->portal_default_lang, time() + 864000, '/');
				$current_lang = Backstage::gi()->portal_default_lang;
			}
			else {
                $current_lang = $_COOKIE['portal_current_lang'];
            }
		}

        return $current_lang;
	}
}

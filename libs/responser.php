<?php

/**
 * @package    router
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Responser 
{
	private $data = array();
	
	function __construct($data) 
	{
		$this->data = $data;
		$this->run();
	}
	
	private function run()
	{
		if (isset($this->data['excepion']))
			switch ($this->data['excepion']->code)
			{
				case 'ER-00006': $this->sendResponse(501, $this->data['excepion']->message);
				break;
				default: $this->sendResponse(400, $this->data['excepion']->message);
				break;
			}
		else
		{
			try 
			{
				$filter_chain = new FilterChain($this->data);
				$this->data = $filter_chain->getData();
			}
			catch (QException $e)
			{
				// If there is an exception we will output it otherwise we shall return a normal body
				$this->data['excepion'] = new stdClass();
				$this->data['excepion']->message = $e;
				$this->data['excepion']->code = $e->getErrorCode();
				switch ($this->data['excepion']->code)
				{
						// Not authorised
					case 'ER-00010': 
					case 'ER-00011': 
					case 'ER-00012': 
					case 'ER-00013': $this->sendResponse(401, $this->data['excepion']->message);
						break;
						// Not found
					case 'ER-00014': 
					case 'ER-00015': 
					$this->sendResponse(404, $this->data['excepion']->message);
						break;
						// Not acceptable
					case 'ER-00002': 
					case 'ER-00003': 
					case 'ER-00004': 
					case 'ER-00005': 
					case 'ER-00016': 
					case 'ER-00017': 
					$this->sendResponse(406, $this->data['excepion']->message);
						break;
					default: $this->sendResponse(400, $this->data['excepion']->message);
						break;
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
				// Format the returned data to json, xml or other format
			$response_body_type = gettype($this->data['response']->body);
			switch ($this->data['request']->data_type)
			{
				case 'json':
					switch($response_body_type)
					{
						case 'array':
						case 'object':
							$this->data['response']->body = json_encode($this->data['response']->body);
						break;
						default:
							$this->data['response']->body = json_encode($this->data['response']->body);
						break;
					}
				break;
				default:
					switch($response_body_type)
					{
						case 'array':
						case 'object':
							$this->data['response']->body = json_encode($this->data['response']->body);
						break;
						default:
							$this->data['response']->body = $this->data['response']->body;
						break;
					}					
				break;
			}
			
			$this->sendResponse($this->data['response']->status, $this->data['response']->body);
		}
	}
	
	// Let's send the response
	public function sendResponse($status = 200, $body = '', $content_type = 'text/html')  
	{
		header('HTTP/1.1 '.$status.' '.$this->getStatusCodeMessage($status));  
		header('Content-type: '.$content_type);
	  
		// if body is not empty echo it or generate status message
		if(!empty($body))
		{
			echo $body;
			exit;
		}
		else
		{  
			// body messages that will be returned to the user
			$message = '';
			switch($status)
			{
				case 401:
					$message = 'You must be authorized to view this page.';
					break;
				case 404:
					$message = 'The requested URL '.$_SERVER['REQUEST_URI'].' was not found.';
					break;
				case 500:  
					$message = 'The server encountered an error processing your request.';
					break;
				case 501:
					$message = 'The requested method is not implemented.';
					break;
			}
	  
			// servers don't always have a signature turned on (this is an apache directive "ServerSignature On")  
			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];  
	  
			// this should be templatized in a real-world solution  
			$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">  
						<html>  
							<head>  
								<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">  
								<title>'.$status.' '.$this->getStatusCodeMessage($status).'</title>  
							</head>  
							<body>
								<h1>'.$this->getStatusCodeMessage($status).'</h1>
								<p>'.$message.'</p>
								<hr />
								<address>'.$signature.'</address>
							</body>
						</html>';  
	  
			//echo $body;
			exit;  
		}
	}		
	
	function getStatusCodeMessage($status)
	{
        $codes = Array(  
            100 => 'Continue',  
            101 => 'Switching Protocols',  
            200 => 'OK',  
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            300 => 'Multiple Choices',  
            301 => 'Moved Permanently',  
            302 => 'Found',  
            303 => 'See Other',  
            304 => 'Not Modified',  
            305 => 'Use Proxy',  
            306 => '(Unused)',  
            307 => 'Temporary Redirect',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',  
            409 => 'Conflict',  
            410 => 'Gone',  
            411 => 'Length Required',  
            412 => 'Precondition Failed',  
            413 => 'Request Entity Too Large',  
            414 => 'Request-URI Too Long',  
            415 => 'Unsupported Media Type',  
            416 => 'Requested Range Not Satisfiable',  
            417 => 'Expectation Failed',  
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported'  
        );  
  
        return (isset($codes[$status])) ? $codes[$status] : '';
	}
}

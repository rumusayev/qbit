<?php
/**
 * @package    translations
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Queries 
{
	
	private static $instance = null;
	private $xml_file;
	private $xml;

	private function __construct() 
	{	
		$this->xml_file = Backstage::gi()->CONFIG_DIR.'queries.xml';
		$this->xml = simplexml_load_file($this->xml_file);
	}
	
	/** SINGLETON PATTERN */
	public static function gi() 
	{	
		 if (is_null(self::$instance))
		 {
			self::$instance = new Queries();
		 }
		return self::$instance;	
	}
	
	/**
	 * Trasnalting array from xml
	 */
	public function getArray($arr)
	{
		$q_arr = array ();
		$i = 0;
		foreach ($arr as $key)
		{
				if (isset($this->xml->$key)) 
				{ 
					$q_arr[$i++] = (string)$this->xml->$key;
				}
				else
				{
					$q_arr[$i++] = $key;
				}	
		}
		return $q_arr;
	}
	
	public function __set($key, $value) 
	{	
		$this->xml->$key = $value;	
	}
	
	public function __get($key) 
	{	
		
		if (isset($this->xml->$key)) { return (string)$this->xml->$key; }	
	}
	
	public function getQuery($key, $message_parts)
	{
		$return_val = $this->xml->$key;
	    preg_match_all("/\[(\w+)\]/", $return_val, $matches, PREG_SET_ORDER);
  
 		foreach ($matches as $match)
 		{
 			$return_val = str_replace($match[0],$message_parts[$match[1]],$return_val);
 		}

		return (string)$return_val;
	}
        

}
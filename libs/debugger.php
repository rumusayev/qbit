<?php

/**
 * @package    debug
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Debugger
{
    protected static $instance = null;  // object instance
    private static $errors_xml = array();
    private static $warnings_xml = array();
    private static $notices_xml = array();
    private static $errors = array();	// ER-XXXXX
    private static $warnings = array();	// WR-XXXXX
    private static $notices = array();	// NC-XXXXX
    private static $notes = array();	// NT-XXXXX or customized
	
	private function __construct() 
	{	
		self::$errors_xml = simplexml_load_file(Backstage::gi()->CONFIG_DIR.'debugger/errors.xml');
		//self::$warnings_xml = simplexml_load_file(Backstage::gi()->CONFIG_DIR.'debugger/warnings.xml');
		//self::$notices_xml = simplexml_load_file(Backstage::gi()->CONFIG_DIR.'debugger/notices.xml');
	}
	
    private function __clone()     {  }
    private function __wakeup()    {  }

    public static function gi()
	{
        if (is_null(self::$instance)) 
		{
            self::$instance = new Debugger();
        }
        return self::$instance;
    }
	
	// Write error code and its source
	public function setError($code, $source)
	{
		self::$errors[$code] = $source;
	}

	// Get error description by its code
	public function getError($code, $message_parts)
	{
		if (!isset(self::$errors_xml->$code))
			return null;
		// Let's parse error message and replace it's [x]-es with the data 
		preg_match_all("/\[([0-9]+)\]/", self::$errors_xml->$code, $matches, PREG_SET_ORDER);
		foreach ($matches as $match)
		{
			self::$errors_xml->$code = str_replace($match[0],$message_parts[$match[1]],self::$errors_xml->$code);
		}

		return self::$errors_xml->$code;
	}
		
	public function setWarning($code, $source)
	{
		self::$warnings[$code] = $source;
	}

	public function getWarning($code, $message_parts)
	{
		if (!isset(self::$warnings_xml->$code))
			return null;
		// Let's parse error message and replace it's [x]-es with the data 
		preg_match_all("/\[([0-9]+)\]/", self::$warnings_xml->$code, $matches, PREG_SET_ORDER);
		foreach ($matches as $match)
		{
			self::$warnings_xml->$code = str_replace($match[0],$message_parts[$match[1]],self::$warnings_xml->$code);
		}

		return self::$warnings_xml->$code;
	}
		
	public function setNotice($code, $source)
	{
		self::$notices[$code] = $source;
	}

	public function getNotice($code, $message_parts)
	{
		if (!isset(self::$notices_xml->$code))
			return null;
		// Let's parse error message and replace it's [x]-es with the data 
		preg_match_all("/\[([0-9]+)\]/", self::$notices_xml->$code, $matches, PREG_SET_ORDER);
		foreach ($matches as $match)
		{
			self::$notices_xml->$code = str_replace($match[0],$message_parts[$match[1]],self::$notices_xml->$code);
		}

		return self::$notices_xml->$code;
	}
	
	// Set and get of developer's notes
	public function setNote($code, $note)
	{
		self::$notes[$code] = $note;
	}

	public function getNote($code)
	{
		if (!isset(self::$notes[$code])) 
			return null;
		return self::$notes[$code];
	}
	
	public function logFile($msg)
	{
		$fp = fopen("log.txt","a+");
		fwrite($fp, date("Y-m-d H:i:s").': '.$msg."\r\n");
		fclose($fp);		
	}
}

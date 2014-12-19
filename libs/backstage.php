<?php
/**
 * @package    backstage
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

final class Backstage extends Config
{
    protected static $instance = null;  // object instance
    private static $vars = array();
	
    private function __construct() { self::$vars = parent::getConfig();}
    private function __clone()     {  }
    private function __wakeup()    {  }
    public static function gi()
	{
        if (is_null(self::$instance)) 
		{
            self::$instance = new Backstage;
        }
        return self::$instance;
    }
	
	public function __set($key, $var) 
	{
        self::$vars[$key] = $var;
        return true;
	}
	
	public function &__get($key) 
	{
		if (!isset(self::$vars[$key])) 
			throw new QException(array('ER-00022', $key));
		return self::$vars[$key];
	}
	
	public static function remove($var) 
	{
		unset(self::$vars[$key]);
	}
}

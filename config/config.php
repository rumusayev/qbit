<?php

/**
 * @package    backstage
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Config
{	
    private function __construct() {  }
    private function __clone()     {  }
    private function __wakeup()    {  }
	
    public static function gi() 
    {
        if (is_null(self::$instance)) 
		{
            self::$instance = new Config;
        }
        return self::$instance;
    }
	
	final public static function getConfig()
	{
		return array (
                        // DB parameters
			"db_host" => "localhost",
			"db_name" => "qbit3",
			"db_user" => "root",
			"db_pass" => "1986519",
			"db_table_prefix" => "",
			
			"db_adapter" => "PDODB",
			"db_install" => false,
			"db_install_option" => "create,insert",				// recreate, create, alter, update, insert, delete
			
                        // Portal parameters
			"portal_email" => "admin@qbit.az",
			"portal_name" => "qbit",
			"portal_url" => "http://localhost/qbit/",
			"portal_langs" => "az,en,ru",
			"portal_default_lang" => "ru",
			"portal_time_correction" => 10,
			"portal_installed" => 1,
			"development_mode" => 0,
			
                        // Image parameters
			"image_max_width" => 800,
			"image_max_height" => 400,
			"image_thumb_max_width" => 400,
			"image_thumb_max_height" => 200,
                        
                        // Core parameters
			"template_name" => "master",
			"default_module_name" => "pages",						// which module will be called by default when the portal is opened
			"default_action_name" => "getPage",
			"default_view_name" => "default",
			"default_data_type" => "text",							// default type of the returned data (json, xml or text) 
			"default_method" => "GET",								// default request method (PUT, POST, GET, DELETE - CRUD) 
			
			"controller_prefix" => "c",
			"model_prefix" => "m",
			
			"process_keep_time" => 30,
                    
                        // Navigator parameters
			"count_per_page" => 20,

            // QBIT Update
            "update_server_address" => 'http://localhost/qbit/'
		);
	}
}
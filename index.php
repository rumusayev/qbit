<?php

/**
 * @package    front
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

error_reporting(E_ALL);
session_start();
ob_start();
header('Pragma: no-cache');
header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Content-Type: text/html');

if (version_compare(phpversion(), '5.1.0', '<') == true) {
    die ('Please use PHP 5.1 or higher.');
}

// Root constants and directories
define('DR', '/');
define('ROOT_DIR', realpath(dirname(__FILE__)) . DR);
define('LIBS_DIR', ROOT_DIR . 'libs' . DR);
define('CONFIG_DIR', ROOT_DIR . 'config' . DR);
define('LANGS_DIR', CONFIG_DIR . 'languages' . DR);
define('MODULES_DIR', ROOT_DIR . 'modules' . DR);
define('MATERIALS_DIR', ROOT_DIR . 'materials' . DR);
define('TEMPLATES_DIR', ROOT_DIR . 'templates' . DR);
define('EXTERNAL_DIR', ROOT_DIR . 'external' . DR);
define('CGI_DIR', ROOT_DIR . 'cgi' . DR);


require_once EXTERNAL_DIR."apache-log4php-2.3.0/src/main/php/Logger.php";
Logger::configure(CONFIG_DIR."log_config.xml");
    	

// Autoloading classes
function q_autoload($class_name) 
{
	$filename = strtolower($class_name).'.php';
	$libs_file = LIBS_DIR.$filename;
	$config_file = CONFIG_DIR.$filename;
	if (!file_exists($libs_file))
		if (!file_exists($config_file))
			return false;
		else
			include ($config_file);
	else
		include ($libs_file);
}

spl_autoload_register('q_autoload');

if (Backstage::gi()->portal_installed == 0) {
    header("Location: install");
} else {

// Generating core
    new Core();
}
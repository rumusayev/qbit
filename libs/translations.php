<?php
/**
 * @package    translations
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Translations 
{
	
	private static $instance = null;
	private $xml_file;
	private $xml;

	private function __construct() 
	{	
		$this->xml_file = Backstage::gi()->LANGS_DIR.Backstage::gi()->portal_current_lang.'.xml';
		$this->xml = simplexml_load_file($this->xml_file);
	}
	
	/** SINGLETON PATTERN */
	public static function gi() 
	{	
		if (is_null(self::$instance))
		{
			self::$instance = new Translations();
		}
		return self::$instance;	
	}
	
	public function __set($key, $value) 
	{	
		$this->xml->$key = $value;	
	}
	
	public function __get($key) 
	{	
		if (isset($this->xml->$key)) { return (string)$this->xml->$key; }	
	}
        
	public function getFields($table_name, $module_name = '')
	{
		$data['module_name'] = 'common';
		$data['model_name'] = 'translations';
		$data['action_name'] = 'getFields';
		$data['translations_table_name'] = $table_name;
		$data['translations_module_name'] = $module_name;
		$data = Loader::gi()->getModel($data);
		return $data['fields'];
	}        
        
	public function getTranslations($table_name, $row_id, $language = '', $module_name = '')
	{
		$data['module_name'] = 'common';
		$data['model_name'] = 'translations';
		$data['action_name'] = 'getTranslations';
		$data['translations_table_name'] = $table_name;
		$data['translations_row_id'] = $row_id;
		$data['translations_language'] = $language;
		$data['translations_module_name'] = $module_name;
		$data = Loader::gi()->getModel($data);
		if (!isset($data['translations']))
			return false;
		return $data['translations'];
	}     
        
	public function setTranslations($translations, $table_name, $row_id, $module_name = '')
	{
		$data['module_name'] = 'common';
		$data['model_name'] = 'translations';
		$data['action_name'] = 'setTranslations';
		$data['translations_translations'] = $translations;
		$data['translations_table_name'] = $table_name;
		$data['translations_row_id'] = $row_id;
		$data['translations_module_name'] = $module_name;
		$data = Loader::gi()->getModel($data);
		return $data['status'];
	}
}
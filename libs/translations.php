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
	private $words;

	private function __construct() 
	{

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
        $data['module_name'] = 'common';
        $data['model_name'] = 'translations';
        $data['action_name'] = 'getStaticTranslations';
        $data['language'] =  Backstage::gi()->portal_current_lang;
        $data = Loader::gi()->getModel($data);

        $translations = array();
        foreach ($data['items'] as $value){

            $translations[$value->w_key] = $value->w_value;
        }
        $translations = (object)$translations;
        $this->words = $translations;

		$this->words->$key = $value;
	}
	
	public function __get($key) 
	{
        $data['module_name'] = 'common';
        $data['model_name'] = 'translations';
        $data['action_name'] = 'getStaticTranslations';
        $data['language'] =  Backstage::gi()->portal_current_lang;
        $data = Loader::gi()->getModel($data);

        $translations = array();
        foreach ($data['items'] as $value){

            $translations[$value->w_key] = $value->w_value;
        }
        $translations = (object)$translations;
        $this->words = $translations;

		if (isset($this->words->$key)) { return (string)$this->words->$key; }
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
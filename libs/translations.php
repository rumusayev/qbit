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
        $data['module_name'] = 'common';
        $data['model_name'] = 'translations';
        $data['action_name'] = 'getStaticTranslations';
        $data = Loader::gi()->getModel($data);
        $items = array();

        foreach ($data['items'] as $key => $value) {
            $items[$value->field_name] = $value->word;
        }

        $words = $items;
        $this->words = $words;
    }

    /** SINGLETON PATTERN */
    public static function gi()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Translations();
        }
        return self::$instance;
    }

    public function __get($key)
    {
        $words = $this->words;
        if (isset($words[$key])) {
            return (string)$words[$key];
        }
    }
    
	/**
	 * Trasnalting array
	 */
	public function translateArray($arr)
	{
		$translated_arr = array();
		$i = 0;
		foreach ($arr as $key=>$val)
		{
				if (isset($this->words->$val)) 
				{ 
					$translated_arr[$key] = (string)$this->words->$val;
				}
				else
				{
					$translated_arr[$key] = $val;
				}	
		}
		return $translated_arr;
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
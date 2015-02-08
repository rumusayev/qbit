<?php

/**
 * @package    translations
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mTranslations extends model
{
    function getFields()
    {
        $where = 'table_name = "' . $this->data['translations_table_name'] . '"';
        if ($this->data['translations_module_name'] != '')
            $where .= 'AND module_name = "' . $this->data['translations_module_name'] . '"';
        $this->data['fields'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . 'translation_modules')
            ->fields('field')
            ->where($where)
            ->select();
        return $this->data;
    }

    function getTranslations()
    {
        $fields = Translations::gi()->getFields($this->data['translations_table_name']);
        $where = '';
		$lang_fields = Backstage::gi()->portal_langs;
		$lang_fields_arr = explode(',', $lang_fields);
			// Select only one language if needed
        if ($this->data['translations_language'] !== '')
			$lang_fields = strtolower($this->data['translations_language']);
		//$fields_str = implode(', ', array_map(function($f) {return ''''.$f->field.'''';}, $fields));

        foreach ($fields as $field) 
		{
            $translations = $this->dbmanager->tables(Backstage::gi()->db_table_prefix."translations tr")
                ->fields($lang_fields)
                ->where("tr.table_name = '{$this->data['translations_table_name']}' and tr.field_name = '{$field->field}' and tr.row_id = {$this->data['translations_row_id']}")
                ->getScalar();
            if ($this->data['translations_language'] !== '')
				$this->data['translations'][$field->field]->translation = $translations->{$this->data['translations_language']};
			else
				foreach ($lang_fields_arr as $lang_key => $lang_field)
				{
					$this->data['translations'][$field->field][$lang_key]->short = $lang_field;
					$this->data['translations'][$field->field][$lang_key]->translation = $translations->{$lang_field};
				}
        }
        return $this->data;
    }


    function setTranslations()
    {
        foreach ($this->data['translations_translations'] as $field => $values) 
		{			
			$cnt = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . "translations")
				->where("table_name = '" . $this->data['translations_table_name'] . "' and row_id = " . $this->data['translations_row_id'] . " and field_name = '$field'")
				->count();
			if ($cnt > 0) 
			{
				$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . "translations")
					->values($values)
					->where("table_name = '" . $this->data['translations_table_name'] . "' and row_id = " . $this->data['translations_row_id'] . " and field_name = '$field'")
					->update();
			} 
			else
				$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . "translations")
					->values(array_merge($values, array('id' => '', 'language_id' => $language_id, 'table_name' => $this->data['translations_table_name'], 'row_id' => $this->data['translations_row_id'], 'field_name' => $field)))
					->insert();
        }
        return $this->data;
    }

    public function getStaticTranslations()
    {
        $where = '';

        $this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . 'translations')
            ->fields('field_name', Backstage::gi()->portal_current_lang . ' as word')
            ->where($where)
            ->select();

        return $this->data;
    }

}
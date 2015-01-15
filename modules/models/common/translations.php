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
        if ($this->data['translations_language'] !== '')
            $where = 'ln.short = "' . strtolower($this->data['translations_language']) . '"';
        foreach ($fields as $field) 
		{
            $this->data['translations'][$field->field] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . "languages ln left join " . Backstage::gi()->db_table_prefix . "translations tr on tr.language_id = ln.id and tr.table_name = '" . $this->data['translations_table_name'] . "' and tr.field_name = '" . $field->field . "' and tr.row_id = " . $this->data['translations_row_id'])
                ->fields('tr.translation, ln.id language_id, ln.short')
                ->where($where)
                ->select();
            if ($this->data['translations_language'] !== '')
                $this->data['translations'][$field->field] = $this->data['translations'][$field->field][0];
        }
        return $this->data;
    }


    function setTranslations()
    {
        foreach ($this->data['translations_translations'] as $field => $values) 
		{
            foreach ($values as $language_id => $value) 
			{
                $cnt = $this->dbmanager->tables(Backstage::gi()->db_table_prefix."translations")
                    ->where("language_id = $language_id and table_name = '" . $this->data['translations_table_name'] . "' and row_id = " . $this->data['translations_row_id'] . " and field_name = '$field'")
                    ->count();
                if ($cnt > 0)
				{
                    $this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix."translations")
                        ->values(array('translation' => $value))
                        ->where("language_id = $language_id and table_name = '" . $this->data['translations_table_name'] . "' and row_id = " . $this->data['translations_row_id'] . " and field_name = '$field'")
                        ->update();
				}
                else
                    $this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix."translations")
                        ->values(array('id' => '', 'language_id' => $language_id, 'table_name' => $this->data['translations_table_name'], 'row_id' => $this->data['translations_row_id'], 'field_name' => $field, 'translation' => $value))
                        ->insert();
            }
        }
        return $this->data;
    }

    public function getStaticTranslations()
    {
        $where = '';

        $this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'translations_words')
            ->fields('*')
            ->where($where)
            ->select();

        foreach ($this->data['items'] as $key=>$item)
        {
            $translations = Translations::gi()->getTranslations('translations_words', $item->id, Backstage::gi()->portal_current_lang);
            if (!empty($translations))
            {
                foreach ($translations as $field => $translation)
                    $this->data['items'][$key]->$field = $translation->translation;
            }
        }

        return $this->data;
    }

}
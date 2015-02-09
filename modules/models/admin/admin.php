<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mAdmin extends model
{
    public function getLayout()
    {
        if (isset($this->data['request']->parameters['id']))
            $this->data['where'] = "l.id = '" . $this->data['request']->parameters['id'] . "'";


        $tables = Backstage::gi()->db_table_prefix . 'layouts l LEFT JOIN ' . Backstage::gi()->db_table_prefix . 'designs d ON l.design_id=d.id';


        $conditions = $this->data['where'];
        $this->data['items'] = $this->dbmanager->tables($tables)
            ->fields('l.*, d.design_name')
            ->where($conditions)
            ->select();

        return $this->data;
    }

    public function siteConfigs(){

        $table_languages = Backstage::gi()->db_table_prefix . 'languages';
        $table_translations = Backstage::gi()->db_table_prefix . 'translations';

        if (isset($this->data['request']->parameters['portal_all_langs'])) {

            $query = "TRUNCATE table " . $table_languages;
            $this->data['languages'] = $this->dbmanager->selectByQuery($query);

            $portal_langs = explode(',', $this->data['portal_all_langs']);
            $portal_langs_abbr = explode(',', $this->data['request']->parameters['portal_langs_abbr']);

            $i = 0;
            foreach ($portal_langs as $lang){
                $language['id'] = 0;
                $language['short'] = $lang;
                $language['language'] = $portal_langs_abbr[$i++];
                $langs = (object) $language;

                $checkColumnExists =$this->dbmanager->selectByQuery("SHOW columns FROM " . $table_translations . " WHERE field='" . $lang . "';");
                if (empty($checkColumnExists)){

                    $this->dbmanager->selectByQuery("ALTER TABLE " . $table_translations . " ADD " . $lang . " TEXT;");
                } else {
                }

                $this->data['items'] = $this->dbmanager->tables($table_languages)
                    ->values($langs)
                    ->insert();
            }

        } else {
            $this->data['languages'] = $this->dbmanager->tables($table_languages)
                ->fields('short, language')
                ->select();
        }

        return $this->data;
    }

}
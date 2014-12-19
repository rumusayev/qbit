<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mLayouts extends model
{

    public function get(){
        $this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . 'layouts')
            ->fields('id, layout_name')
            ->select();

        return $this->data;
    }

    public function getLayoutsList()
    {
        $this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix . 'layouts')
            ->fields('*')
            ->select();
        return $this->data;
    }

    public function addLayout()
    {
        $tables = array(Backstage::gi()->db_table_prefix . 'layouts');

        $this->data['items'] = $this->dbmanager->insert($tables, $this->data['request']->parameters);

        return $this->data;
    }

    public function editLayout()
    {
        $tables = array(Backstage::gi()->db_table_prefix . 'layouts');

        $this->data['items'] = $this->dbmanager->update($tables, $this->data['request']->parameters);

        return $this->data;
    }

    public function saveLayout()
    {
        $table = Backstage::gi()->db_table_prefix . 'layouts';

        if (empty($this->data['request']->parameters['id'])) {

            $this->data['items'] = $this->dbmanager->tables($table)
                ->values($this->data['request']->parameters)
                ->insert();

        } else {
            $this->data['items'] = $this->dbmanager->tables($table)
                ->values($this->data['request']->parameters)
                ->update();
        }

        return $this->data;
    }

    public function deleteLayout()
    {
        $table = Backstage::gi()->db_table_prefix . 'layouts';

        $this->data['items'] = $this->dbmanager->tables($table)
            ->where("id=".$this->data['request']->parameters['id'])
            ->delete();

        return $this->data;
    }


}
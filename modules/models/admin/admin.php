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


}
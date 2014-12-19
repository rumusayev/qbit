<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mAuth extends model
{
    public function login()
    {
        $table = Backstage::gi()->db_table_prefix.'users a';

		$this->data['item'] = $this->dbmanager->tables($table)
											->fields('*')
											->where('lower(login) = "'.strtolower($this->data['request']->parameters['login']).'" and password = "'.md5($this->data['request']->parameters['password']).'"')
											->getScalar();
        return $this->data;
    }
}
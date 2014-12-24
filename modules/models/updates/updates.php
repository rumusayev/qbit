<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mUpdates extends model
{
    public function form()
    {
        $this->dbmanager->query($this->data['sqlQuery']);

        return $this->data;
    }
}
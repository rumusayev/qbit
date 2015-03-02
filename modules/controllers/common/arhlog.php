<?php

/**
 * @package    Arhlog
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 class cArhlog extends controller
{
    function rollback()
    {
        $this->data['resource_name'] = $this->data['request']->parameters['resource_name'];
        $this->data['condition_part'] = isset($this->data['request']->parameters['condition_part'])?$this->data['request']->parameters['condition_part']:null;
		
		// Model loading
        $this->data = Loader::gi()->getModel($this->data);		
        
        if (!isset($this->data['error']))
        {
            if ($this->data['routing'] === 'ajax')
                echo 'Əməliyyat uğurla başa çatdı.';
        }
        else
            if ($this->data['routing'] === 'ajax')
                echo $this->data['error'];     

        return $this->data;
    }
}
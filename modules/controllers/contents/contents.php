<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cContents extends controller
{

	/**
	 * Used to load catalog to the viewport (client)
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
    public function getContent()
    {
		$this->data = Loader::gi()->getModel($this->data);
		$this->data['body'] = $this->data['item']->content;
        return $this->data;
	}
}
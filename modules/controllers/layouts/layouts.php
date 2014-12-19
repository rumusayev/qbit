<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cLayouts extends controller
{
	public function get()
	{
			// Page name
        /*if (isset($this->data['request']->parameters['p']))
            $this->data['where'] = "page_name = '".$this->data['request']->parameters['p']."'";*/

        $this->data = Loader::gi()->getModel($this->data);
        if ($this->data['request']->data_type === 'json')
            $this->data['items'] = json_encode($this->data['items']);

		//$this->data = parent::get();
		$this->data['body'] = $this->data['items'];
		return $this->data;
	}

    public function addLayout()
    {
        // View loading
        $this->data['view_name'] = 'addLayout';

        $this->data = Loader::gi()->getModel($this->data);

        $this->data['body'] = Loader::gi()->getView($this->data);

        return $this->data;
    }

    public function editLayout()
    {
        // View loading
        $this->data['view_name'] = 'editLayout';

        $this->data = Loader::gi()->getModel($this->data);

        $this->data['body'] = Loader::gi()->getView($this->data);

        return $this->data;
    }

    public function saveLayout(){

        // View loading
        $this->data['view_name'] = 'editLayout';

        $this->data = Loader::gi()->getModel($this->data);

        echo (isset($this->data['items'])) ? $this->data['items'] : '';

        $this->data['body'] = Loader::gi()->getView($this->data);


        return $this->data;
    }

    public function deleteLayout(){

        // View loading
        $this->data['view_name'] = 'editLayout';

        $this->data = Loader::gi()->getModel($this->data);

        echo (isset($this->data['items'])) ? $this->data['items'] : '';

        $this->data['body'] = Loader::gi()->getView($this->data);


        return $this->data;
    }
	
}
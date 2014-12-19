<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cDesigns extends controller
{
    public function getDesign()
    {
        // View loading
        $this->data['view_name'] = 'design';
        $out = Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'design');
        $this->data['body'] = $out;
        //$this->data['body'] = Loader::gi()->getView($this->data);

        return $this->data;
    }

    public function addDesign()
    {
        // View loading
        $this->data['view_name'] = 'designs';
        $out = Loader::gi()->callAPI('POST', Backstage::gi()->portal_url.'design');
        $this->data['body'] = $out;
        $this->data['body'] = Loader::gi()->getView($this->data);

        return $this->data;
    }
	
}
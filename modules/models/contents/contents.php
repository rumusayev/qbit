<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mContents extends model
{
    public function getContent()
    {
		if (!isset($this->data['request']->parameters['lq']['name']))
			throw new QException(array('ER-00023', 'name', $this->data['request']->parameters['lq']['lq']));
		
        $this->data['item'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'contents')
            ->fields('*')
            ->where('content_name="'.$this->data['request']->parameters['lq']['name'].'"')
            ->getScalar();
		
		if (!$this->data['item'])
			throw new QException(array('ER-00025', 'content_name="'.$this->data['request']->parameters['lq']['name'], ''));
		
		$translations = Translations::gi()->getTranslations('contents', $this->data['item']->id, Backstage::gi()->portal_current_lang);

		if (!empty($translations)) 
		{
			foreach ($translations as $field => $translation)
				$this->data['item']->$field = $translation->translation;
		}			

        return $this->data;
    }
}
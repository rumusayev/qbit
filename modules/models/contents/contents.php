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
		if (!isset($this->data['request']->parameters['lq']['name']) && !isset($this->data['request']->parameters['name']))
		{
			if (isset($this->data['request']->parameters['lq']['lq']))
				throw new QException(array('ER-00023', 'name', $this->data['request']->parameters['lq']['lq']));
			else
				throw new QException(array('ER-00023', 'name', ''));
		}
		$content_name = isset($this->data['request']->parameters['lq']['name'])?$this->data['request']->parameters['lq']['name']:$this->data['request']->parameters['name'];
		
        $this->data['item'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'contents')
            ->fields('*')
            ->where('content_name="'.$content_name.'"')
            ->getScalar();
		
		if (!$this->data['item'])
			throw new QException(array('ER-00025', 'content_name="'.$content_name, ''));
		
		$translations = Translations::gi()->getTranslations('contents', $this->data['item']->id, Backstage::gi()->portal_current_lang);

		if (!empty($translations)) 
		{
			foreach ($translations as $field => $translation)
				$this->data['item']->$field = $translation->translation;
		}			

        return $this->data;
    }
}
<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mPages extends model
{
    public function getMenu()
    {
        $table = Backstage::gi()->db_table_prefix.'pages a';
		
        if (isset($this->data['request']->parameters['parent']))
        {
            $parent_id = $this->dbmanager->tables($table)
										->fields('id')
										->where('page_name = "'.$this->data['request']->parameters['parent'].'"')
										->getScalar();
            $this->data['request']->parameters['parent_id'] = $parent_id->id;
        }
		
		$group = '';
		if (isset($this->data['request']->parameters['lq']['group']))
			$group = $this->data['request']->parameters['lq']['group'];

		if (isset($this->data['request']->parameters['lq']['parent_id']))
			$this->data['request']->parameters['parent_id'] = $this->data['request']->parameters['lq']['parent_id'];

		if (!isset($this->data['request']->parameters['parent_id']))
		{
			// Get the lowest parent_id which have the corresponding group_name
            $parent_id = $this->dbmanager->tables($table)
										->fields('parent_id')
										->where('find_in_set("'.$group.'", page_menu_group) <> 0')
										->order('parent_id')
										->getScalar();
			$this->data['request']->parameters['parent_id'] = $parent_id->parent_id;
		}

		$this->data['items'] = $this->dbmanager->tables($table)
											->fields('a.*, (select count(*) from pages where parent_id = a.id) cnt')
											->where('is_visible = 1 and find_in_set("'.$group.'", page_menu_group) <> 0 and parent_id='.$this->data['request']->parameters['parent_id'])
											->order('ordering')
											->select();
        foreach ($this->data['items'] as $key=>$item)
        {
			$translations = Translations::gi()->getTranslations('pages', $item->id, Backstage::gi()->portal_current_lang);
			if (!empty($translations))
			{
				foreach ($translations as $field => $translation)
					$this->data['items'][$key]->$field = $translation->translation;
			}				
        }
        return $this->data;		
	}

    public function getPage()
    {
		$where = isset($this->data['request']->parameters['p'])?' AND b.page_name="'.$this->data['request']->parameters['p'].'"':' AND b.is_main = 1';
		
        $this->data['item'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'layouts a', Backstage::gi()->db_table_prefix.'pages b')
            ->fields('a.layout_content, b.page_content, a.design_id, b.id, b.page_title, b.page_name, b.page_meta_title, b.page_meta_keywords, b.page_meta_description, b.page_menu_group')
            ->where('a.id = b.layout_id'.$where)
            ->getScalar();
		
		if (!$this->data['item'])
			throw new QException(array('ER-00025', $where, ''));
			
		$translations = Translations::gi()->getTranslations('pages', $this->data['item']->id, Backstage::gi()->portal_current_lang);

		if (!empty($translations)) 
		{
			foreach ($translations as $field => $translation)
				$this->data['item']->$field = $translation->translation;
		}			

        return $this->data;
    }
	
	public function getLangBar()
	{
		$this->data['items'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'languages')
											->fields('*')
											->order('id')
											->select();
        return $this->data;	
	}
	
    public function post()
    {
		$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'pages')
															->values($this->data['page_data'])
															->insert();
		$this->data['page_data']['id'] = $this->dbmanager->getLastID();
                
		if (isset($this->data['page_data']['translations']))
			Translations::gi()->setTranslations($this->data['page_data']['translations'], 'pages', $this->data['page_data']['id']);
		
		return $this->data;
    }
	
    public function addLQContent()
    {
		if (isset($this->data['values']['translations'])) 
		{
			$translations = $this->data['values']['translations'];
			unset($this->data['values']['translations']);
		}
		
		$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'pages')
			->values($this->data['values'])
			->update();			
			
		$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'contents')
			->values($this->data['content_values'])
			->insert();	
			
			// Insert grants for the content
		$grant_values['resource_type'] = 'contents';
		$grant_values['resource_id'] = $this->dbmanager->getLastID();
		$grant_values['resource_name'] = '-';
		$grant_values['grant_type'] = 'GET';
		$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
			->values($grant_values)
			->insert();
		$last_grant_ids['GET'] = $this->dbmanager->getLastID();
		$grant_values['grant_type'] = 'PUT';
		$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
			->values($grant_values)
			->insert();
		$last_grant_ids['PUT'] = $this->dbmanager->getLastID();
		$grant_values['grant_type'] = 'POST';
		$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
			->values($grant_values)
			->insert();
		$last_grant_ids['POST'] = $this->dbmanager->getLastID();								
		$grant_values['grant_type'] = 'DELETE';
		$this->data['status'] = $this->dbmanager->tables('`'.Backstage::gi()->db_table_prefix.'grants`')
			->values($grant_values)
			->insert();
								
		if (isset($translations)) 
		{
			Translations::gi()->setTranslations($translations, 'pages', $this->data['values']['id']);
			unset($translations);
		}
		return $this->data;
	}	
}
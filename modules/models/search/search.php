<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mSearch extends model
{
	public function getSearchResults()		
	{
		$search_word = $this->data['request']->parameters['s'];
		
		$this->data['items'] = $this->dbmanager->selectByQuery('select r.*, m.action, m.design, m.material_design, m.container_id, m.container_type, l.short from
			(select a.is_searchable, a.id, case when a.parent_id > 0 then (select catalog_name from '.Backstage::gi()->db_table_prefix.'catalogs where id = a.parent_id) else a.catalog_name end resource_name, a.design_id, 
			case a.catalog_title when "" then b.translation else a.catalog_title end resource_value, 
			b.table_name resource_type,
			b.field_name resource_field,
			b.language_id
			from '.Backstage::gi()->db_table_prefix.'catalogs a left join '.Backstage::gi()->db_table_prefix.'translations b on a.id = b.row_id and b.table_name = "catalogs" and b.field_name = "catalog_title"
			where lower(a.catalog_title) like lower("%'.$search_word.'%") or lower(b.translation) like lower("%'.$search_word.'%")
			union all
			select a.is_searchable, a.id, case when a.parent_id > 0 then (select catalog_name from '.Backstage::gi()->db_table_prefix.'catalogs where id = a.parent_id) else a.catalog_name end resource_name, a.design_id,
			case a.catalog_content when "" then b.translation else a.catalog_content end resource_value, 
			b.table_name resource_type,
			b.field_name resource_field,
			b.language_id
			from '.Backstage::gi()->db_table_prefix.'catalogs a left join '.Backstage::gi()->db_table_prefix.'translations b on a.id = b.row_id and b.table_name = "catalogs" and b.field_name = "catalog_content"
			where lower(a.catalog_content) like lower("%'.$search_word.'%") or lower(b.translation) like lower("%'.$search_word.'%")
			union all
			select a.is_searchable, a.id, a.content_name resource_name, a.design_id, 
			case a.content when "" then b.translation else a.content end resource_value, 
			b.table_name resource_type,
			b.field_name resource_field,
			b.language_id
			from '.Backstage::gi()->db_table_prefix.'contents a left join '.Backstage::gi()->db_table_prefix.'translations b on a.id = b.row_id and b.table_name = "contents" and b.field_name = "content"
			where lower(a.content) like lower("%'.$search_word.'%") or lower(b.translation) like lower("%'.$search_word.'%")) r left join '.Backstage::gi()->db_table_prefix.'maps m
			on r.resource_type = m.resource_type and r.resource_name = m.resource_name, '.Backstage::gi()->db_table_prefix.'languages l where r.is_searchable = 1 and r.language_id = l.id and (m.action="getCatalog" or m.action = "getContent")');
		
		$new_items = array(); 
		foreach ($this->data['items'] as $item)
		{
			$new_items[$item->id][$item->short] = $item;
		}
		$this->data['items'] = $new_items;
        return $this->data;		
	}

    public function saveMap()
    {
		foreach ($this->data['items'] as $item)
		{
			$where = 'container_type = "'.$item['container_type'].'" 
				and container_id = "'.$item['container_id'].'" 
				and resource_type = "'.$item['resource_type'].'" 
				and resource_name = "'.$item['resource_name'].'" 
				and resource_id = "'.$item['resource_id'].'" 
				and action = "'.$item['action'].'" 
				and design = "'.$item['design'].'" 
				and material_design = "'.$item['material_design'].'"';
			$count = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'maps')
				->where($where)
				->count();
			if ($count == 0)
			{
				$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'maps')
					->values($item)
					->insert();					
			}			
		}
        return $this->data;
    }


}
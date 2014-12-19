<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mCatalogs extends model
{
    public function getCatalog()
    {
        $table = Backstage::gi()->db_table_prefix . 'catalogs a';
        $where = isset($this->data['request']->parameters['parent_id']) ? 'parent_id = ' . $this->data['request']->parameters['parent_id'] : 'parent_id = (select id from ' . $table . ' where ' . (isset($this->data['request']->parameters['id']) ? 'id="' . $this->data['request']->parameters['id'] : 'catalog_name="' . $this->data['request']->parameters['lq']['name']) . '")';

        if (isset($this->data['request']->parameters['lq']['navigation'])) {
            if (isset($this->data['request']->parameters['pnum'])) {
                $start = $this->data['request']->parameters['pnum']*$this->data['request']->parameters['lq']['navigation']-$this->data['request']->parameters['lq']['navigation'];
            } else {
                $start = 0;
            }
            $limit = ' LIMIT ' . ($start) . ',' . $this->data['request']->parameters['lq']['navigation'];
        } else {
            $limit='';
        }

        // Check usage of ID parameter in LQ. If exist - show it, else get parameter from URL. ( If both not exists - 404 page must be created )
        if (isset($this->data['request']->parameters['lq']['id'])) {
            if (!empty($where))
                $where .= " AND";
            $where .= " id IN (" . $this->data['request']->parameters['lq']['id'] . ")";
        }

        if (isset($this->data['request']->parameters['lq']['order'])) {
            $order = str_replace('|', ' ', $this->data['request']->parameters['lq']['order']);
        } else {
            $order = " insert_date ASC";
        }

        $this->data['items'] = $this->dbmanager->tables($table)
            ->fields('a.*, (select count(*) from ' . Backstage::gi()->db_table_prefix . 'catalogs' . ' where parent_id = a.id) cnt, (select count(*) from catalogs WHERE '.$where.') cnt_parent')
            ->where('is_visible = 1 and ' . $where)
            ->order($order . $limit)
            ->select();

        foreach ($this->data['items'] as $key => $item) {
            $translations = Translations::gi()->getTranslations('catalogs', $item->id, Backstage::gi()->portal_current_lang);
            if (!empty($translations)) {
                foreach ($translations as $field => $translation)
                    $this->data['items'][$key]->$field = $translation->translation;
            }
        }
        return $this->data;
    }

    public function getCatalogItem()
    {
        $table = Backstage::gi()->db_table_prefix . 'catalogs a';

        $id = isset($this->data['request']->parameters['lq']['id']) ? ' LIMIT 0,' . $this->data['request']->parameters['lq']['id'] : '';

        // Check usage of ID parameter in LQ. If exist - show it, else get parameter from URL. ( If both not exists - 404 page must be created )
        if (isset($this->data['request']->parameters['lq']['id']) && !isset($this->data['request']->parameters['id'])) {
            $id = $this->data['request']->parameters['lq']['id'];
        } else {
            $id = $this->data['request']->parameters['id'];
        }

        $this->data['item'] = $this->dbmanager->tables($table)
            ->fields('a.*, (select count(*) from ' . $table . ' where parent_id = a.id) cnt')
            ->where('is_visible = 1 and id = ' . $id)
            ->getScalar();

        $translations = Translations::gi()->getTranslations('catalogs', $this->data['item']->id, Backstage::gi()->portal_current_lang);
        if (!empty($translations)) {
            foreach ($translations as $field => $translation)
                $this->data['item']->$field = $translation->translation;
        }
        return $this->data;
    }
}
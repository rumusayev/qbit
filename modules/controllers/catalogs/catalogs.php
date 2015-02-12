<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cCatalogs extends controller
{
    /**
     * Used to load catalog to the viewport (client)
     * Please use $this->data['request']->parameters['parameter_name'] to get parameters
     *
     * @return array global $this->data
     */
    public function getCatalog()
    {
        $this->data = Loader::gi()->getModel($this->data);
        // Get design
        $design_name = isset($this->data['request']->parameters['lq']['design']) ? $this->data['request']->parameters['lq']['design'] : '';
        $des_data = Loader::gi()->callModule('GET', 'designs', array('where' => 'design_name="' . $design_name . '"'));
        if ($des_data['items'])
            $design = $des_data['items'][0];
        else
            throw new QException(array('ER-00026', $design_name));

        $block_rules_cnt = preg_match_all("/.*(\[\[([A-z0-9\-\_]+)\]\]).*/", $design->block, $block_rules, PREG_SET_ORDER);
        $structure_rules_cnt = preg_match_all("/(\[\[([A-z0-9\-\_]+)\]\])/", $design->structure, $structure_rules, PREG_SET_ORDER);

        // Get material design if there is any
        $material_design_name = isset($this->data['request']->parameters['lq']['material_design']) ? $this->data['request']->parameters['lq']['material_design'] : '';
        $material_des_data = Loader::gi()->callModule('GET', 'designs', array('where' => 'design_name="' . $material_design_name . '"'));
        if ($material_des_data['items']) {
            $material_design = $material_des_data['items'][0];
            $material_block_rules_cnt = preg_match_all("/.*(\[\[([A-z0-9\-\_]+)\]\]).*/", $material_design->block, $material_design->block_rules, PREG_SET_ORDER);
            $material_structure_rules_cnt = preg_match_all("/(\[\[([A-z0-9\-\_]+)\]\])/", $material_design->structure, $material_design->structure_rules, PREG_SET_ORDER);
        }

        // Get navigation design if there is any
        $navigation_design_name = isset($this->data['request']->parameters['lq']['navigation_design']) ? $this->data['request']->parameters['lq']['navigation_design'] : '';
        $navigation_design_data = Loader::gi()->callModule('GET', 'designs', array('where' => 'design_name="' . $navigation_design_name . '"'));
        if ($navigation_design_data['items']) {
            $nav_items = array();
            $navigation = '';
            $navigation_design = $navigation_design_data['items'][0];
            $navigation_block_rules_cnt = preg_match_all("/.*(\[\[([A-z0-9\-\_]+)\]\]).*/", $navigation_design->block, $navigation_design->block_rules, PREG_SET_ORDER);
            $navigation_structure_rules_cnt = preg_match_all("/(\[\[([A-z0-9\-\_]+)\]\])/", $navigation_design->structure, $navigation_design->structure_rules, PREG_SET_ORDER);

            $navigation_structure_rules_cnt = @$this->data['items'][0]->cnt_parent / $this->data['request']->parameters['lq']['navigation'];
            for ($i = 1; $i <= ceil($navigation_structure_rules_cnt); $i++) {
                $nav_items[] = str_replace('[[pnum]]', $i, $navigation_design->structure);
            }
            foreach ($nav_items as $key => $nav_item) {
                $navigation = $navigation . $nav_item;
            }
            $navigation = str_replace('[[structure]]', $navigation, $navigation_design->block);
        }

        $design->block = str_replace('[[navigation]]', @$navigation, $design->block);

        $fields = array('id', 'catalog_name', 'catalog_title', 'catalog_content', 'insert_date', 'is_category', 'form_id', 'parent_name');
        $this->data['sequence'] = '';

        foreach ($this->data['items'] as $key => $cur_item) {

            // Get navigation design if there is any
            if ($navigation_design_data['items']) {
                $design->block = str_replace('[[parent_name]]', $cur_item->parent_name, $design->block);
            }
            // Cut
            $cut = explode('[[cut]]', $cur_item->catalog_content);
            $this->data['items'][$key]->catalog_content = $cut[0];

            // Get additional form values if there are any
            $form_values = Loader::gi()->callModule('GET', 'forms/getFormValues', array('form_id' => $cur_item->form_id, 'row_id' => $cur_item->id, 'table_name' => 'catalogs'));
            foreach ($form_values['items'] as $form_key => $form_item) {
                $fields[] = $form_item->field_name;
                if (is_array($form_item->value)) {
                    foreach ($form_item->value as $lang_item) {
                        if ($lang_item->short === Backstage::gi()->portal_current_lang) {
                            $cur_item->{$form_item->field_name} = $lang_item->translation;
                            break;
                        }
                    }
                } else
                    $cur_item->{$form_item->field_name} = $form_item->value;
            }

            $item = $design->structure;

            foreach ($structure_rules as $rule_key => $rule) {
                if (in_array($rule[2], $fields))
                    $item = str_replace($rule[1], $cur_item->$rule[2], $item);
            }

            // Loading files
            $parameters['object_id'] = $cur_item->id;
            $parameters['object_type'] = 'catalogs';
            if (isset($material_design))
                $parameters['design'] = $material_design;
            $files_data = Loader::gi()->callModule('GET', 'materials/getFiles', $parameters);

            $item = str_replace('[[files]]', $files_data['files'], $item);
            $item = str_replace('[[first_file]]', $files_data['first_file'], $item);


            /*
            if ($cur_item->cnt > 0)
            {
                    // Subdata
                $sub_data = '';
                $this->data['request']->parameters['parent_id'] = $cur_item->id;
                $sub_data = Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'pages/getCatalog', $this->data['request']->parameters);
                $item = str_replace('[[sub]]', $sub_data, $item);
            }
            $item = str_replace('[[sub]]', '', $item);
            */
            $this->data['items'][$key]->item = $item;
            $this->data['sequence'] .= $item;
        }


        $block = $design->block;
        foreach ($block_rules as $rule_key => $rule) {
            $block = str_replace('[[structure]]', $this->data['sequence'], $block);
        }

        $this->data['body'] = $block;

        return $this->data;
    }

    public function getCatalogItem()
    {
        $this->data = Loader::gi()->getModel($this->data);
        $this->data['item']->catalog_content = str_replace('[[cut]]', '', $this->data['item']->catalog_content);
        // Get design
        $design_name = isset($this->data['request']->parameters['lq']['design']) ? $this->data['request']->parameters['lq']['design'] : '';
        $des_data = Loader::gi()->callModule('GET', 'designs', array('where' => 'design_name="' . $design_name . '"'));
        if ($des_data['items'])
            $design = $des_data['items'][0];
        else
            throw new QException(array('ER-00026', $design_name));

        $block_rules_cnt = preg_match_all("/.*(\[\[([A-z0-9\-\_]+)\]\]).*/", $design->block, $block_rules, PREG_SET_ORDER);
        $structure_rules_cnt = preg_match_all("/(\[\[([A-z0-9\-\_]+)\]\])/", $design->structure, $structure_rules, PREG_SET_ORDER);

        // Get material design if there is any
        $material_design_name = isset($this->data['request']->parameters['lq']['material_design']) ? $this->data['request']->parameters['lq']['material_design'] : '';
        $material_des_data = Loader::gi()->callModule('GET', 'designs', array('where' => 'design_name="' . $material_design_name . '"'));
        if ($material_des_data['items']) {
            $material_design = $material_des_data['items'][0];
            $material_block_rules_cnt = preg_match_all("/.*(\[\[([A-z0-9\-\_]+)\]\]).*/", $material_design->block, $material_design->block_rules, PREG_SET_ORDER);
            $material_structure_rules_cnt = preg_match_all("/(\[\[([A-z0-9\-\_]+)\]\])/", $material_design->structure, $material_design->structure_rules, PREG_SET_ORDER);
        }

        $fields = array('id', 'catalog_name', 'catalog_title', 'catalog_content', 'insert_date', 'is_category', 'form_id');
        $this->data['sequence'] = '';

        $cur_item = $this->data['item'];

        // Get additional form values if there are any
        $form_values = Loader::gi()->callModule('GET', 'forms/getFormValues', array('form_id' => $cur_item->form_id, 'row_id' => $cur_item->id, 'table_name' => 'catalogs'));
        foreach ($form_values['items'] as $form_key => $form_item) {
            $fields[] = $form_item->field_name;
            if (is_array($form_item->value)) {
                foreach ($form_item->value as $lang_item) {
                    if ($lang_item->short === Backstage::gi()->portal_current_lang) {
                        $cur_item->{$form_item->field_name} = $lang_item->translation;
                        break;
                    }
                }
            } else
                $cur_item->{$form_item->field_name} = $form_item->value;
        }

        $item = $design->structure;
        foreach ($structure_rules as $rule_key => $rule) {
            if (in_array($rule[2], $fields))
                $item = str_replace($rule[1], $cur_item->$rule[2], $item);
        }

        // Loading files
        $parameters['object_id'] = $cur_item->id;
        $parameters['object_type'] = 'catalogs';
        if (isset($material_design))
            $parameters['design'] = $material_design;
        $files_data = Loader::gi()->callModule('GET', 'materials/getFiles', $parameters);

        $item = str_replace('[[files]]', $files_data['files'], $item);
        $item = str_replace('[[first_file]]', $files_data['first_file'], $item);

        $this->data['sequence'] .= $item;


        $block = $design->block;
        foreach ($block_rules as $rule_key => $rule) {
            $block = str_replace('[[structure]]', $this->data['sequence'], $block);
        }

        $this->data['body'] = $block;
        return $this->data;
    }

}
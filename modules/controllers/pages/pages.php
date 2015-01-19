<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cPages extends controller
{

    /**
     * @description        Returns the resource
     */
    public function get()
    {
        parent::get();

        // Get form data
        foreach ($this->data['items'] as $key => $cur_item) {
            if (isset($cur_item->form_id) && $cur_item->form_id!=0) {
                $cur_item->form_data = Loader::gi()->callModule('GET', 'forms/getFormValues', array('form_id' => $cur_item->form_id, 'row_id' => $cur_item->id, 'table_name' => $this->data['request']->resource_name));
                $cur_item->form_data = $cur_item->form_data['items'];
            }
        }

        $this->data['body'] = $this->data['items'];

        return $this->data;
    }

	/**
	 * Used to load page to the viewport (client)
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
    public function getPage()
    {
		$this->data = Loader::gi()->getModel($this->data);
			// Handle data by Linquistic Queries
		$this->data['query'] = $this->data['item']->layout_content.$this->data['item']->page_content;
		$this->data = Loader::gi()->getLQ($this->data);
		
			// Handle design if there is any
		$des_data = Loader::gi()->callModule('GET', 'designs', array('id'=>$this->data['item']->design_id));
		$design = @$des_data['items'][0];

        if ($design)
		{
			$design_data['query'] = $design->additional_style;
			$design_data = Loader::gi()->getLQ($design_data);
			$design->additional_style = $design_data['query'];
		}
			// View loading
		$this->data['view_name'] = 'getPage'; 
		$this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}
	
	/**
	 * Used to show a menu
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
    public function getMenu()
    {
		//$time_start = microtime(true); 
        // Model loading
        $this->data = Loader::gi()->getModel($this->data);
        
		// Get design
        $design_name = isset($this->data['request']->parameters['lq']['design'])?$this->data['request']->parameters['lq']['design']:'';
		$des_data = Loader::gi()->callModule('GET', 'designs', array('where'=>'design_name="'.$design_name.'"'));
		$design = $des_data['items'][0];

        $block_rules_cnt = preg_match_all("/.*(\[\[([A-z0-9\-\_]+)\]\]).*/", $design->block, $block_rules, PREG_SET_ORDER);
        $structure_rules_cnt = preg_match_all("/(\[\[([A-z0-9\-\_]+)\]\])/", $design->structure, $structure_rules, PREG_SET_ORDER);
        $fields = array('id', 'is_active', 'page_name', 'page_title');
        $this->data['sequence'] = '';

        foreach ($this->data['items'] as $key => $menu_item)
        {
            $item = $design->structure;
            foreach ($structure_rules as $rule_key => $rule)
            {
                if (in_array($rule[2], $fields))
                    $item = str_replace($rule[1], $menu_item->$rule[2], $item);
            }
               
            if (trim($menu_item->page_sub_menu) != '')
            {
				$sub_menu_data['request'] = new stdClass();
				$sub_menu_data['query'] = $menu_item->page_sub_menu;
				$sub_menu_data['request']->parameters = array();
				$sub_menu_data = Loader::gi()->getLQ($sub_menu_data);
                $item = str_replace('[[sub]]', $sub_menu_data['query'], $item);
            }
            elseif ($menu_item->cnt > 0)
			{
					// Submenu data
				$this->data['request']->parameters['parent_id'] = $menu_item->id;
				$sub_data = Loader::gi()->callModule('GET', 'pages/getMenu', $this->data['request']->parameters);
				$sub_data_body = $sub_data['body'];
				$item = str_replace('[[sub]]', $sub_data_body, $item);
			}
            $item = str_replace('[[sub]]', '', $item);
            $this->data['items'][$key]->item = $item;
            $this->data['sequence'] .= $item;
        }

        $block = $design->block;
        foreach ($block_rules as $rule_key => $rule)
        {
			//echo $rule[0];
            $block = str_replace('[[structure]]', $this->data['sequence'], $block);
        }
        $this->data['block'] = $block;

        // View loading
		$this->data['design'] = $design;
        $this->data['view_name'] = 'menu';
        $this->data['body'] = Loader::gi()->getView($this->data);
		
		//Debugger::gi()->logFile('Execution time in controller catalogs: '.round(microtime(true) - $time_start,6).' sec');

        return $this->data;
    }

	/**
	 * Used to return a language bar
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
	public function getLangBar()
	{
        $this->data = Loader::gi()->getModel($this->data);
	
		// Get design
        $design_name = isset($this->data['request']->parameters['lq']['design'])?$this->data['request']->parameters['lq']['design']:'';
		$des_data = Loader::gi()->callModule('GET', 'designs', array('where'=>'design_name="'.$design_name.'"'));
		if ($des_data['items'])
			$design = $des_data['items'][0];
		else
			throw new QException(array('ER-00026', $design_name));
			
        $block_rules_cnt = preg_match_all("/.*(\[\[([A-z0-9\-\_]+)\]\]).*/", $design->block, $block_rules, PREG_SET_ORDER);
        $structure_rules_cnt = preg_match_all("/(\[\[([A-z0-9\-\_]+)\]\])/", $design->structure, $structure_rules, PREG_SET_ORDER);
        $fields = array('id', 'short', 'language', 'language_url');
        
		$this->data['sequence'] = '';
		$items = array();

		foreach ($this->data['items'] as $key => $lang_item)
        {
			if (strstr(Backstage::gi()->full_url, 'lang='))
				$lang_item->language_url = preg_replace('/lang=([A-z]{2})/', 'lang='.$lang_item->short, Backstage::gi()->full_url);
			else			
				$lang_item->language_url = strstr(Backstage::gi()->full_url, '?')?Backstage::gi()->portal_url.Backstage::gi()->clean_url.'&lang='.$lang_item->short:Backstage::gi()->portal_url.Backstage::gi()->clean_url.'?lang='.$lang_item->short;
			
            $items[$key] = $design->structure;
            foreach ($structure_rules as $rule_key => $rule)
            {
                if (in_array($rule[2], $fields))
                    $items[$key] = str_replace($rule[1], $lang_item->$rule[2], $items[$key]);
            }
			$lq['query'] = $items[$key];
			$lq = Loader::gi()->getLQ($lq);
			$items[$key] = $lq['query'];
        }

		$this->data['sequence'] = implode('', $items);

        $block = $design->block;
        foreach ($block_rules as $rule_key => $rule)
        {
            $block = str_replace('[[structure]]', $this->data['sequence'], $block);
        }
        $this->data['body'] = $block;
		
        return $this->data;
	}
	
	/**
	 * Used to create a new page using data sent by post
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
    public function post()
    {
		$page_data = $this->data['request']->parameters;
		
		$page_data['is_active'] = isset($page_data['is_active'])?$page_data['is_active']:0;
		$page_data['is_main'] = isset($page_data['is_main'])?$page_data['is_main']:0;
		$page_data['is_visible'] = isset($page_data['is_visible'])?$page_data['is_visible']:0;
		
		if (is_array($page_data['page_title']))
		{
			$page_data['translations']['page_title'] = $page_data['page_title'];
			$page_data['page_title'] = '';
		}

		if (is_array($page_data['page_content']))
		{
			$page_data['translations']['page_content'] = $page_data['page_content'];
			$page_data['page_content'] = '';
		}

		$this->data['page_data'] = $page_data;
		
		$this->data = Loader::gi()->getModel($this->data);
		
        $this->data['body'] = $this->data['page_data'];
        return $this->data;
    }

	/**
	 * Used in CRUD to add a LQ and a content when the text data is empty
	 * Please use $this->data['request']->parameters['parameter_name'] to get parameters
	 *
	 * @return array global $this->data
	 */	
    public function addLQContent()
    {
		if ($this->data['request']->method == 'POST')
		{
			$form_params = json_decode($this->data['request']->parameters['form_params'], true);
			$form_values = json_decode($this->data['request']->parameters['form_values'], true);
			$translations = json_decode($form_params['translations']);			
			$this->data['values'] = array();
			$this->data['values']['id'] = $this->data['request']->parameters['id'];
			if (in_array('page_content', $translations))
			{
				unset($form_values['pages^page_content'][0]);
				foreach ($form_values['pages^page_content'] as $key=>$value)
				{
					if ($value == '')
						$this->data['values']['translations']['page_content'][$key] = '[[module type=contents name='.$form_values['pages^page_name'].' action=getContent container=central_container]]';
				}
				$this->data['values']['page_content'] = '';
			}
			elseif ($form_values['pages^page_content'] == '')
				$this->data['values']['page_content'] = '[[module type=contents name='.$form_values['pages^page_name'].' action=getContent container=central_container]]';
			
			$this->data['content_values']['content_name'] = $form_values['pages^page_name'];
			$this->data['content_values']['content'] = '';
			$this->data['content_values']['design_id'] = 0;
			
			$this->data = Loader::gi()->getModel($this->data);
		}
        $this->data['body'] = '-';
        return $this->data;
    }

}
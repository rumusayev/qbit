<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cCMS extends controller
{
    public function get()
    {
        $this->contents();
        return $this->data;
    }
	
	private function navigationData()
	{
		$this->data['catalogs'] = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'catalogs', array('where'=>'parent_id = 0')));
	}
	
    public function pages()
    {
		// Collect global menu data
		$this->navigationData();
		
		$pages = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'pages', array('order'=>'page_name')), true);
		$pages_arr = array();
		foreach ($pages as $key=>$page) 
            $pages_arr[$page['id']] = $page['page_name'];

		$layouts = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'layouts', array('order'=>'layout_name')), true);
		$layouts_arr = array();
		foreach ($layouts as $key=>$layout) 
            $layouts_arr[$layout['id']] = $layout['layout_name'];

        $crud_pages = new Crud("pages");
        $this->data['crud_pages'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'pages')
            ->setFields('id', 'page_name', 'page_title', 'parent_id', 'page_meta_title', 'page_meta_keywords', 'page_meta_description', 'page_content', 'page_menu_group', 'page_sub_menu', 'layout_id', 'is_visible', 'is_active', 'is_main', 'ordering')
            ->setSearch('*')
            ->setIDs('id')
            //->restrict('add','edit','delete')
            ->mapTitles(
                'page_name', 'Name',
                'page_title', 'Title',
                'parent_id', 'Parent page',
                'page_meta_title', 'Meta title',
                'page_meta_keywords', 'Meta keyword',
                'page_meta_description', 'Meta description',
                'page_content', 'Content',
                'page_menu_group', 'Menu group',
                'page_sub_menu', 'Sub menu',
                'layout_id', 'Layout',
                'is_visible', 'Visibility',
                'is_active', 'Active',
                'is_main', 'Main',
				'ordering', 'Order number')
            ->mapParents('id','parent_id')
			->hiddenEditFields('design_id', 'is_visible', 'is_active', 'is_main')
            ->setTranslations('page_title', 'page_content')
            ->setEditor('page_content')
            ->mapFieldInputs(
                'layout_id', 'select:' . json_encode($layouts_arr),
                'parent_id', 'select:' . json_encode($pages_arr),
                'is_visible', 'checkbox:1',
                'is_active', 'checkbox:1',
                'is_main', 'checkbox:1')
            ->setGrants('pages')				
            ->afterSave('pages/addLQContent')
            ->execute();

        $this->data['view_name'] = 'pages';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }
	
    public function contents()
    {
		// Collect global menu data
		$this->navigationData();
	
		$designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'designs'), true);
		$designs_arr = array();
		foreach ($designs as $key=>$design) 
            $designs_arr[$design['id']] = $design['design_name'];

        $crud_pages = new Crud("contents");
        $this->data['crud_contents'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'contents')
            ->setFields('id', 'design_id', 'content_name', 'content', 'is_visible')
            ->setSearch('*')
            ->setIDs('id')
			->hiddenEditFields('design_id', 'is_visible')
            ->setGrants('contents')
            //->restrict('add','edit','delete')
            ->mapTitles(
                'design_id', 'Design',
                'content_name', 'Content name',
                'content', 'Content',
                'is_visible', 'Visibility')
            ->mapFieldInputs('design_id', 'select:' . json_encode($designs_arr), 'is_visible', 'checkbox:1')
            ->setTranslations('content')
            ->setEditor('content')
            ->disabledTableFields('content')
            ->execute();

        $this->data['view_name'] = 'contents';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

    public function catalogs()
    {
		// Collect global menu data
		$this->navigationData();

		$designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'designs', array('order'=>'design_name')), true);
		$designs_arr = array();
		foreach ($designs as $key=>$design) 
            $designs_arr[$design['id']] = $design['design_name'];

		$catalogs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'catalogs', array('order'=>'catalog_name')), true);
		$catalogs_arr = array();
		foreach ($catalogs as $key=>$catalog) 
            $catalogs_arr[$catalog['id']] = $catalog['catalog_name'];

		$forms = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'forms'), true);
		$forms_arr = array();
		foreach ($forms as $key=>$form) 
            $forms_arr[$form['id']] = $form['form_name'];

        $crud_pages = new Crud("catalogs");
        $this->data['crud_catalogs'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix.'catalogs')
            ->setFields('id', 'parent_id', 'design_id', 'form_id', 'catalog_name', 'catalog_title', 'catalog_content', 'catalog_group', 'is_category', 'is_visible', 'insert_date')
            ->setSearch('*')
            ->setIDs('id')
			->hiddenEditFields('parent_id', 'design_id', 'form_id', 'is_category', 'is_visible', 'catalog_group')
//			->disabledEditFields('parent_id', 'design_id', 'form_id', 'is_category', 'is_visible', 'catalog_group')
            ->setGrants('catalogs')
            ->mapParents('id','parent_id')
            ->setSystemValues('crud_parent_id', $this->data['request']->parameters['id'])
            //->restrict('add','edit','delete')
            ->mapTitles(
                'parent_id', 'Parent catalog',
                'design_id', 'Design',
                'form_id', 'Form',
                'catalog_name', 'Name',
                'catalog_title', 'Title',
                'catalog_content', 'Content',
                'catalog_group', 'Group',
                'is_category', 'Is category',
                'is_visible', 'Visibility',
                'insert_date', 'Modify date')
            ->setTranslations('catalog_title', 'catalog_content')
            ->setUploader('catalogs')
            ->mapFieldInputs('design_id', 'select:' . json_encode($designs_arr), 
							'parent_id', 'select:' . json_encode($catalogs_arr), 
							'form_id', 'select:' . json_encode($forms_arr), 
							'is_category', 'checkbox:1',
							'is_visible', 'checkbox:1')
            ->setEditor('catalog_content')
            ->execute();

        $this->data['view_name'] = 'catalogs';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }



}
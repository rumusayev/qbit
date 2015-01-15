<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cAdmin extends controller
{
    public function get()
    {
        $this->pages();
        //$this->addPage();
        return $this->data;
    }

    public function pages()
    {
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
            ->validateUnique('page_name')
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
            ->disabledTableFields('child_count')
            ->mapParents('id','parent_id')
            ->setTranslations('page_title', 'page_content')
            ->setEditor('page_content')
            ->mapFieldInputs(
                'layout_id', 'select:' . json_encode($layouts_arr),
                'parent_id', 'select:' . json_encode($pages_arr),
                'is_visible', 'checkbox:1',
                'is_active', 'checkbox:1',
                'is_main', 'checkbox:1')
            ->setGrants('pages')
            ->addEasyLQ('page_name', 'page_title', 'page_meta_title', 'page_meta_keywords', 'page_meta_description', 'page_content', 'page_menu_group', 'page_sub_menu')
            ->afterSave('pages/addLQContent')
            ->execute();

        $this->data['view_name'] = 'pages';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }


    public function layouts()
    {
		$designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'designs'), true);
		$designs_arr = array();
		foreach ($designs as $key=>$design) 
            $designs_arr[$design['id']] = $design['design_name'];
		
        $crud_pages = new Crud("layouts");
        $this->data['crud_layouts'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'layouts')
            ->setFields('id', 'design_id', 'layout_name', 'layout_content')
            ->setSearch('*')
            ->setIDs('id')
            ->validateUnique('layout_name')
//            ->restrict('add','edit','delete')
            ->disabledTableFields('layout_content')
            ->mapTitles(
                'design_id', 'Design',
                'layout_name', 'Layout name',
                'layout_content', 'Layout content')
//           ->setTranslations('layout_content')
            ->setEditor('layout_content')
            ->mapFieldInputs(
                'design_id', 'select:' . json_encode($designs_arr)
				)
            ->addEasyLQ('layout_content')
            ->execute();

        $this->data['view_name'] = 'layouts';
        $this->data['body'] = Loader::gi()->getView($this->data);

        return $this->data;
    }

    public function contents()
    {
		$designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'designs'), true);
		$designs_arr = array();
		foreach ($designs as $key=>$design) 
            $designs_arr[$design['id']] = $design['design_name'];

        $crud_pages = new Crud("contents");
        $this->data['crud_contents'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'contents')
            ->setFields('id', 'content_name', 'content', 'is_visible', 'is_searchable')
            ->setSearch('*')
            ->setIDs('id')
            ->validateUnique('content_name')
            //->restrict('add','edit','delete')
            ->mapTitles(
                'design_id', 'Design',
                'content_name', 'Content name',
                'content', 'Content',
                'is_searchable', 'Searchable',
                'is_visible', 'Visibility')
            ->mapFieldInputs('is_visible', 'checkbox:1', 'is_searchable', 'checkbox:1')
            ->setTranslations('content')
            ->setGrants('contents')			
            ->setEditor('content')
            ->disabledTableFields('content')
            ->addEasyLQ('content_name', 'content')
            ->execute();

        $this->data['view_name'] = 'contents';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

    public function catalogs()
    {
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
        $this->data['crud_catalogs'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'catalogs')
            ->setFields('id', 'parent_id', 'design_id', 'form_id', 'catalog_name', 'catalog_title', 'catalog_content', 'catalog_group', 'is_category', 'is_visible', 'is_searchable', 'insert_date')
            ->setSearch('*')
            ->setIDs('id')
            ->validateUnique('catalog_name')			
            ->setGrants('catalogs')
            ->mapParents('id','parent_id')
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
                'is_searchable', 'Searchable',
                'insert_date', 'Modify date')
            ->disabledTableFields('child_count')
            ->setTranslations('catalog_title', 'catalog_content')
            ->setForm('form_id', 'catalogs')
            ->setUploader('catalogs')
            ->mapFieldInputs('design_id', 'select:' . json_encode($designs_arr), 
							'parent_id', 'select:' . json_encode($catalogs_arr), 
							'form_id', 'select:' . json_encode($forms_arr), 
							'is_category', 'checkbox:1',
							'is_searchable', 'checkbox:1',
							'is_visible', 'checkbox:1')
            ->setEditor('catalog_content')
            ->addEasyLQ('catalog_name', 'catalog_title', 'catalog_content', 'catalog_group')
            ->execute();

        $this->data['view_name'] = 'catalogs';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

    public function designs()
    {
        $crud_pages = new Crud("designs");
        $this->data['crud_designs'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'designs')
            ->setFields('id', 'design_name', 'block', 'structure', 'additional_style')
            ->setSearch('*')
            ->setIDs('id')
            ->validateUnique('design_name')
            ->setFormFieldsDimensions('block','700,300', 'structure','700,300')
            //->restrict('add','edit','delete')
            ->mapTitles(
                'design_name', 'Name',
                'block', 'Block',
                'structure', 'Structure',
                'additional_style', 'Additional Style')
            ->addEasyLQ('block', 'structure', 'additional_style')
            ->execute();

        $this->data['view_name'] = 'designs';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }
	
    public function forms()
    {
		$designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url.'designs', array('order'=>'design_name')), true);
		$designs_arr = array();
		foreach ($designs as $key=>$design) 
            $designs_arr[$design['id']] = $design['design_name'];	
        $crud_forms = new Crud("forms");
        $this->data['crud_forms'] = $crud_forms->setTables(Backstage::gi()->db_table_prefix.'forms')
            ->setFields('id', 'form_name', 'design_id')
            ->setSearch('*')
            ->setIDs('id')
            ->validateUnique('form_name')
			->setButtons('getFormFields', 'glyphicon glyphicon-th-list')	
			->afterDelete('forms/deleteFormFields')
            ->mapTitles(
                'form_name', 'Form name',
                'design_id', 'Design')
            ->mapFieldInputs('design_id', 'select:' . json_encode($designs_arr))				
            ->execute();
			
		$crud_selects = new Crud("form_selects");
        $this->data['crud_selects'] = $crud_selects->setTables(Backstage::gi()->db_table_prefix.'form_field_selects')
            ->setFields('id', 'select_name')
            ->setSearch('*')
            ->setIDs('id')
            ->validateUnique('select_name')
			->setButtons('getFormFieldSelectOptions', 'glyphicon glyphicon-th-list')	
			->afterDelete('forms/deleteFormFieldSelects')
            ->mapTitles(
                'select_name', 'Select name')
            ->execute();			

        $this->data['view_name'] = 'forms';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

    public function users()
    {
        $crud_pages = new Crud("users");
        $this->data['crud_users'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'users')
            ->setFields('id', 'login', 'password', 'name', 'surname', 'patronymic', 'email', 'about', 'is_visible')
            ->setSearch('*')
            ->setIDs('id')
            //->restrict('add','edit','delete')
			->mapPasswords('password', 'md5')
			->disabledTableFields('password')			
            ->mapTitles(
                'login', 'Login',
                'password', 'Password',
                'name', 'First name',
                'surname', 'Last name',
                'patronymic', 'Middle name',
                'email', 'E-mail',
                'about', 'About',
                'is_visible', 'Visibility')
            //->setTranslations('catalog_title', 'catalog_content')
            ->mapFieldInputs('is_visible', 'checkbox:1')
            ->execute();

        $this->data['view_name'] = 'users';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

    public function grants()
    {
        $crud_grants = new Crud("grants");
        $this->data['crud_grants'] = $crud_grants->setTables(Backstage::gi()->db_table_prefix.'users')
            ->setFields('id', 'login', 'name', 'surname', 'patronymic', 'email', 'about')
            ->setSearch('*')
            ->setIDs('id')
            ->restrict('add','edit','delete')
            ->mapTitles(
                'login', 'Login',
                'name', 'First name',
                'surname', 'Last name',
                'patronymic', 'Middle name',
                'email', 'E-mail',
                'about', 'About')
			->setButtons('getUserGrants', 'glyphicon glyphicon-ok')
            ->mapFieldInputs('is_visible', 'checkbox:1')
            ->execute();
	
        $crud_roles = new Crud("roles");
        $this->data['crud_roles'] = $crud_roles->setTables(Backstage::gi()->db_table_prefix.'roles')
            ->setFields('id', 'role_name')
            ->setSearch('*')
            ->setIDs('id')
            //->restrict('add','edit','delete')
            ->mapTitles('role_name', 'Role name')
			->setButtons('getRoleGrants', 'glyphicon glyphicon-ok')
            ->mapFieldInputs('is_visible', 'checkbox:1')
            ->execute();

        $this->data['view_name'] = 'grants';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;		
    }

    public function translations()
    {
        $crud_pages = new Crud("translations_words");
        $this->data['crud_static_translations'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'translations_words')
            ->setFields('id, w_key, w_value')
            ->setSearch('*')
            ->validateUnique('w_key')
            ->setIDs('id')
            //->restrict('add','edit','delete')
            ->mapTitles(
                'w_key', 'Key',
                'w_value', 'Translation')
            ->setTranslations('w_value')
            ->execute();

        $this->data['view_name'] = 'static_translations';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

}
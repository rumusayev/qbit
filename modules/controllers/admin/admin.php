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
        $pages = Loader::gi()->callModule('GET', 'pages', array('order' => 'page_name'));
        $pages_arr = array();
        foreach ($pages['items'] as $key => $page)
            $pages_arr[$page->id] = $page->page_name;

        $layouts = Loader::gi()->callModule('GET', 'layouts', array('order' => 'layout_name'));
        $layouts_arr = array();
        foreach ($layouts['items'] as $key => $layout)
            $layouts_arr[$layout->id] = $layout->layout_name;

        $crud_pages = new Crud("pages");
        $this->data['crud_pages'] = $crud_pages->setQuery('SELECT pages.*, layouts.layout_name from '.Backstage::gi()->db_table_prefix . 'pages LEFT JOIN '.Backstage::gi()->db_table_prefix . 'layouts ON pages.layout_id = layouts.id')
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
                'layout_name', 'Layout name',
                'is_visible', 'Visibility',
                'is_active', 'Active',
                'is_external_link', 'Link to external URL',
                'external_url_target', 'External URL Target',
                'is_main', 'Main',
                'ordering', 'Order number')
            ->disabledTableFields('child_count', 'layout_id')
//            ->disabledEditFields('layout_name')
			->removeFields('layout_name, design_id', 'add')
            ->disableSavingToTables('layouts')
            ->mapParents('id', 'parent_id')
            ->setParentTable('pages')
            ->setTranslations('page_title', 'page_content')
            ->setEditor('page_content')
            ->mapFieldInputs(
                'layout_id', 'select:' . json_encode($layouts_arr),
                'parent_id', 'select:' . json_encode($pages_arr),
                'is_visible', 'checkbox:1',
                'is_active', 'checkbox:1',
                'is_external_link', 'checkbox:1',
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
        $designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url . 'designs'), true);
        $designs_arr = array();
        foreach ($designs as $key => $design)
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
        $designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url . 'designs'), true);
        $designs_arr = array();
        foreach ($designs as $key => $design)
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
        $designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url . 'designs', array('order' => 'design_name')), true);
        $designs_arr = array();
        foreach ($designs as $key => $design)
            $designs_arr[$design['id']] = $design['design_name'];

        $catalogs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url . 'catalogs', array('order' => 'catalog_name')), true);
        $catalogs_arr = array();
        foreach ($catalogs as $key => $catalog)
            $catalogs_arr[$catalog['id']] = $catalog['catalog_name'];

        $forms = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url . 'forms'), true);
        $forms_arr = array();
        foreach ($forms as $key => $form)
            $forms_arr[$form['id']] = $form['form_name'];

        $crud_pages = new Crud("catalogs");
        $this->data['crud_catalogs'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'catalogs')
            ->setFields('id', 'parent_id', 'design_id', 'form_id', 'catalog_name', 'catalog_title', 'catalog_content', 'catalog_group', 'is_category', 'is_visible', 'is_searchable', 'insert_date')
            ->setSearch('*')
            ->setIDs('id')
            ->validateUnique('catalog_name')
            ->setGrants('catalogs')
            ->mapParents('id', 'parent_id')
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
            ->setFormFieldsDimensions('block', '700,300', 'structure', '700,300')
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
        $designs = json_decode(Loader::gi()->callAPI('GET', Backstage::gi()->portal_url . 'designs', array('order' => 'design_name')), true);
        $designs_arr = array();
        foreach ($designs as $key => $design)
            $designs_arr[$design['id']] = $design['design_name'];
        $crud_forms = new Crud("forms");
        $this->data['crud_forms'] = $crud_forms->setTables(Backstage::gi()->db_table_prefix . 'forms')
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
        $this->data['crud_selects'] = $crud_selects->setTables(Backstage::gi()->db_table_prefix . 'form_field_selects')
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
        $this->data['crud_grants'] = $crud_grants->setTables(Backstage::gi()->db_table_prefix . 'users')
            ->setFields('id', 'login', 'name', 'surname', 'patronymic', 'email', 'about')
            ->setSearch('*')
            ->setIDs('id')
            ->restrict('add', 'edit', 'delete')
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
        $this->data['crud_roles'] = $crud_roles->setTables(Backstage::gi()->db_table_prefix . 'roles')
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
        $crud_pages = new Crud("translations");
        $this->data['crud_static_translations'] = $crud_pages->setTables(Backstage::gi()->db_table_prefix . 'translations')
            ->setFields('id, field_name, '.Backstage::gi()->portal_langs)
            ->setSearch('*')
            ->setWhere('row_id = 0')
            ->validateUnique('field_name')
            ->setIDs('id')
            ->mapTitles(
                'field_name', 'Key')
            ->execute();

        $this->data['view_name'] = 'static_translations';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

    public function siteConfigs()
    {

        if (isset($this->data['request']->parameters['portal_url'])) {

            if (!empty($this->data['request']->parameters['portal_url'])
                || !empty($this->data['request']->parameters['portal_email'])
                || !empty($this->data['request']->parameters['portal_name'])
                || !empty($this->data['request']->parameters['portal_langs'])
                || !empty($this->data['request']->parameters['portal_default_lang'])
                || !empty($this->data['request']->parameters['template_name'])
                || !empty($this->data['request']->parameters['db_host'])
                || !empty($this->data['request']->parameters['db_name'])
                || !empty($this->data['request']->parameters['db_user'])
                || !empty($this->data['request']->parameters['db_pass'])
            ) {
                try {
                    @$dbh = new pdo("mysql:host=" . $this->data['request']->parameters['db_host'] . ";dbname=" . $this->data['request']->parameters['db_name'],
                        $this->data['request']->parameters['db_user'],
                        $this->data['request']->parameters['db_pass']);
                    $this->data['status']['mysql'] = 'mysql_ok';
                } catch (PDOException $ex) {
                    $this->data['status']['mysql'] = 'mysql_connection_error';
                }

                if ($this->data['status']['mysql'] == 'mysql_ok') {
                    $this->data['status'] = true;

                    // Editing config file
                    $portal_langs = trim($this->data['request']->parameters['portal_all_langs'], ",");

                    $this->data['portal_all_langs'] = $portal_langs;
                    $this->data = Loader::gi()->getModel($this->data);

                    $portal_default_lang = substr($this->data['request']->parameters['portal_default_lang'], 0, strpos($this->data['request']->parameters['portal_default_lang'], "|"));

                    // Write to config
                    $config_file = file_get_contents(Backstage::gi()->CONFIG_DIR . 'config.php');

                    $config_file = preg_replace('/("db_host" =>).+"/', '"db_host" => "' . $this->data['request']->parameters['db_host'] . '"', $config_file);
                    $config_file = preg_replace('/("portal_email" =>).+"/', '"portal_email" => "' . $this->data['request']->parameters['portal_email'] . '"', $config_file);
                    $config_file = preg_replace('/("portal_name" =>).+"/', '"portal_name" => "' . $this->data['request']->parameters['portal_name'] . '"', $config_file);
                    $config_file = preg_replace('/("db_name" =>).+"/', '"db_name" => "' . $this->data['request']->parameters['db_name'] . '"', $config_file);
                    $config_file = preg_replace('/("db_user" =>).+"/', '"db_user" => "' . $this->data['request']->parameters['db_user'] . '"', $config_file);
                    $config_file = preg_replace('/("db_pass" =>).+"/', '"db_pass" => "' . $this->data['request']->parameters['db_pass'] . '"', $config_file);
                    $config_file = preg_replace('/("portal_url" =>).+"/', '"portal_url" => "' . $this->data['request']->parameters['portal_url'] . '"', $config_file);
                    $config_file = preg_replace('/("template_name" =>).+"/', '"template_name" => "' . $this->data['request']->parameters['template_name'] . '"', $config_file);
                    $config_file = preg_replace('/("portal_langs" =>).+"/', '"portal_langs" => "' . $portal_langs . '"', $config_file);
                    $config_file = preg_replace('/("portal_default_lang" =>).+"/', '"portal_default_lang" => "' . $portal_default_lang . '"', $config_file);
                    $config_file = preg_replace('/("portal_url" =>).+"/', '"portal_url" => "' . $this->data['request']->parameters['portal_url'] . '"', $config_file);
                    $config_file = preg_replace('/("db_table_prefix" =>).+"/', '"db_table_prefix" => "' . $this->data['request']->parameters['db_table_prefix'] . '"', $config_file);

                    $fname = Backstage::gi()->CONFIG_DIR . 'config.php';

                    $f = fopen($fname, "w") or die("Unable to open file!");
                    fwrite($f, $config_file);
                    fclose($f);

                    // End of Editing config file

                } elseif ($this->data['status']['mysql'] == 'mysql_connection_error') {
                    $this->data['status'] = false;
                    $this->data['status']['mysql'] = false;
                }
            } else {
                $this->data['status'] = 'empty';
            }
            $this->data['body']['status'] = $this->data['status'];
        } else {
            $this->data = Loader::gi()->getModel($this->data);
            $this->data['view_name'] = 'site_configs';
            $this->data['body'] = Loader::gi()->getView($this->data);
        }

        return $this->data;
    }

}
<?php

/**
 * @package    crud
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class Crud 
{	
    private $name;	
    private $field_names = '';	
    //private $field_translations = '';	
    private $titles = array();	
    private $mapped_values = array();	
    private $mapped_values_f = array();	
    private $types = array();	
    private $links = array();	
    private $buttons = array();	
    private $js_handlers = array();	
    private $format_rules = array();	
    private $form_fields_dimensions = array();	
    private $ids = array();
    private $mapped_fields = array();
    private $mapped_search = array();
    private $mapped_field_inputs = array();
    private $mapped_parents = array();
    private $mapped_passwords = array();
    private $restrictions = array();
    private $hidden_edit_fields = array();
    private $disabled_edit_fields = array();
    private $disabled_table_fields = array();
    private $query = '';
    private $tables = '';
    private $where = '1=1';
    private $totals = '';
    private $order = '';
    private $search = array();
    private $before_save_method_path = '';
    private $after_save_method_path = '';
    private $after_load_method_path = '';
    private $after_delete_method_path = '';
    private $override_orig_save = 0;
    private $translations = array();
    private $uploader_object_type = '';
    private $add_lq_button = array();
    private $add_editor_list = array();
    private $crud_parent_id = 0;
    private $crud_resource_types = array();
    private $unique_fields = array();
    private $additional_form_field = '';
    private $additional_form_table = '';
    private $manual_search_format = array();

    function __construct($name = 'default_crud') 	
    {		
        $this->name = $name;	
    }

    public function setFields()
    {
        $arg_list = func_get_args();
        $this->field_names = implode(',', $arg_list);
        return $this;
    }

    public function setWhere($where)	
    {		
        $this->where = $where;		
        return $this;	
    }
    
    public function setOrder()	
    {		
        $arg_list = func_get_args();
        $this->order = implode(',', $arg_list);
        return $this;
    }

	/**
	 * Validate a field for uniquness
	 *
	 * @param string Field name(s)
	 * @return object Self object
	 */
    public function validateUnique()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->unique_fields[] = $arg;
        return $this;
    }

	/**
	 * Specifies whether the resource will be restricted
	 *
	 * @param string Resource types (table names)
	 * @return object Self object
	 */
    public function setGrants()
    {		
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->crud_resource_types[] = $arg;
        return $this;
    }

	/**
	 * Sets field(s) that should participate in search
	 * set * if you want all fields to be searchable
	 *
	 * @param string Field name
	 * @return object Self object
	 */
    public function setSearch()
    {		
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->search[] = $arg;
        return $this;
    }

	// Used if a field or fields should have a manual search format)(e.g. you want to use % in the search string by yourself instead of the programm)
	// field_names
    public function setManualSearchFormat()	
    {		
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)

            $this->manual_search_format[] = $arg;
        return $this;
    }
	
	/**
	 * Sets query if you don't want to use tables
	 *
	 * @param string $query A SQL-query
	 * @return object Self object
	 */
    public function setQuery($query)	
    {		
        $this->query = $query;		
        return $this;	
    }
	
	/**
	 * Sets table(s) for a query that will be prepared
	 *
	 * @param string Table name
	 * ... 
	 * @return object Self object
	 */        
    public function setTables()	
    {
		$arg_list = func_get_args();
		$this->tables = implode(',', $arg_list);
        return $this;
    }

	/**
	 * Totals that will be shown on the bottom of a CRUD table
	 *
	 * @param string Field name
	 * ... 
	 * @return object Self object
	 */    
    public function setTotals()	
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->totals[$arg] = 0;
        return $this;		
    }    

	/**
	 * Map a search field(s) to a custom form input(s)
	 *
	 * @param string Field name
	 * @param string json-type string (input_type:options_in_json)
	 * ...
	 * @return object Self object
	 */    
    public function mapSearch()
    {
        $arg_list = func_get_args();
        $mapped_search = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
            if(!($i&1)) 
                $mapped_search[$arg_list[$i]] = base64_encode($arg_list[$i+1]);
        $this->mapped_search = $mapped_search;

        return $this;
    }    
	
	/**
	 * Map a field(s) to a custom form input(s)
	 *
	 * @param string Field name
	 * @param string json-type string (input_type:options_in_json)
	 * ...
	 * @return object Self object
	 */    	
    public function mapFieldInputs()
    {
        $arg_list = func_get_args();
        $mapped_field_inputs = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++)
            if(!($i&1)) 
                $mapped_field_inputs[$arg_list[$i]] = base64_encode($arg_list[$i+1]);
        $this->mapped_field_inputs = $mapped_field_inputs;

        return $this;
    }    

	/**
	 * Map a title(s) to a custom field(s)
	 *
	 * @param string Field name
	 * @param string Title
	 * ...
	 * @return object Self object
	 */	
    public function mapTitles()
    {
        $arg_list = func_get_args();
        $titles = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++)
            if(!($i&1))
				$titles[$arg_list[$i]] = $arg_list[$i+1];
        $this->titles = $titles;

        return $this;
    }	
	
	/**
	 * Map a field(s) to a parent field(s) to make hierarchical view
	 *
	 * @param string Field name
	 * @param string Parent field name
	 * ...
	 * @return object Self object
	 */	
    public function mapParents()
    {
        $arg_list = func_get_args();
        $mapped_parents = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++)
            if(!($i&1))
				$mapped_parents[$arg_list[$i]] = $arg_list[$i+1];
        $this->mapped_parents = $mapped_parents;

        return $this;
    }

	/**
	 * Set system value(s) which are used for CRUD internal purposes
	 *
	 * @param string Parameter name
	 * @param string Value
	 * ...
	 * @return object Self object
	 */	
	public function setSystemValues()
    {
        $arg_list = func_get_args();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
            if(!($i&1))
				$this->{$arg_list[$i]} = $arg_list[$i+1];
		return $this;
    }

	/**
	 * Map a value(s) to a custom field(s)
	 *
	 * @param string Field name
	 * @param string Value
	 * ...
	 * @return object Self object
	 */	
	public function mapValues()
    {
        $arg_list = func_get_args();
        $mapped_values = array();
        $cnt = count($arg_list);
        for ($i=1; $i<$cnt-1; $i++) 
			if($i&1) 
				$mapped_values[$arg_list[$i]] = $arg_list[$i+1];
        $this->mapped_values[$arg_list[0]] = $mapped_values;
        return $this;
    }
	
	/**
	 * Map a value(s) to a custom field(s) using a function
	 *
	 * @param string Field name
	 * @param string Value
	 * ...
	 * @return object Self object
	 */	
	public function mapValuesF()
    {
        $arg_list = func_get_args();
        $mapped_values_f = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
			if(!($i&1)) 
				$mapped_values_f[$arg_list[$i]] = base64_encode($arg_list[$i+1]);
        $this->mapped_values_f = $mapped_values_f;
        return $this;
    }
	
    public function mapTypes()
    {
        $arg_list = func_get_args();
		$types = array();
		$cnt = count($arg_list);
		for ($i=0; $i<$cnt-1; $i++) 
			if(!($i&1)) 
				$types[$arg_list[$i]] = $arg_list[$i+1];
		$this->types = $types;

        return $this;
    }
    
    // Map a field to a js function (the field will be framed by a href)
    // field_name, jsmethod, ...
    public function mapLinks()
    {
        $arg_list = func_get_args();
        $links = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
            if(!($i&1)) 
                $links[$arg_list[$i]] = $arg_list[$i+1];
        $this->links = $links;
        return $this;
    }
        
    // Set buttons with js functions (a new button will be added near the functional buttons)
    // jsmethod, icon class...
    public function setButtons()
    {
        $arg_list = func_get_args();
        $buttons = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
            if(!($i&1)) 
                $buttons[$arg_list[$i]] = $arg_list[$i+1];
        $this->buttons = $buttons;
        return $this;
    }        
	
    // Set a js handler for the specific field
    // field_name, js event (onchange, onclick,...), handler function, ...
    public function setJSHandlers()
    {
        $arg_list = func_get_args();
        $js_handlers = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
			if(!($i%3))
			{
				$js_handlers[$arg_list[$i]]['event'] = $arg_list[$i+1];
				$js_handlers[$arg_list[$i]]['handler'] = $arg_list[$i+2];
			}
        $this->js_handlers = $js_handlers;
        return $this;
    }
    
    // Set conditional formatting
    // field_name, css_class_name, ...
    public function formatRowByRule()
    {
        $arg_list = func_get_args();
        $links = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
            if(!($i&1)) 
                $format_rules[$arg_list[$i]] = $arg_list[$i+1];
        $this->format_rules = $format_rules;
        return $this;
    }
    
	/**
	 * Set translations for the specified fields
	 *
	 * @param string Field name
	 * ...
	 * @return object Self object
	 */	
    public function setTranslations()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->translations[] = $arg;
        return $this;
    }  
	
	/**
	 * Activate adding materials for the object
	 *
	 * @param string Object type
	 * ...
	 * @return object Self object
	 */	
    public function setUploader($uploader_object_type)
    {
		$this->uploader_object_type = $uploader_object_type;
        return $this;
    }
        
	/**
	 * Set add/edit form fields dimensions (width,height)
	 *
	 * @param string Field name
	 * @param string Dimensions in the next format: width,height
	 * ...
	 * @return object Self object
	 */	
    public function setFormFieldsDimensions()
    {
        $arg_list = func_get_args();
        $form_fields_dimensions = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
            if(!($i&1)) 
                $form_fields_dimensions[$arg_list[$i]] = $arg_list[$i+1];
        $this->form_fields_dimensions = $form_fields_dimensions;
        return $this;		
    }
    
    public function setIDs()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->ids[] = $arg;
        return $this;
    }
    
    public function mapFields()
    {
        $arg_list = func_get_args();
        $mapped_fields = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
			if(!($i&1)) 
				$mapped_fields[$arg_list[$i]] = $arg_list[$i+1];
        $this->mapped_fields = $mapped_fields;        
        return $this;
    }
    
    public function restrict()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->restrictions[] = strtolower($arg);
        return $this;
    }
	
	// Fields which will be hidden in the edit window
    public function hiddenEditFields()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->hidden_edit_fields[] = strtolower($arg);
        return $this;
    }	
	
	// Fields which will be disabled in the edit window
    public function disabledEditFields()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->disabled_edit_fields[] = strtolower($arg);
        return $this;
    }
    
	// Fields which will be disabled in the output table
    public function disabledTableFields()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->disabled_table_fields[] = strtolower($arg);
        return $this;
    }    
    
    // Event before save
    public function beforeSave($method_path, $override_orig_save = false)
    {
        $this->before_save_method_path = $method_path;
        $this->override_orig_save = (int)$override_orig_save;
        return $this;
    }
    // Event before save
    public function afterSave($method_path)
    {
        $this->after_save_method_path = $method_path;
        return $this;
    }
	
	public function afterLoad($method_path)
    {
        $this->after_load_method_path = $method_path;
        return $this;
    }	
	
	public function afterDelete($method_path)
    {
        $this->after_delete_method_path = $method_path;
        return $this;
    }

    public function setEditor(){

        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->add_editor_list[] = $arg;

        return $this;
    }
	
        
	/**
	 * Set an additional form when the specified field is changed
	 *
	 * @param string Field name (it must have the ID structure)
	 * @param string Table name (of the specified field)
	 * @return object Self object
	 */		
    public function setForm($field_name, $table_name)
	{
		$this->additional_form_field = $field_name;
		$this->additional_form_table = $table_name;

        return $this;
    }
	
	/**
	 * Enalbe password mode for a field(s)
	 *
	 * @param string Field name
	 * @param string Encrypt type (md5,...)
	 * ...
	 * @return object Self object
	 */	
	public function mapPasswords()
    {
        $arg_list = func_get_args();
        $mapped_passwords = array();
        $cnt = count($arg_list);
        for ($i=0; $i<$cnt-1; $i++) 
            if(!($i&1)) 
                $mapped_passwords[$arg_list[$i]] = $arg_list[$i+1];
        $this->mapped_passwords = $mapped_passwords;
        return $this;
    }	
    
	/**
	 * Get data of the result of the executed query
	 *
	 * @param string Data type (json, xml, ...)
	 * ...
	 * @return object Text content or data source
	 */	

    public function execute($data_type = 'crud')
    {        
        $data['query'] = $this->query;
        $data['tables'] = $this->tables;
        $data['field_names'] = $this->field_names;
        $data['order'] = $this->order;
        $data['search'] = $this->search;
        $data['ids'] = $this->ids;
        $data['mapped_fields'] = $this->mapped_fields;
        $data['before_save_method_path'] = $this->before_save_method_path;
        $data['after_save_method_path'] = $this->after_save_method_path;
        $data['after_load_method_path'] = $this->after_load_method_path;
        $data['after_delete_method_path'] = $this->after_delete_method_path;
        $data['override_orig_save'] = $this->override_orig_save;
        $data['name'] = $this->name;
        $data['where'] = $this->where;
        $data['restrictions'] = $this->restrictions;
        $data['hidden_edit_fields'] = $this->hidden_edit_fields;
        $data['disabled_edit_fields'] = $this->disabled_edit_fields;
        $data['disabled_table_fields'] = $this->disabled_table_fields;
        $data['add_editor_list'] = $this->add_editor_list;
        $data['add_lq_button'] = $this->add_lq_button;
        $data['titles'] = $this->titles;
        $data['mapped_values'] = $this->mapped_values;
        $data['mapped_values_f'] = $this->mapped_values_f;
        $data['mapped_parents'] = $this->mapped_parents;
        $data['mapped_passwords'] = $this->mapped_passwords;
        $data['types'] = $this->types;
        $data['totals'] = $this->totals;
        $data['links'] = $this->links;
        $data['buttons'] = $this->buttons;
        $data['js_handlers'] = $this->js_handlers;
        $data['format_rules'] = $this->format_rules;
        $data['form_fields_dimensions'] = $this->form_fields_dimensions;
        $data['mapped_search'] = $this->mapped_search;
        $data['mapped_field_inputs'] = $this->mapped_field_inputs;
        $data['translations'] = $this->translations;
        $data['uploader_object_type'] = $this->uploader_object_type;
        $data['crud_parent_id'] = $this->crud_parent_id;
        $data['crud_resource_types'] = $this->crud_resource_types;
        $data['unique_fields'] = $this->unique_fields;
        $data['additional_form_field'] = $this->additional_form_field;
        $data['additional_form_table'] = $this->additional_form_table;
        $data['manual_search_format'] = $this->manual_search_format;
        switch ($data_type)
        {
            case 'json':
                $data['query'] = base64_encode($data['query']);
                $data['mapped_values_f'] = json_encode($data['mapped_values_f']);
                $data['search'] = json_encode($data['search']);
                $data['titles'] = json_encode($data['titles']);
                $data['mapped_values'] = json_encode($data['mapped_values']);
                $data['types'] = json_encode($data['types']);
                $data['totals'] = json_encode($data['totals']);
                $data['links'] = json_encode($data['links']);
                $data['buttons'] = json_encode($data['buttons']);
                $data['js_handlers'] = json_encode($data['js_handlers']);
                $data['format_rules'] = json_encode($data['format_rules']);
                $data['form_fields_dimensions'] = json_encode($data['form_fields_dimensions']);
                $data['ids'] = json_encode($data['ids']);
                $data['mapped_parents'] = json_encode($data['mapped_parents']);
                $data['mapped_fields'] = json_encode($data['mapped_fields']);
                $data['mapped_search'] = json_encode($data['mapped_search']);
                $data['mapped_field_inputs'] = json_encode($data['mapped_field_inputs']);
                $data['mapped_passwords'] = json_encode($data['mapped_passwords']);
                $data['restrictions'] = json_encode($data['restrictions']);
                $data['hidden_edit_fields'] = json_encode($data['hidden_edit_fields']);
                $data['disabled_edit_fields'] = json_encode($data['disabled_edit_fields']);
                $data['disabled_table_fields'] = json_encode($data['disabled_table_fields']);
                $data['translations'] = json_encode($data['translations']);
                $data['add_editor_list'] = json_encode($data['add_editor_list']);
                $data['add_lq_button'] = json_encode($data['add_lq_button']);
                $data['crud_resource_types'] = json_encode($data['crud_resource_types']);
                $data['unique_fields'] = json_encode($data['unique_fields']);
                $data['manual_search_format'] = json_encode($data['manual_search_format']);

                $data = Loader::gi()->callModule('POST', 'crud/load', array('crud_params_form'=>json_encode($data), 'crud_search_form'=>'[]'));
                $data['body'] = $data['rows'];
            break;
            default:
                $data['controller_name'] = 'crud';        
                $data['action_name'] = 'execute';		
                if ($this->query !== '' || $this->tables !== '')
                {
                    $data = Loader::gi()->getController($data);			
                }
                else
                    throw new Exception('No query or tables specified');        
                    
            break;
        }
        
        return $data['body'];
    }

    public function addEasyLQ(){

        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->add_lq_button[] = $arg;

        return $this;
    }
}
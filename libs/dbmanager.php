<?php
/**
 * @package    DB
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class DBManager 
{
	private $db;
	private $tables = array();
	private $fields = array();
	private $values = array();
	private $where = '';
	private $limit = '';
	private $order = '';
	private $group = '';
	
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * @description Sets tables
	 */		
	public function tables()
	{
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->tables[] = $arg;
        return $this;
	}
	
	/**
	 * @description Sets fields
	 */		
	public function fields()
	{
        $arg_list = func_get_args();
        foreach ($arg_list as $arg)
            $this->fields[] = $arg;		
        return $this;
	}	
	
	/**
	 * @description Sets values
	 */		
	public function values($values)
	{
		$this->values = $values;		
        return $this;
	}	
	
	/**
	 * @var $where string
	 * @description Sets where condition (unprotected from external injections, should be used for internal purposes only)
	 */		
	public function where($where)
	{
        $this->where = $where;
        return $this;
	}		
	
	/**
	 * @var $order string
	 * @description Sets orders
	 */		
	public function order($order)
	{
        $this->order = $order;
        return $this;
	}	
	
	/**
	 * @var $group string
	 * @description Sets groups
	 */		
	public function group($group)
	{
        $this->group = $group;
        return $this;
	}	

	/**
	 * @var $limit string
	 * @description Sets limits
	 */		
	public function limit($limit)
	{
        $this->limit = $limit;
        return $this;
	}	
	
	/**
	 * Flushes all parameters
	 */		
	private function flush()
	{
		$this->tables = array();
		$this->fields = array();
		$this->values = array();
		$this->where = '';
		$this->limit = '';
		$this->order = '';
		$this->group = '';
	}
	
	/**
	 * @var $debug smallint
	 * @description Collects tables, fields and other given parameters and pass them to the database adapter
	 */	
	public function select($debug = -1)
	{
		$conditions['fields'] = $this->fields;
		$conditions['where'] = $this->where;
		$conditions['order'] = $this->order;
		$conditions['group'] = $this->group;
		$conditions['limit'] = $this->limit;
		foreach ($this->tables as $table)
		{			
			if ($first_space = strpos($table, ' '))
				$table = substr($table, 0, $first_space);
			 $arhlog_tables[] = $table;
		}
		Arhlog::log($arhlog_tables, 'select', 's', $conditions);				// Arhlog data
		$out = $this->db->select($this->tables, $conditions, $debug);
		$this->flush();
		return $out;
	}
	
	// Count of rows in table
	public function count($debug = -1)
	{
		$out = $this->db->count($this->tables, $this->where);
		$this->flush();		
		return $out;
	}		

	public function getLastID()
	{
		$last_id = $this->db->getLastID();
		return $last_id;
	}
	
	public function getScalar($debug = -1)
	{
		$this->limit = '1';
		$scalar = $this->select();
		if (isset($scalar[0]))
			return $scalar[0];
		else 
			return false;
	}
        
	public function getScalarByQuery($query, $debug = -1)
	{
		$scalar = $this->selectByQuery($query, $debug);
		if (!$scalar) return false;
		return $scalar[0];
	}
        
	// Select and return object
	public function selectByQuery($query, $debug = -1)
	{
		$conditions['query'] = $query;
		Arhlog::log('select_by_query', 'select', 's', $conditions);				// Arhlog data	
		return $this->db->selectByQuery($query, $debug);
	}
	
	// Process a simple query
	public function query($query, $debug = -1)
	{
		return $this->db->query($query, $debug);
	}
	
	// Inserting to a table
	public function insert($debug = -1) 
	{
		foreach ($this->tables as $table)
		{			
			if ($first_space = strpos($table, ' '))
				$table = substr($table, 0, $first_space);
			 $arhlog_tables[] = $table;
		}
		Arhlog::log($arhlog_tables, 'insert', 'i', array('values'=>$this->values));				// Arhlog data	
		$out = $this->db->insert(implode(',', $this->tables), $this->values);
		$this->flush();
		return $out;
	}


	// Bulk inserting to a table
	public function bulkInsert($debug = -1) 
	{
		$out = $this->db->bulkInsert(implode(',', $this->tables), $this->values);
		$this->flush();
		return $out;
	}
	
	// Updating a table
	public function update($debug = -1) 
	{
		foreach ($this->tables as $table)
		{			
			if ($first_space = strpos($table, ' '))
				$table = substr($table, 0, $first_space);
			 $arhlog_tables[] = $table;
		}
		Arhlog::log($arhlog_tables, 'update', 'u', array('values'=>$this->values, 'where'=>$this->where));				// Arhlog data		
		$out = $this->db->update(implode(',', $this->tables), $this->values, $this->where);
		$this->flush();		
		return $out;
	}
	
	// Deleting form a table	
	public function delete($debug = -1) 
	{
		foreach ($this->tables as $table)
		{			
			if ($first_space = strpos($table, ' '))
				$table = substr($table, 0, $first_space);
			 $arhlog_tables[] = $table;
		}
		Arhlog::log($arhlog_tables, 'delete', 'd', array('where'=>$this->where));				// Arhlog data		
		$out = $this->db->delete(implode(',', $this->tables), $this->where, $debug);	
		$this->flush();	
		return $out;		
	}
	
	// Get link
	public function getLink()
	{
		return $this->db->getLink();
	}
	
	// Get fields
	public function getFields()
	{
		return $this->db->getFields();
	}		
}
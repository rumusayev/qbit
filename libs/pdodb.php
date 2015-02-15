<?php
class PDODB
{
	protected static $instance;
	private $link;
	private $parsed;	
	
	private function __construct()
	{	
		$dbhost = Backstage::gi()->db_host;
		$dbname = Backstage::gi()->db_name;
		$dbuser = Backstage::gi()->db_user;
		$dbpass = Backstage::gi()->db_pass;
	
		$connect_str = 'mysql:host='.$dbhost.';port=3306;dbname='.$dbname.';charset=utf8';
		$this->link = new PDO($connect_str, $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$this->link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$error_array = $this->link->errorInfo();
		if($this->link->errorCode() != 0000)
			throw new QException(array('ER-00019', $error_array[2]));
	}
	
	public function __destruct()
	{
		$this->link = null;
	}
	
	private function __clone() {}
	
	private function __wakeup() {}

    public static function gi()
	{
		if (is_null(self::$instance)) 
		{
			self::$instance = new PDODB;
		}
		return self::$instance;
	}
	public function getLink()
	{
		return $this->link;
	}
	
	function select($tables, $conditions, $debug = -1)
	{
		$params_arr = array();
		$where = '';
		$order_by = '';
		$group_by = '';
		$limit = '';
		if (isset($conditions['where']) && $conditions['where'] != '') $where = 'WHERE '.$conditions['where'];
		if (isset($conditions['order']) && $conditions['order'] != '') $order_by = 'ORDER BY '.$conditions['order'];
		if (isset($conditions['group']) && $conditions['group'] != '') $group_by = 'GROUP BY '.$conditions['group'];
		if (isset($conditions['limit']) && $conditions['limit'] != '') $limit = 'LIMIT '.$conditions['limit'];
/*
		foreach($where as $where_field => $where_value) 
		{
			$where_arr[] = "$where_field = '?'";
			$params_arr[] = $where_value;
		}
*/		
		$query = "SELECT ".implode(",",$conditions['fields'])." FROM ".implode(",",(array)$tables)." $where $order_by $group_by $limit";
		$this->parsed = $this->link->prepare($query);
		try
		{
			$result = $this->parsed->execute($params_arr);
        }
		catch(Exception $e)
		{
			throw new QException(array('ER-00020', $query, $e->getMessage()));		
		}
		if(!$result)
			throw new QException(array('ER-00020', $query, ''));
		
		$rows = array();
		while ($row = $this->parsed->fetchObject())
			$rows[] = (object)$row;

		return $rows;
	}

	function selectByQuery($query, $debug = -1)
	{
		$params_arr = array();		
		try
		{
			$this->parsed = $this->link->prepare($query);
			$result = $this->parsed->execute($params_arr);
        }
		catch(Exception $e)
		{
			throw new QException(array('ER-00020', $query, $e->getMessage()));		
		}
		if(!$result)
			throw new QException(array('ER-00020', $query, ''));
			
		$rows = array();
		while ($row = $this->parsed->fetchObject())
			$rows[] = (object)$row;

		return $rows;
	}
	
		
	function create($query)
	{
		if(!($result = $this->link->query($query))) 
		{ 
			echo "SQL connection error: ";
			echo $query;
			exit();
		}
		return $this->link->insert_id;
	}
	
	function count($tables, $where)
	{
		if ($where && $where != '')
			 $where = "WHERE $where";
		$query = "SELECT COUNT(*) FROM ".implode(',', $tables)." $where";
		try
		{
			$result = $this->link->query($query);
        }
		catch(Exception $e)
		{
			throw new QException(array('ER-00020', $query, $e->getMessage()));		
		}
		if(!$result)
			throw new QException(array('ER-00020', $query, ''));

		$counts = $result->fetch();
		return $counts[0];
	}
	
	public function insert($table_name, $data)
	{
		$sql = "SHOW COLUMNS FROM ".$table_name;
		try
		{
			$result = $this->link->query($sql);
        }
		catch(Exception $e)
		{
			throw new QException(array('ER-00020', $sql, $e->getMessage()));		
		}		
		
		while ($row = $result->fetchObject()) 
		{
			$column_arr[(String)$row->Field] = $row->Field;
			$type_arr[(String)$row->Field] = $row->Type;
		}
		foreach($data as $curr_field => $curr_value) 
		{
			if( in_array($curr_field, $column_arr) && $curr_field!='id' ) 
			{
				$fields_arr[] = $curr_field;
				if ($type_arr[(String)$curr_field] === 'date' && $curr_value == '')			
					$values_arr[] = "NULL";
				else
				{
					$values_arr[] = '?';
					$params_arr[] = $curr_value;
				}
			}
		}
		$query = "INSERT INTO ".$table_name." (".implode(",",$fields_arr).") VALUES (".implode(",",$values_arr).")";
		$this->parsed = $this->link->prepare($query);
		try
		{
			$result = $this->parsed->execute($params_arr);
        }
		catch(Exception $e)
		{
			throw new QException(array('ER-00020', $this->interpolateQuery($query, $params_arr), $e->getMessage()));		
		}
		if(!$result)
			throw new QException(array('ER-00020', $this->interpolateQuery($query, $params_arr), ''));
			
		return $result;
	}

	public function update($table_name, $data, $where) 
	{
		if (isset($data['id'])) 
		{
			$row_id = intval($data['id']);
			unset($data['id']);
			$where = "id = '".$row_id."'";
		} 
		elseif (!$where || empty($where))
			return false;
                $types_arr = array('bigint','int','smallint','tinyint','float');

		$sql = "SHOW COLUMNS FROM ".$table_name;
		$result = $this->link->query($sql);
		while ($row = $result->fetchObject())
		{
			$column_arr[] = $row->Field;
			$column_arr[(String)$row->Field] = $row->Field;
			$type_arr[(String)$row->Field] = $row->Type;			
		}
		foreach($data as $curr_field => $curr_value) 
		{
			if(in_array($curr_field, $column_arr) && $curr_field!='id') 
			{
                                $brace_pos = strpos($type_arr[(String)$curr_field], '(');
                                if (in_array($brace_pos?substr($type_arr[(String)$curr_field],0, $brace_pos):$type_arr[(String)$curr_field], $types_arr) && strstr($curr_value,'++'))
                                {
                                    $curr_value = str_replace('++','',$curr_value);
                                    $fields_arr[] = $curr_field."=$curr_field + ?";
                                }
                                else
                                    $fields_arr[] = $curr_field."=?";
				
				if (($type_arr[(String)$curr_field] === 'date' || $type_arr[(String)$curr_field] === 'datetime') && $curr_value == '')
					$curr_value = "NULL";
				elseif ((strpos($type_arr[(String)$curr_field],'int') || $type_arr[(String)$curr_field] === 'float') && $curr_value == '')
					$curr_value = 0;				
				$params_arr[] = $curr_value;	

			}
		}
		$query = "UPDATE ".$table_name." SET ".join(",",$fields_arr)." WHERE ".$where;
		$this->parsed = $this->link->prepare($query);
		
		try
		{
			$result = $this->parsed->execute($params_arr);
        }
		catch(Exception $e)
		{
			throw new QException(array('ER-00020', $this->interpolateQuery($query, $params_arr), $e->getMessage()));		
		}
		if(!$result)
			throw new QException(array('ER-00020', $this->interpolateQuery($query, $params_arr), ''));

		return $result;
	}       
        
	function delete($table, $where = '', $debug = -1)
	{

		if (!empty($where) && $where != '') 
			$where = 'WHERE '.$where;
		$query = "DELETE FROM ".$table." $where";

		try
		{
			$result =$this->link->query($query);
        }
		catch(Exception $e)
		{
			throw new QException(array('ER-00020', $query, $e->getMessage()));		
		}
		if(!$result)
			throw new QException(array('ER-00020', $query, ''));

		return $result;
	}
	
	function getLastID()
	{
		return $this->link->lastInsertId();
	}
	
	/**
	 * Replaces any parameter placeholders in a query with the value of that
	 * parameter. Useful for debugging. Assumes anonymous parameters from 
	 * $params are in the same order as specified in $query
	 *
	 * @param string $query The sql query with parameter placeholders
	 * @param array $params The array of substitution parameters
	 * @return string The interpolated query
	 */
	private function interpolateQuery($query, $params) 
	{
		$keys = array();
		$values = $params;

		# build a regular expression for each parameter
		foreach ($params as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/:'.$key.'/';
			} else {
				$keys[] = '/[?]/';
			}

			if (is_array($value))
				$values[$key] = implode(',', $value);

			if (is_null($value))
				$values[$key] = 'NULL';
		}
		// Walk the array to see if we can add single-quotes to strings
		array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".$v."\'";'));

		$query = preg_replace($keys, $values, $query, 1, $count);

		return $query;
	}
	
	function getFields()
	{
		$cols_num = $this->parsed->columnCount();
		$fields = array();
		
		for ($i = 0; $i < $cols_num; $i++) 
		{
			$column = $this->parsed->getColumnMeta($i);
			$fields[$i]['name'] = $column['name'];
			@$fields[$i]['type'] = $column['native_type'];
			$fields[$i]['size'] = $column['len'];
			$fields[$i]['table'] = $column['table'];
		}
		return $fields;
	}
}
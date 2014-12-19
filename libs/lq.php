<?php

/**
 * @package    LQ
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class LQ
{	
	private $data = array();
	private $lq_num = 1;
	private $lqs = array();
	private $level = 1;
        
	function __construct($data) 
	{
		$this->lq_num = 1;
		$this->lqs = array();
		$this->level = 1;
		$this->data = $data;            
	}
        
	public function flush($data)
	{
		$this->lq_num = 1;
		$this->lqs = array();
		$this->level = 1;
		$this->data = $data;
	}
	
	/**
	 * @description		Search for LQs and parse them without calling a module
	 */
	public function parseData()
	{
		while ($this->lq_num > 0 && $this->level < 10)
		{
			$this->lqs = $this->findLQ($this->data['query']);
				// Sort lqs
			foreach ($this->lqs as $key=>$lq)
				$this->lqs[$key] = $this->sortData($lq); // Sort LQ
				
			$this->data['lqs'] = $this->lqs;
			$this->level++;
		}
	}
		
	/**
	 * @description		Handles data and processes it for lqs inside it
	 */
	public function handleData()
	{
		while ($this->lq_num > 0 && $this->level < 10)
		{
			$this->lqs = $this->processLQ($this->data['query']);
				// Sort lqs
			foreach ($this->lqs as $key=>$lq)
				$this->lqs[$key] = $this->sortData($lq); // Sort LQ
				// Parse lqs
			foreach ($this->lqs as $lq)
				$this->parseLQ($lq);
			$this->level++;
		}
	}
	
	/**
	 * @description		Searches lqs inside a specific query (text) and returns a needed one
	 * @in			A qiven query (text)
	 */
	private function processLQ($in) 
	{
		// @ - variable passed through the url
		// $ - constants
		$lqs = array();
		$this->lq_num = preg_match_all("/\[\[([^\^\[^\]]+)\]\]\?\[\[([^\^\[^\]]+)\]\]\:\[\[([^\^\[^\]]+)\]\]|\[\[([^\^\[^\]]+)\]\]/i", $in, $matches, PREG_SET_ORDER);
		foreach ($matches as $match)
		{
				// If not empty this is a construction like [[condition]]?[[true lq]]:[[false lq]]
			if (!empty($match[1]))
			{
				$or_booleans = array();
					// Condition devided by ORs
				$or_condition_parts = preg_split("/[\s]+or[\s]+/i", $match[1]);
				foreach ($or_condition_parts as $key=>$or_condition_part)
				{
					$and_boolean = true;
						// Condition devided by ANDs
					$and_condition_parts = preg_split("/[\s]+and[\s]+/i", $or_condition_part);
					foreach ($and_condition_parts as $and_condition_part)
					{
						preg_match_all("/([^\s^\[\[^\]\]^=^\>^\<]+)[\s]*([=\>\<]{1,2})[\s]*([\"\'][^\[^\]]+[\"\'])|([^\s^\[^\]^=^\>^\<]+)[\s]*([=\>\<]{1,2})[\s]*([^\s^\[^\]^=^\>^\<]+)|([^\s^\[^\]^=]+)/i", $and_condition_part, $condition_matches, PREG_SET_ORDER);
						foreach ($condition_matches as $condition_match)
						{
							if (empty($condition_match[1]))
								array_splice($condition_match,1,3);
						}
							// We should erase all signs before the variables

						$variable = str_replace(array('@','$'),'',$condition_match[1]);
							// if $ - we should load a constant
						if (strstr($condition_match[1], '$'))
							$variable = Backstage::gi()->$variable;
							// if @ - we should load it from the url string
						if (strstr($condition_match[1], '@'))
							$variable = isset($this->data['request']->parameters[$variable])?$this->data['request']->parameters[$variable]:false;	
							// If the variable is not found from a url the result of a suboperand will be taken as false
						if ($variable === false)
						{
							$and_boolean = $and_boolean && $variable;
							continue;
						}
						switch ($condition_match[2])
						{
							case '=':
								if (strstr($condition_match[3],'"'))
									$and_boolean = $and_boolean && '"'.$variable.'"'==$condition_match[3];
								else
									$and_boolean = $and_boolean && $variable==$condition_match[3];
									
							break;
							case '>':
								if (strstr($condition_match[3],'"'))
									$and_boolean = $and_boolean && '"'.$variable.'"'>$condition_match[3];
								else
									$and_boolean = $and_boolean && $variable>$condition_match[3];
							break;
							case '<':
								if (strstr($condition_match[3],'"'))
									$and_boolean = $and_boolean && '"'.$variable.'"'<$condition_match[3];
								else
									$and_boolean = $and_boolean && $variable<$condition_match[3];
							break;
							case '>=':
								if (strstr($condition_match[3],'"'))
									$and_boolean = $and_boolean && '"'.$variable.'"'>=$condition_match[3];
								else
									$and_boolean = $and_boolean && $variable>=$condition_match[3];
							break;
							case '<=':
								if (strstr($condition_match[3],'"'))
									$and_boolean = $and_boolean && '"'.$variable.'"'<=$condition_match[3];
								else
									$and_boolean = $and_boolean && $variable<=$condition_match[3];
							break;
						}
					}
					$or_booleans[$key] = $and_boolean;
				}
				$result_boolean = (bool)array_sum($or_booleans);
				
				if ($result_boolean)
					$final_lq = $match[2];
				else
					$final_lq = $match[3];
			}
			else
				$final_lq = $match[4];
			
			preg_match_all("/([^\s^\[^\]]+)=([\"\'][^\[^\]]+[\"\'])|([^\s^\[^\]]+)=([^\s^\[^\]]+)|([^\s^\[^\]^=]+)/i", $final_lq, $final_lq_matches, PREG_SET_ORDER);
			$final_lq_matches = array_merge(array($match[0]), $final_lq_matches);
			$lqs[] = $final_lq_matches;
		}
		
		return $lqs;
	}
	
	/**
	 * @description		Sorts data inside a specific lq
	 * @lq			A lq
	 */
	private function sortData($lq) 
	{	
		$sorted = array();
		foreach ($lq as $key=>$lq_part) 
		{
			if ($key === 0) 
			{
				$sorted['lq'] = $lq_part;
				continue;
			}
			if (isset($lq_part[4]) && empty($lq_part[4]))
				array_splice($lq_part,1,4);
			elseif (isset($lq_part[2]) && empty($lq_part[2]))
				array_splice($lq_part,1,2);
			
			switch ($lq_part[0]) 
			{
				case "module": 
				case "container": 
				case "translation": 
				case "constant":
					$sorted['lq_type'] = $lq_part[0];
					continue 2;
				break;
			}
			
			$first_symb = substr($lq_part[0], 0, 1);
			if (in_array($first_symb, array('$', '@')))
			{
					// @var or $var cases
				$variable = str_replace(array('@','$'),'',$lq_part[0]);
					// if $ - we should load a constant
				if ($first_symb === '$')
					$sorted['lq_type'] = 'backstage';
					// if @ - we should load it from the url string
				if ($first_symb === '@')
					$sorted['lq_type'] = 'parameter';
				$sorted['value'] = $variable;
			}
			else
				$sorted[$lq_part[1]] = $lq_part[2];
		}

		return $sorted;
	}
	
	/**
	 * @description		Searches lqs inside a specific query (text) 
	 * @in			A qiven query (text)
	 */	

	private function findLQ($in) 
	{
		// @ - variable passed through the url
		// $ - constants
		$lqs = array();
		$this->lq_num = preg_match_all("/\[\[([^\^\[^\]]+)\]\]\?\[\[([^\^\[^\]]+)\]\]\:\[\[([^\^\[^\]]+)\]\]|\[\[([^\^\[^\]]+)\]\]/i", $in, $matches, PREG_SET_ORDER);
		foreach ($matches as $match)
		{
				// If not empty this is a construction like [[condition]]?[[true lq]]:[[false lq]]
			if (!empty($match[1]))
			{
				preg_match_all("/([^\s^\[^\]]+)=([\"\'][^\[^\]]+[\"\'])|([^\s^\[^\]]+)=([^\s^\[^\]]+)|([^\s^\[^\]^=]+)/i", $match[2], $lq_matches, PREG_SET_ORDER);
				$lq_matches = array_merge(array($match[2]), $lq_matches);
				$lqs[] = $lq_matches;
				preg_match_all("/([^\s^\[^\]]+)=([\"\'][^\[^\]]+[\"\'])|([^\s^\[^\]]+)=([^\s^\[^\]]+)|([^\s^\[^\]^=]+)/i", $match[3], $lq_matches, PREG_SET_ORDER);
				$lq_matches = array_merge(array($match[3]), $lq_matches);
				$lqs[] = $lq_matches;
			}
			else
			{
				preg_match_all("/([^\s^\[^\]]+)=([\"\'][^\[^\]]+[\"\'])|([^\s^\[^\]]+)=([^\s^\[^\]]+)|([^\s^\[^\]^=]+)/i", $match[4], $lq_matches, PREG_SET_ORDER);
				$lq_matches = array_merge(array($match[4]), $lq_matches);
				$lqs[] = $lq_matches;
			}
		}
		
		return $lqs;
	}	
	
	/**
	 * @description		Parses ready lqs
	 * @lq		A ready lq
	 */	
	private function parseLQ($lq)
	{
		switch ($lq['lq_type']) 
		{
			case "container":
				// Depricated
			break;			
			case "backstage":
				$value = $lq['value'];
				$value = Backstage::gi()->$value;
				$this->data['query'] = str_replace($lq['lq'], $value, $this->data['query']);
			break;			
			case "parameter":
				$value = $lq['value'];			
				if (!isset($this->data['request']->parameters[$value]))
					// Let's not through an error
					//throw new QException(array('ER-00024', $value));
					$value = '';
				else
					$value = $this->data['request']->parameters[$value];	
				$this->data['query'] = str_replace($lq['lq'], $value, $this->data['query']);
			break;
			case "module":
				$module_data = array();
				$parameters = $this->data['request']->parameters;
				$parameters['lq'] = $lq;
				unset($parameters['lq']['lq']);
				unset($parameters['lq']['lq_type']);
					// Type should be specified
				if (!isset($lq['type']))
					throw new QException(array('ER-00023', 'type', $lq['lq']));
		         
	         	$module_data = Loader::gi()->callModule('GET', $lq['type'].'/'.$lq['action'], $parameters);
				$out = $module_data['body'];
				if (array_key_exists('container', $lq) && !empty($lq['container']))
				{
					$cont_lq = $this->findLQByName('container', $lq['container']);
					$this->data['query'] = str_replace($cont_lq['lq'], $out, $this->data['query']);
					$this->data['query'] = str_replace($lq['lq'], '', $this->data['query']);
				}
				else
				{
					$this->data['query'] = str_replace($lq['lq'], $out, $this->data['query']);
				}			
			break;
			case "translation": 
				$this->data['query'] = str_replace($lq['lq'], Translations::gi()->{$lq['name']}, $this->data['query']);				
			break;
			case "constant":
				$this->data['query'] = str_replace($lq['lq'], Backstage::gi()->{$lq['name']}, $this->data['query']);
			break;
			case "parameters": 
				$this->data['query'] = str_replace($lq[0], $this->data['parameters'][$lq[3]], $this->data['query']);
			break;
		}
	}
     
	/**
	 * @description		Finds lq by its type and name
	 * @lq_type		Type of a lq
	 * @lq_name		Name of a lq
	 */		 
	public function findLQByName($lq_type, $lq_name)
	{
		foreach ($this->lqs as $lq)
			if ($lq['lq_type'] === $lq_type && $lq['name'] === $lq_name)
				return $lq;
	}
	
	public function getdata() 
	{
		return $this->data;
	}
}
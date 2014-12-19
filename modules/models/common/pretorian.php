<?php

/**
 * @package    filterChain
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
class mPretorian extends model
{
	function check()
	{
		$this->data['check_count'] = $this->dbmanager->tables('(select * from (select id from `'.Backstage::gi()->db_table_prefix.'grants` where UPPER(resource_name) = UPPER("'.$this->data['resource_name'].'") '.
		'and grant_type in ("'.implode('","',(array)$this->data['grant_types']).'")) aa where aa.id IN '. 
		'(select c.grant_id from '.Backstage::gi()->db_table_prefix.'users a, '.Backstage::gi()->db_table_prefix.'user_grants b, '.Backstage::gi()->db_table_prefix.'role_grants c where a.login = "'.Backstage::gi()->user->login.'" and a.id = b.user_id '.
		'and b.role_id = c.role_id '.
		'union all '.
		'select b.grant_id from '.Backstage::gi()->db_table_prefix.'users a, '.Backstage::gi()->db_table_prefix.'user_grants b where a.login = "'.Backstage::gi()->user->login.'" and a.id = b.user_id and b.grant_id is not null '.
		'union all '.
		'select b.grant_id from '.Backstage::gi()->db_table_prefix.'roles a, '.Backstage::gi()->db_table_prefix.'role_grants b where a.role_name = "Public" and a.id = b.role_id)'.
		'union all '.
		'select * from (select id from `'.Backstage::gi()->db_table_prefix.'grants` where resource_id = '.$this->data['resource_id'].' and resource_type = "'.$this->data['resource_name'].'"'.
		'and grant_type in ("'.implode('","',(array)$this->data['grant_types']).'")) aa where aa.id IN '. 
		'(select c.grant_id from '.Backstage::gi()->db_table_prefix.'users a, '.Backstage::gi()->db_table_prefix.'user_grants b, '.Backstage::gi()->db_table_prefix.'role_grants c where a.login = "'.Backstage::gi()->user->login.'" and a.id = b.user_id '.
		'and b.role_id = c.role_id '.
		'union all '.
		'select b.grant_id from '.Backstage::gi()->db_table_prefix.'users a, '.Backstage::gi()->db_table_prefix.'user_grants b where a.login = "'.Backstage::gi()->user->login.'" and a.id = b.user_id and b.grant_id is not null '.
		'union all '.
		'select b.grant_id from '.Backstage::gi()->db_table_prefix.'roles a, '.Backstage::gi()->db_table_prefix.'role_grants b where a.role_name = "Public" and a.id = b.role_id)) bbb'
		)
		->count();
		return $this->data;
	}	
}
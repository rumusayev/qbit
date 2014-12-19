<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cAuth extends controller
{
    public function get()
	{
        $this->data['view_name'] = 'loginform';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
	}
	
	private function generateCookie($login, $expiration) 
	{
		$key = hash_hmac('md5', $login.$expiration, 'blabla');
		$hash = hash_hmac('md5', $login.$expiration, $key);
		$cookie = $login.'|'.$expiration.'|'.$hash;
		return $cookie;
	}
		
    public function login()
    {
		$this->data = Loader::gi()->getModel($this->data);
		if (!$this->data['item'])
		{
			$this->data['body'] = 0;
			return $this->data;
		}
		
		$expiration = time() + 172800;
		$cookie = $this->generateCookie($this->data['item']->login, $expiration);
		
		Backstage::gi()->user = $this->data['item'];
		Backstage::gi()->user->password = '';
			
		$this->data['body'] = $cookie;
        return $this->data;
    }

    public function logout()
    {
		Backstage::gi()->user->id = 0;
		Backstage::gi()->user->login = '';
		Backstage::gi()->user->password = '';
		
		header("Location: ".Backstage::gi()->portal_url.'admin', true, 301);
		exit();
        return $this->data;
    }
}
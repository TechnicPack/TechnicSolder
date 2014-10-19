<?php

class User extends Eloquent {
	public $timestamps = true;

	public function permission()
	{
		return $this->has_one('UserPermission');
	}

	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}
}
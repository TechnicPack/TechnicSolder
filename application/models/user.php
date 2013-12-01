<?php

class User extends Eloquent {
	public static $timestamps = true;

	public function permission()
	{
		return $this->has_one('UserPermission');
	}
}
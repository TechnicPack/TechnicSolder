<?php

class UserPermission extends Eloquent {
	public static $table = 'user_permissions';
	public static $timestamps = true;

	public function user()
	{
		return $this->belongs_to('User');
	}

	public function set_modpacks($modpack_array)
	{
		if (is_array($modpack_array))
		{
			$this->set_attribute('modpacks', implode(',',$modpack_array));
		} else {
			$this->set_attribute('modpacks', null);
		}
		
	}

	public function get_modpacks()
	{
		return explode(',', $this->get_attribute('modpacks'));
	}
}
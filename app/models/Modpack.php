<?php

class Modpack extends Eloquent {
	public $timestamps = true;

	public function builds()
	{
		return $this->hasMany('Build');
	}

	public function clients()
	{
		return $this->belongsToMany('Client');
	}
}

?>
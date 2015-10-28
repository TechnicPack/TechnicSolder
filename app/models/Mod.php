<?php

class Mod extends Eloquent {
	public $timestamps = true;

	public function versions()
	{
		return $this->hasMany('Modversion');
	}

	public function tags()
	{
		return $this->belongsToMany('Tag');
	}
}

?>
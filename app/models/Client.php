<?php

class Client extends Eloquent {
	public $timestamps = true;

	public function modpacks()
	{
		return $this->belongsToMany('Modpack')->withTimestamps();
	}

}
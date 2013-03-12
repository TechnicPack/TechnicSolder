<?php

class Modversion extends Eloquent {
	public $timestamps = true;

	public function mod()
	{
		return $this->belongsTo('Mod');
	}

	public function builds()
	{
		return $this->belongsToMany('Build');
	}
}
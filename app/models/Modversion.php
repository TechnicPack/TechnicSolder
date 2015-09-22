<?php

class Modversion extends Eloquent {
	protected $table = 'modversions';
	public $timestamps = true;

	public function mod()
	{
		return $this->belongsTo('Mod');
	}

	public function builds()
	{
		return $this->belongsToMany('Build')->withPivot('target')->withTimestamps();
	}
}

?>

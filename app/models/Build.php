<?php

class Build extends Eloquent {
	public $timestamps = true;

	public function modpack()
	{
		return $this->belongsTo('Modpack');
	}

	public function modversions()
	{
		return $this->belongsToMany('Modversion')->withTimestamps();
	}
}

?>

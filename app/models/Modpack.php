<?php

class Modpack extends Eloquent {
	public $timestamps = true;

	public function builds()
	{
		return $this->hasMany('Build');
	}

	public function clients()
	{
		return $this->belongsToMany('Client')->withTimestamps();;
	}

	public function private_builds()
	{
		$private = false;
		foreach($this->builds as $build){
			if($build->private){
				$private = true;
				break;
			}
		}
		return $private;
	}
}

?>
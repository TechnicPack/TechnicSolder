<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Modpack extends Model {
	public $timestamps = true;

	public function builds()
	{
		return $this->hasMany('App\Build');
	}

	public function clients()
	{
		return $this->belongsToMany('App\Client')->withTimestamps();;
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
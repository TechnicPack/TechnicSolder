<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Build extends Model {
	public $timestamps = true;

	public function modpack()
	{
		return $this->belongsTo('App\Modpack');
	}

	public function modversions()
	{
		return $this->belongsToMany('App\Modversion')->withTimestamps();
	}
}
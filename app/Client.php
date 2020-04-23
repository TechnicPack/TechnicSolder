<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model {
	public $timestamps = true;

	public function modpacks()
	{
		return $this->belongsToMany('App\Modpack')->withTimestamps();
	}

}
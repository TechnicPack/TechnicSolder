<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Mod extends Model {
	public $timestamps = true;

	public function versions()
	{
		return $this->hasMany('App\Modversion');
	}
}
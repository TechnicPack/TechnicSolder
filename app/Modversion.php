<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Modversion extends Model {
	protected $table = 'modversions';
	public $timestamps = true;

	public function mod()
	{
		return $this->belongsTo('App\Mod');
	}

	public function builds()
	{
		return $this->belongsToMany('App\Build')->withTimestamps();
	}
	public function humanFilesize($unit = "")
	{
		$size = $this->filesize;
		if( (!$unit && $size >= 1<<30) || $unit == "GB")
			return number_format($size/(1<<30),2)." GB";
		if( (!$unit && $size >= 1<<20) || $unit == "MB")
			return number_format($size/(1<<20),2)." MB";
		if( (!$unit && $size >= 1<<10) || $unit == "KB")
			return number_format($size/(1<<10),2)." KB";
		return number_format($size)." bytes";
	}
}
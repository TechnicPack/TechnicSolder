<?php

class Mod extends Eloquent {
	public static $timestamps = true;

	public function versions()
	{
		return $this->has_many('ModVersion');
	}
}

?>
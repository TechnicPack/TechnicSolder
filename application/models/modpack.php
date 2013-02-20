<?php

class Modpack extends Eloquent {
	public static $timestamps = true;

	public function builds()
	{
		return $this->has_many('Build');
	}
}

?>
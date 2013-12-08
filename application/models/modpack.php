<?php

class Modpack extends Eloquent {
	public static $timestamps = true;

	public function builds()
	{
		return $this->has_many('Build');
	}

	public function clients()
	{
		return $this->has_many_and_belongs_to('Client');
	}
}

?>
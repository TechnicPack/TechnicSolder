<?php

class Build extends Eloquent {
	public static $timestamps = true;

	public function modpack()
	{
		return $this->belongs_to('Modpack');
	}
}

?>
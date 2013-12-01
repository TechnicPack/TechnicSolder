<?php

class Build extends Eloquent {
	public static $timestamps = true;

	public function modpack()
	{
		return $this->belongs_to('Modpack');
	}

	public function modversions()
	{
		return $this->has_many_and_belongs_to('ModVersion');
	}
}

?>
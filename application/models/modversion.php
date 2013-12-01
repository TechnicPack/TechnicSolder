<?php

class ModVersion extends Eloquent {
	public static $timestamps = true;

	public function mod()
	{
		return $this->belongs_to('Mod');
	}

	public function builds()
	{
		return $this->has_many_and_belongs_to('Build');
	}
}

?>
<?php

class Client extends Eloquent {
	public $timestamps = true;

	public function modpacks()
	{
		return $this->has_many_and_belongs_to('Modpack');
	}

}
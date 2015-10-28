<?php

class Tag extends Eloquent {
    public $timestamps = true;

    public function mods()
    {
        return $this->belongsToMany('Mod');
    }
}
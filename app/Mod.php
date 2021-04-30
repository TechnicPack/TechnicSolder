<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mod extends Model
{
    public function versions()
    {
        return $this->hasMany(Modversion::class);
    }
}
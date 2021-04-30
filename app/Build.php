<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    public function modpack()
    {
        return $this->belongsTo(Modpack::class);
    }

    public function modversions()
    {
        return $this->belongsToMany(Modversion::class)->withTimestamps();
    }
}
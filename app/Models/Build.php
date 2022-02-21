<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    protected $guarded = [];

    public function modpack()
    {
        return $this->belongsTo(Modpack::class);
    }

    public function modversions()
    {
        return $this->belongsToMany(Modversion::class)->withTimestamps();
    }
}

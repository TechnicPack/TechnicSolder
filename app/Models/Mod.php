<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mod extends Model
{
    protected $guarded = [];

    public function versions(): HasMany
    {
        return $this->hasMany(Modversion::class);
    }
}

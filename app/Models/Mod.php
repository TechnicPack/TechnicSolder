<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mod extends Model
{
    protected $guarded = [];

    public function versions(): HasMany
    {
        return $this->hasMany(Modversion::class);
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(Modversion::class)->latestOfMany('updated_at');
    }
}

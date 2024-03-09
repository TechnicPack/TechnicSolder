<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    public function modpacks(): BelongsToMany
    {
        return $this->belongsToMany(Modpack::class)->withTimestamps();
    }
}

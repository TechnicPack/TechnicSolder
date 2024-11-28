<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    protected $guarded = [];

    public function modpack(): BelongsTo
    {
        return $this->belongsTo(Modpack::class);
    }

    public function modversions(): BelongsToMany
    {
        return $this->belongsToMany(Modversion::class)->withTimestamps();
    }
}

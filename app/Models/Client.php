<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $uuid
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Modpack> $modpacks
 * @property-read int|null $modpacks_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUuid($value)
 *
 * @mixin \Eloquent
 */
class Client extends Model
{
    protected $fillable = [
        'name',
        'uuid',
    ];

    public function modpacks(): BelongsToMany
    {
        return $this->belongsToMany(Modpack::class)->withTimestamps();
    }
}

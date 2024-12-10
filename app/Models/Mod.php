<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $author
 * @property string|null $link
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $pretty_name
 * @property-read \App\Models\Modversion|null $latestVersion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modversion> $versions
 * @property-read int|null $versions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod wherePrettyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mod whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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

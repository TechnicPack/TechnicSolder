<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $modpack_id
 * @property string $version
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $minecraft
 * @property string|null $forge
 * @property bool $is_published
 * @property bool $private
 * @property string|null $min_java
 * @property int|null $min_memory
 * @property-read \App\Models\Modpack|null $modpack
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modversion> $modversions
 * @property-read int|null $modversions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereForge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereMinJava($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereMinMemory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereMinecraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereModpackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build wherePrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Build whereVersion($value)
 *
 * @mixin \Eloquent
 */
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

    public function isLive(): bool
    {
        return $this->is_published && ! $this->private;
    }
}

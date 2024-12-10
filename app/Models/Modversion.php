<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $mod_id
 * @property string $version
 * @property string $md5
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int|null $filesize
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Build> $builds
 * @property-read int|null $builds_count
 * @property-read mixed $url
 * @property-read \App\Models\Mod|null $mod
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion whereFilesize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion whereMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modversion whereVersion($value)
 *
 * @mixin \Eloquent
 */
class Modversion extends Model
{
    protected $guarded = [];

    public function mod(): BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class)->withTimestamps();
    }

    public function getUrlAttribute()
    {
        if (! empty($this->attributes['url'])) {
            return $this->attributes['url'];
        }

        return config('solder.mirror_url').'mods/'.$this->mod->name.'/'.$this->mod->name.'-'.$this->version.'.zip';
    }

    public function humanFilesize($unit = null)
    {
        $size = $this->filesize;
        if ((! $unit && $size >= 1 << 30) || $unit == 'GB') {
            return number_format($size / (1 << 30), 2).' GB';
        }
        if ((! $unit && $size >= 1 << 20) || $unit == 'MB') {
            return number_format($size / (1 << 20), 2).' MB';
        }
        if ((! $unit && $size >= 1 << 10) || $unit == 'KB') {
            return number_format($size / (1 << 10), 2).' KB';
        }

        return number_format($size).' bytes';
    }

    public function toApiResponse(bool $full)
    {
        $response = [
            'id' => $this->id,
            'name' => $this->mod->name,
            'version' => $this->version,
            'md5' => $this->md5,
            'filesize' => $this->filesize,
            'url' => $this->url,
        ];

        if ($full) {
            $response = array_merge($response, [
                'pretty_name' => $this->mod->pretty_name,
                'author' => $this->mod->author,
                'description' => $this->mod->description,
                'link' => $this->mod->link,
            ]);
        }

        return $response;
    }
}

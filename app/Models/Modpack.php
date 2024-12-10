<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $recommended
 * @property string|null $latest
 * @property string|null $url
 * @property string|null $icon_md5
 * @property string|null $logo_md5
 * @property string|null $background_md5
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $order
 * @property bool $hidden
 * @property bool $private
 * @property bool $icon
 * @property bool $logo
 * @property bool $background
 * @property string|null $icon_url
 * @property string|null $logo_url
 * @property string|null $background_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Build> $builds
 * @property-read int|null $builds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereBackgroundMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereBackgroundUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereIconMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereLatest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereLogoMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack wherePrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereRecommended($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modpack whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Modpack extends Model
{
    protected $guarded = [];

    public function builds(): HasMany
    {
        return $this->hasMany(Build::class);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class)->withTimestamps();
    }

    public function private_builds()
    {
        $private = false;
        foreach ($this->builds as $build) {
            if ($build->private) {
                $private = true;
                break;
            }
        }

        return $private;
    }

    public function toApiResponse(?Client $client = null, ?Key $key = null)
    {
        $response = [
            'id' => $this->id,
            'name' => $this->slug,
            'display_name' => $this->name,
            'url' => $this->url,
            'icon' => $this->icon_url,
            'icon_md5' => $this->icon_md5,
            'logo' => $this->logo_url,
            'logo_md5' => $this->logo_md5,
            'background' => $this->background_url,
            'background_md5' => $this->background_md5,
            'recommended' => $this->recommended,
            'latest' => $this->latest,
        ];

        $response['builds'] = $this->builds->filter(function ($build) use ($client, $key) {
            // Don't return unpublished builds
            if (! $build->is_published) {
                return false;
            }

            // If this build isn't private, return it
            if (! $build->private) {
                return true;
            }

            // If a key is set, return all the builds
            if ($key) {
                return true;
            }

            // If this is a private build and there's a client set, check if the client can access it
            return $client && $client->modpacks->contains($this);
        })->pluck('version');

        return $response;
    }
}

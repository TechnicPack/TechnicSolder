<?php

namespace App\Models;

use App\Http\ApiAuthContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $order
 * @property bool $hidden
 * @property bool $private
 * @property bool $icon
 * @property bool $logo
 * @property bool $background
 * @property string|null $icon_url
 * @property string|null $logo_url
 * @property string|null $background_url
 * @property-read Collection<int, Build> $builds
 * @property-read int|null $builds_count
 * @property-read Collection<int, Client> $clients
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
    protected $fillable = [
        'name',
        'slug',
        'recommended',
        'latest',
        'url',
        'icon',
        'icon_md5',
        'icon_url',
        'logo',
        'logo_md5',
        'logo_url',
        'background',
        'background_md5',
        'background_url',
        'order',
        'hidden',
        'private',
    ];

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

    public function isAccessibleBy(ApiAuthContext $auth): bool
    {
        return $auth->key
            || ($auth->user && $auth->user->permission->canAccessModpack($this->id))
            || ($auth->client && $auth->client->modpacks->contains($this));
    }

    public function toApiResponse(ApiAuthContext $auth)
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

        $canViewPrivate = $this->isAccessibleBy($auth);

        $response['builds'] = $this->builds->filter(function ($build) use ($canViewPrivate) {
            if (! $build->is_published) {
                return false;
            }

            if (! $build->private) {
                return true;
            }

            return $canViewPrivate;
        })->pluck('version');

        return $response;
    }
}

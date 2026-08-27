<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property bool $solder_full
 * @property bool $solder_users
 * @property bool $mods_create
 * @property bool $mods_manage
 * @property bool $mods_delete
 * @property string|null $modpacks
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $solder_keys
 * @property bool $solder_clients
 * @property bool $modpacks_create
 * @property bool $modpacks_manage
 * @property bool $modpacks_delete
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereModpacks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereModpacksCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereModpacksDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereModpacksManage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereModsCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereModsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereModsManage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereSolderClients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereSolderFull($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereSolderKeys($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereSolderUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserPermission extends Model
{
    /**
     * The grantable boolean permission columns, mapped to the form field that
     * sets them. Single source of truth for the grant clamp (`grant-permission`
     * gate), the create/edit forms, and the user-management subset check —
     * adding a permission means adding one entry here.
     *
     * Excludes the per-modpack `modpacks` scope, which is not a boolean flag.
     *
     * @var array<string, string>
     */
    public const GRANTABLE_FIELDS = [
        'solder_full' => 'solder-full',
        'solder_users' => 'manage-users',
        'solder_keys' => 'manage-keys',
        'solder_clients' => 'manage-clients',
        'mods_create' => 'mod-create',
        'mods_manage' => 'mod-manage',
        'mods_delete' => 'mod-delete',
        'modpacks_create' => 'modpack-create',
        'modpacks_manage' => 'modpack-manage',
        'modpacks_delete' => 'modpack-delete',
    ];

    /**
     * The permission columns compared when deciding whether one user's
     * permissions are a subset of another's. Excludes solder_full, which is
     * handled separately by the Gate::before superadmin bypass.
     *
     * @return list<string>
     */
    public static function permissionFlags(): array
    {
        return array_values(array_diff(array_keys(self::GRANTABLE_FIELDS), ['solder_full']));
    }

    protected $fillable = [
        'user_id',
        'solder_full',
        'solder_users',
        'solder_keys',
        'solder_clients',
        'mods_create',
        'mods_manage',
        'mods_delete',
        'modpacks',
        'modpacks_create',
        'modpacks_manage',
        'modpacks_delete',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setModpacksAttribute($modpack_array)
    {
        if (is_array($modpack_array)) {
            $this->attributes['modpacks'] = implode(',', $modpack_array);
        } else {
            $this->attributes['modpacks'] = null;
        }
    }

    public function getModpacksAttribute($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return preg_split('/[,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function canAccessModpack(int $id): bool
    {
        return $this->solder_full || in_array($id, $this->modpacks);
    }

    public function grantModpackAccess(int $modpackId): void
    {
        $this->modpacks = [...$this->modpacks, $modpackId];
        $this->save();
    }
}

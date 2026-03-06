<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $created_ip
 * @property string|null $last_ip
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $remember_token
 * @property string|null $updated_by_ip
 * @property int $created_by_user_id
 * @property int|null $updated_by_user_id
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\UserPermission|null $permission
 * @property-read User|null $updated_by_user
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedByIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'created_ip',
        'last_ip',
        'created_by_user_id',
        'updated_by_user_id',
        'updated_by_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = ['permission'];

    public function permission(): HasOne
    {
        return $this->hasOne(UserPermission::class);
    }

    /**
     * Override to include the instance URL in the TOTP issuer.
     *
     * @see https://github.com/laravel/fortify/blob/1.x/src/TwoFactorAuthenticatable.php
     */
    public function twoFactorQrCodeUrl(): string
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: config('app.url');

        return app(TwoFactorAuthenticationProvider::class)->qrCodeUrl(
            config('app.name').' ('.$host.')',
            $this->{Fortify::username()},
            Fortify::currentEncrypter()->decrypt($this->two_factor_secret)
        );
    }

    public function updated_by_user(): HasOne
    {
        return $this->hasOne(self::class, 'id', 'updated_by_user_id');
    }
}

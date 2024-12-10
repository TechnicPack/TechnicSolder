<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $api_key
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Key whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Key extends Model
{
    protected $guarded = [];
}

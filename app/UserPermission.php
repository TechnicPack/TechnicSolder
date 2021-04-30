<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    public function user()
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

    public function getModpacksAttribute($value)
    {
        return preg_split('/[,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
    }
}
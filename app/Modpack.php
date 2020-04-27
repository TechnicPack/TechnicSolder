<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modpack extends Model
{
    public function builds()
    {
        return $this->hasMany(Build::class);
    }

    public function clients()
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
}
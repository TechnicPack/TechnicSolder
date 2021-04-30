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

    public function toApiResponse(Client $client = null, Key $key = null)
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
            if (!$build->is_published) {
                return false;
            }

            // If this build isn't private, return it
            if (!$build->private) {
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
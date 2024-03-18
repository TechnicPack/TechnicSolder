<?php

namespace App\Mods;

use Illuminate\Database\Eloquent\Model;

class ImportedModData
{
    public $id;
    public $slug;

    public $name;
    public $summary;
    
    public $authors;
    public $thumbnailUrl;
    public $thumbnailDesc;
    public $websiteUrl;

    public $versions;
}

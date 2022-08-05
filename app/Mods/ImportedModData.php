<?php

namespace App\Mods;

use Illuminate\Database\Eloquent\Model;

class ImportedModData // TODO find a better name
{
    public $id;
    public $slug;

    public $name;
    public $summary;
    
    public $authors;
    public $thumbnailUrl;
    public $thumbnailDesc;
    public $websiteUrl;
}

<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    public function getImageUrlAttribute()
    {
        return asset('storage/images/' . $this->image);
    }
}

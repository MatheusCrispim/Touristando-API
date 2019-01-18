<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attraction extends Model
{
    protected $fillable = [
        'name', 'description', 'latitude', 'longitude'
    ];


    public function images()
    {
        return $this->hasMany(Image::class, 'attraction_id'); 
    }


}



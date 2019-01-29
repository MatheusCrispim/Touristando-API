<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attraction extends Model
{
    protected $fillable = [
        'name', 'description', 'latitude', 'longitude', 'user_id'
    ];


    public function images()
    {
        return $this->hasMany(Image::class, 'attraction_id'); 
    }


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}




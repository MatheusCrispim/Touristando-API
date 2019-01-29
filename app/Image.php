<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attraction;

class Image extends Model
{
    protected $fillable = [
        'name', 'attraction_id', 'user_id'
    ];

    
    public function attraction(){
        return $this->belongsTo(Attraction::class, 'attraction_id');
    }


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}

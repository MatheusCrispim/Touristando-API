<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attraction;

class Image extends Model
{
    protected $fillable = [
        'name', 'attraction_id'
    ];

    
    public function attraction(){
        return $this->belongsTo(Attraction::class, 'attraction_id');
    }

}

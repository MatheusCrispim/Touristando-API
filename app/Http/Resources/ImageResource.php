<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $attractions_view_path = Config::get('constants.attractions_view_path');

        return [
            'id' => $this->id,
            'path' =>  $attractions_view_path.'/images/'.$this->name, 
        ];
    }
}

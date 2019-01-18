<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class ImageUtils
{


    //This function checks if the image base64 is valid
    public function isValidImage($data)
    {
        if (base64_decode($data, true) == false) 
        {
            return false;
        }
        return true;
    }
    

    //This function save the image
    public function saveImage($name, $file)
    {
        $attractions_storage_path = Config::get('constants.attractions_storage_path');
        $imagePath =  $attractions_storage_path.'/images/'.$name;
        Storage::put($imagePath, base64_decode($file));
    }    


    public function removeImage($name){
        $attractions_storage_path = Config::get('constants.attractions_storage_path');
        $imagePath =  $attractions_storage_path.'/images/'.$name;
        Storage::delete($imagePath);
    }
    

}
<?php

namespace App\Http\Controllers;

use App\Image;
use App\Helpers\ImageUtils;
use Illuminate\Http\Request;
use App\Http\Resources\ImageResource;

class ImageController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $data = $request->all();
        $imageUtils = new ImageUtils();
        
        if($imageUtils->isValidImage($data['image']))
        {
            $imageName = md5(uniqid()).".jpg";
            $imageUtils->saveImage($imageName, $data['image']);

            $data['name'] = $imageName;
            $image = Image::create($data);

            return new ImageResource($image);
        }

        return response()->json("Imagem inválida!", 404); 
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::find($id);
        $imageUtils = new ImageUtils();

        if(!is_null($image))
        {
            $imageName = $image->name;
            $imageUtils->removeImage($imageName);

            $image->delete();

            return response()->json("Removida com sucesso!", 204);
        }

        return response()->json("Nada encontrado!", 404);
    }

}

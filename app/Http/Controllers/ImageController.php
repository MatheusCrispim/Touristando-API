<?php

namespace App\Http\Controllers;

use App\Image;
use App\Helpers\ImageUtils;
use Illuminate\Http\Request;
use App\Http\Resources\ImageResource;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = [
            'message' => 'Não é possível visualizar todas as imagens registradas, tente visualizar por meio do endpoint /attraction/{id}/images, ou /images/{id}'
        ];

        return response()->json( $response, 422);
    }


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


        $validator = Validator::make($request->all(), [
            'attraction_id' => ['required', 'exists:attractions,id'],
            'image' => ['required'],
        ],[
            'required' => 'O campo :attribute é obrigatório',
            'exists' => 'O dado passado no campo :attribute não existe no banco',
        ],[
            'attraction_id' => "attraction_id",
            'image' => "image",
        ]);
    
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        if($imageUtils->isValidImage($data['image']))
        {
            $imageName = md5(uniqid()).".jpg";
            $imageUtils->saveImage($imageName, $data['image']);

            $data['user_id'] = $request->user()->id;
            $data['name'] = $imageName;
            $image = Image::create($data);

            return new ImageResource($image);
        }

        return response(['errors'=>['Imagem inválida']], 422);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $image=Image::find($id);

        if(!is_null($image))
        {
            return new ImageResource($image);
        }

        $response = [
            'message' => 'Nada encontrado!'
        ];

        return response()->json( $response, 404);
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
        $response = [
            'message' => 'Não é possível atualizar uma imagem, remova e adicione uma nova'
        ];

        return response()->json( $response, 422);
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


            return response()->json(null, 204);
        }

        $response = [
            'message' => 'Nada encontrado!'
        ];

        return response()->json( $response, 404);
    }

}

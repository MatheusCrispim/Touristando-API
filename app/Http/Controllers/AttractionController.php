<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Attraction;
use App\Helpers\ImageUtils;
use App\Http\Resources\ImageResource;
use App\Http\Resources\AttractionResource;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class AttractionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AttractionResource::collection(Attraction::paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $imageUtils = new ImageUtils();

        $data = $request->all();

        $validator = Validator::make($data, 
        [   'name' => 'required|max:191', 
            'description' => 'required', 
            'image' => 'required', 
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', 'max:19'], 
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', 'max:19'], 
        ],[
            'required' => 'O campo :attribute é obrigatório',
            'latitude.regex' => 'O dado passado no campo :attribute deve ser uma latitude',
            'longitude.regex' => 'O dado passado no campo :attribute deve ser uma longitude',
            'latitude.max' => 'O valor passado no campo :attribute excede o limite de precisão',
            'longitude.max' => 'O valor passado no campo :attribute excede o limite de precisão',
            'max' => 'O dado passado no campo :attribute excede a quantidade limite de caracteres'
        ],[
            'name' => 'name',
            'description' => 'description',
            'image' => 'image',
            'latitude' => 'latitude',
            'longitude' => 'longitude'
        ]);


        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        if(!$imageUtils->isValidImage($data['image']))
        {
            return response(['errors'=>['Imagem inválida']], 422);
        }

        $numberOfAttractions = Attraction::where('latitude', '=', $data['latitude'], 'and', 'longitude', '=', $data['longitude'])->count();

        if($numberOfAttractions == 0)
        {
         
            $data['user_id'] = $request->user()->id;
            $attraction = Attraction::create($data);
            $request->merge(['attraction_id' =>  $attraction->id, 'image' => $data['image']]);

            $imagesController = new ImageController();
            $imagesController->store($request);

            return new AttractionResource($attraction);
        }

        $response = [
            'message' => 'Já cadastrado!'
        ];

        return response()->json($response , 409);
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attraction=Attraction::find($id);

        if(!is_null($attraction))
        {
            return new AttractionResource($attraction);
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
        $data = $request->all();

        $validator = Validator::make($data, 
        [   'name' => 'max:191',  
            'latitude' => ['regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', 'max:20'], 
            'longitude' => ['regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', 'max:20'], 
        ],[
            'latitude.regex' => 'O dado passado no campo :attribute deve ser uma latitude',
            'longitude.regex' => 'O dado passado no campo :attribute deve ser uma longitude',
            'latitude.max' => 'O valor passado no campo :attribute excede o limite de precisão',
            'longitude.max' => 'O valor passado no campo :attribute excede o limite de precisão',
            'max' => 'O dado passado no campo :attribute excede a quantidade limite de caracteres'
        ],[
            'name' => 'name',
            'latitude' => 'latitude',
            'longitude' => 'longitude'
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $attraction = Attraction::find($id);  

        if(!is_null($attraction))
        {
            $attraction->update($data);
            return (new AttractionResource($attraction))
                                            ->response()
                                            ->setStatusCode(202);
        }

        $response = [
            'message' => 'Nada encontrado!'
        ];

        return response()->json( $response, 404);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attraction = Attraction::find($id);  
        if(!is_null($attraction))
        {
            $images = $attraction->images;
            $imagesController = new ImageController();

            foreach($images as $image)
            {
                $imagesController->destroy($image->id);
            }
            
            $attraction->delete();

            return response()->json(null, 204);
        }

        $response = [
            'message' => 'Nada encontrado!'
        ];

        return response()->json( $response, 404);
    }


    public function getNearbyAttractions($latitude, $longitude, $radius)
    {
        $data = [
          
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius' => $radius,
        ];

        $validator = Validator::make($data, 
        [   
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'], 
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'], 
            'radius' => 'required|numeric',  
        ],[
            'required' => 'O campo :attribute é obrigatório',
            'numeric' => 'O dado passado no campo :attribute deve ser um raio',
            'latitude.regex' => 'O dado passado no campo :attribute deve ser uma latitude',
            'longitude.regex' => 'O dado passado no campo :attribute deve ser uma longitude',
        ],[
            'radius' => 'radius',
            'latitude' => 'latitude',
            'longitude' => 'longitude'
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all(), 'format'=>'Os dados devem ser passados da seguinte forma /latitude/longitude/raio via GET'], 422);
        }
        
        $data = array('latitude'=>$latitude, 'longitude'=>$longitude, 'latitude2'=>$latitude, 'radius'=>$radius);
        
        $attractions = DB::select( DB::raw(
            "SELECT *,
                    (6371 * acos(
                        cos( radians(:latitude) )
                        * cos( radians( latitude ) )
                        * cos( radians( longitude ) - radians(:longitude) )
                        + sin( radians(:latitude2) )
                        * sin( radians( latitude ) ) 
                        )
                    ) AS distance
            FROM `attractions` 
            HAVING distance <= :radius
            ORDER BY distance ASC;"
        ),  $data);

        
        return $attractions;
    }


    public function getAttractionImages($id)
    {
        $attraction = Attraction::find($id);

        if(!is_null($attraction))
        {
            $images =  $attraction->images;
            return ImageResource::collection($images);
        }

        $response = [
            'message' => 'Nada encontrado!'
        ];

        return response()->json( $response, 404);
    }

}

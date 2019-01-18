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
        $arrayUtils = new ArrayUtils();
        $imageUtils = new ImageUtils();

        $data = $request->all();

        $validate = Validator::make($data, 
        [   'name' => 'required|max:191', 
            'description' => 'required', 
            "image" => 'required', 
            'latitude' => 'required', 
            'longitude' => 'required'
        ]);

        if ($validate->fails())
        {
            return response()->json( "Requisição mal formada!", 404); 
        }

        if(!$imageUtils->isValidImage($data['image']))
        {
            return response()->json( "Requisição mal formada!", 404); 
        }

        $numberOfAttractions=Attraction::where('latitude', '=', $data['latitude'], 'and', 'longitude', '=', $data['latitude'])->count();

        if($numberOfAttractions == 0)
        {
            $attraction = Attraction::create($data);

            $imageRequest = new Request();
            $imageRequest->setMethod('POST');
            $imageRequest->request->add(['attraction_id' =>  $attraction->id, 'image' => $data['image']]);

            $imagesController = new ImageController();
            $imagesController->store($imageRequest);

            return new AttractionResource($attraction);
        }

        return response()->json( "Já cadastrado!", 409);
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
            return new attractionResource($attraction);
        }

        return response()->json("Nada encontrado!", 404);
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
        $attraction = Attraction::find($id);  

        if(!is_null($attraction))
        {
            $attraction->update($data);
            return (new AttractionResource($attraction))
                                            ->response()
                                            ->setStatusCode(202);
        }

        return response()->json("Nada encontrado!", 404);   
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
            return response()->json("Removido com sucesso!", 204);
        }

        return response()->json("Nada encontrado!", 404);  
    }


    public function getNearbyAttractions($latitude, $longitude, $radius)
    {
        
        $request = array('latitude'=>$latitude, 'longitude'=>$longitude, 'latitude2'=>$latitude, 'radius'=>$radius);
        
        $attraction = DB::select( DB::raw(
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
            HAVING distance < :radius
            ORDER BY distance ASC;"
        ),   $request);

        
        return $attraction;
    }


    public function getAttractionImages($id)
    {
        $attraction = Attraction::find($id);

        if(!is_null($attraction))
        {
            $images =  $attraction->images;
            print_r($images);
            return ImageResource::collection($images);
        }

        return response()->json( "Nada encontrado!", 404);
    }

}

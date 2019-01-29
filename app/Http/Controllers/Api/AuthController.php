<?php

namespace App\Http\Controllers\Api;


use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ],[
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O dado passado no campo :attribute não é do tipo string',
            'email' => 'O email passado no campo :attribute é inválido',
            'unique' => 'O :attribute já está cadastrado',
            'max' => 'O campo :attribute excede a quantidade máxima de caracteres permitidos',
            'min' => 'O campo :attribute tem que ter no mínimo 6 caracteres',
            'confirmed' => 'É necessário um campo de confirmação :attribute_confirmation para o campo :attribute',
        ],[
            'name' => "name",
            'email' => "email",
            'password' => "passowrd"
        ]);
    
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
    
        $request['password']=Hash::make($request['password']);
        $user = User::create($request->toArray());
    
        $token = $user->createToken('Token')->accessToken;
        $response = ['token' => $token];
    
        return response($response, 200);
    
    }


    public function login(Request $request) {

        $user = User::where('email', $request->email)->first();
    
        if ($user) {
    
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Token')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = "Senha incorreta";
                return response($response, 422);
            }
    
        } else {
            $response = 'Usuário inexistente';
            return response($response, 422);
        }
    
    }


    public function logout(Request $request) {

        $token = $request->user()->token();
        $token->revoke();

        $response = 'Desconectado com sucesso!';
        return response($response, 200);

    }


    public function unauthenticated(Request $request){
        $reponse="Não autenticado";
        return response($response, 403);
    }

}

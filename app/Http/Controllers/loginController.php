<?php

namespace App\Http\Controllers;

use App\db_categories;
use App\db_has_categories;
use App\db_tokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class loginController extends Controller
{
    public function login(Request $request){
        $device_token = $request->token_device;
        $email = $request->email;
        $password = $request->password;

        if(!isset($email)){
            $response = array(
                'status'=> 'fail',
                'msj'=> 'Email vacio',
                'code'=> 5,
            );
            return response()->json($response);
        }

        if(!isset($password)){
            $response = array(
                'status'=> 'fail',
                'msj'=> 'Password Vacio',
                'code'=> 5,
            );
            return response()->json($response);
        }

        if (Auth::attempt(['email' => $email, 'password' => $password])){
            $id_user = User::where('email',$email)->first();
            $data = User::find($id_user->id);
            $token = str_random(10);
            db_tokens::where('id_user',$id_user->id)->delete();
            
            $key=db_tokens::insertGetId([
                'token'=>$token,
                'token_fcm'=>$device_token,
                'id_user'=>$id_user->id
            ]);
            $data->setAttribute('key',$key);
            $data->setAttribute('token',$token);

            $categorie = db_has_categories::where('users_id',$id_user->id)->first();
            if(db_has_categories::where('users_id',$id_user->id)->exists()){
                $categorie = db_categories::where('id',$categorie->db_categories_id)->first();
            }else{
                $categorie = null;
            }

            $data->setAttribute('categorie',$categorie);

            $response = array(
                'status'=> 'success',
                'msj'=> '',
                'data' => $data,
                'code'=> 0,
            );

            return response()->json($response);
        }else{
            $response = array(
                'status'=> 'fail',
                'msj'=> 'Credenciales Incorrectas',
                'code'=> 5,
            );
            return response()->json($response);
        }
    }

    public function logout(){

        $response = array(
            'status' => 'success',
            'msj' => 'Session Out',
            'code' => 0
        );

        return response()->json($response);
    }
    //comment
}

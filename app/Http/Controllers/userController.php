<?php

namespace App\Http\Controllers;

use App\db_categories;
use App\db_has_categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class userController extends Controller
{
    public function create(Request $request)
    {

        $name = $request->name;
        $password = $request->password;
        $email = $request->email;
        $avatar = $request->avatar;
        $bod = $request->bod;
        $last_name = $request->last_name;
        $phone = $request->phone;
        $uuid = $request->uuid;
        $address = $request->address;
        $level = $request->level;
        $categorie = $request->categorie;

        if (!isset($name)) {
            $response = array(
                'status' => 'fail',
                'msj' => 'Nombre no puede ser vacio',
                'code' => 5,
            );


            return response()->json($response);
        }

        if (!isset($password)) {
            $response = array(
                'status' => 'fail',
                'msj' => 'Password vacio',
                'code' => 5,
            );


            return response()->json($response);
        }

        if (!isset($email)) {
            $response = array(
                'status' => 'fail',
                'msj' => 'Email vacio',
                'code' => 5,
            );


            return response()->json($response);
        }

        if (!isset($categorie)) {
            $response = array(
                'status' => 'fail',
                'msj' => 'ID categoria vacio',
                'code' => 5,
            );


            return response()->json($response);
        }

        $values = array(
            'name' => $name,
            'password' => Hash::make($password),
            'email' => $email,
            'avatar' => $avatar,
            'bod' => $bod,
            'last_name' => $last_name,
            'phone' => $phone,
            'uuid' => $uuid,
            'address' => $address,
            'level' => $level,
        );

        $dataID = User::insertGetId($values);

        db_has_categories::insert([
            'db_categories_id' => $categorie,
            'users_id' => $dataID
        ]);

        $response = array(
            'status' => 'success',
            'msj' => 'Se creo el usuario',
            'code' => 0
        );

        return response()->json($response);
    }

    public function get(Request $request)
    {

        $act = $request->act;
        if (!isset($act)) {
            $response = array(
                'status' => 'fail',
                'msj' => 'Defina act',
                'code' => 5
            );

            return response()->json($response);
        }
        switch ($act) {
            case 'user':
                $id = $request->id;

                if (!isset($id)) {
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Id no puede ser vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }

                if (!$r = User::where('id', $id)->exists()) {
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'No existe usuario',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $data = User::find($id);
                $tmp_cat = null;
                if(db_has_categories::where('users_id',$id)->first()){
                    $tmp_cat = db_categories::where('id',db_has_categories::where('users_id',$id)->first()->db_categories_id)->first();
                }

                $data->setAttribute('categorie',$tmp_cat);
                $response = array(
                    'status' => 'success',
                    'data' => $data,
                    'msj' => '',
                    'code' => 0
                );

                return response()->json($response);
                break;
            case 'all':
                $data = User::orderBy('id','desc')->get();
                $response = array(
                    'status' => 'success',
                    'data' => $data,
                    'msj' => '',
                    'code' => 0
                );

                foreach ($data as $u){
                    $tmp_cat = null;
                    if(db_has_categories::where('users_id',$u->id)->first()){
                        $tmp_cat = db_categories::where('id',db_has_categories::where('users_id',$u->id)->first()->db_categories_id)->first();
                    }
                    $u->setAttribute('categorie',$tmp_cat);
                }

                return response()->json($response);
                break;
        }

    }

    public function update(Request $request){

        $act = $request->act;
        $id = $request->id;
        $name = $request->name;
        $level = $request->level;
        $avatar = $request->avatar;

        $bod = $request->bod;
        $last_name = $request->last_name;
        $phone = $request->phone;
        $uuid = $request->uuid;
        $address = $request->address;
        $categorie = $request->categorie;

        switch ($act){
            case 'app':

                $values = array(
                    'name' => $name,
                    'bod' => $bod,
                    'last_name' => $last_name,
                    'phone' => $phone,
                    'address' => $address,
                );

                User::where('id',$id)->update($values);
                $response = array(
                    'status' => 'success',
                    'msj' => 'Datos actualizados',
                    'code' => 0,
                );

                return response()->json($response);

                break;
            default:

                if (!isset($name)) {
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Nombre no puede ser vacio',
                        'code' => 5,
                    );

                    return response()->json($response);
                }


                $values = array(
                    'name' => $name,
                    'avatar' => $avatar,
                    'level' => $level,
                    'bod' => $bod,
                    'last_name' => $last_name,
                    'phone' => $phone,
                    'uuid' => $uuid,
                    'address' => $address,

                );
                User::where('id',$id)->update($values);

                if(db_has_categories::where('users_id',$id)->exists()){
                    db_has_categories::where('users_id',$id)->update([
                        'db_categories_id' => $categorie,
                    ]);
                }else{
                    db_has_categories::insert([
                        'db_categories_id' => $categorie,
                        'users_id' => $id,
                    ]);
                }


                $response = array(
                    'status' => 'success',
                    'msj' => 'Datos actualizados',
                    'code' => 0,
                );

                return response()->json($response);
                break;
        }

    }

    public function delete(Request $request){

        $id = $request->id;

        if (!isset($id)) {
            $response = array(
                'status' => 'fail',
                'msj' => 'ID vacio',
                'code' => 5,
            );

            return response()->json($response);
        }


        User::where('id',$id)->delete();

        $response = array(
            'status' => 'success',
            'msj' => 'Usuario eliminado',
            'code' => 0,
        );

        return response()->json($response);
    }

    public function resetpwd(Request $request){
        $id_user = $request->id_user;
        $new = $request->new;

        if(!isset($id_user)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID user vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        if(!isset($new)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Password vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        User::where('id',$id_user)->update([
            'password' => bcrypt($new),
        ]);

            $response = array(
                'status' => 'success',
                'msj' => 'Contraseña restablecida',
                'code' => 0
            );

            return response()->json($response);

    }
}

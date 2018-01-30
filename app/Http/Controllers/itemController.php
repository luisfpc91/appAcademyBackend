<?php

namespace App\Http\Controllers;

use App\db_has_categories;
use App\db_images;
use App\db_item;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class itemController extends Controller
{
    public function create(Request $request){
        $title = $request->title;
        $description = $request->description;
        $id_image = $request->id_image;
        $start_at = $request->start_at;
        $id_user = $request->id_user;
        $id_cat = $request->id_cat;

        if(!isset($title)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Titulo Vacio',
                'code' => 0
            );
            return response()->json($response);
        }
        if(!isset($id_image)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID imagen vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        if(!isset($id_user)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID user vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        if(!isset($id_cat)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID cat vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        $values = array(
            'title' => $title,
            'description' => $description,
            'id_image' => $id_image,
            'created_at' => Carbon::now(),
            'start_at' => Carbon::parse($start_at),
            'id_user' => $id_user,
            'id_cat' => $id_cat,
        );

        $id = db_item::insertGetId($values);


        $response = array(
            'status' => 'success',
            'msj' => 'Se agrego correctamente',
            'code' => 0
        );

        return response()->json($response);
    }

    public function save(Request $request){
        $id_item = $request->id_item;

        if(!isset($id_item)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID vacio',
                'code' => 5
            );

            return response()->json($response);
        }


        $title = $request->title;
        $description = $request->description;
        $id_image = $request->id_image;
        $start_at = $request->start_at;
        $id_user = $request->id_user;
        $id_cat = $request->id_cat;


        if(!isset($title)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Titulo Vacio',
                'code' => 0
            );
            return response()->json($response);
        }
        if(!isset($id_image)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID imagen vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        if(!isset($id_user)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID user vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        if(!isset($id_cat)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID cat vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        $values = array(
            'title' => $title,
            'description' => $description,
            'id_image' => $id_image,
            'created_at' => Carbon::now(),
            'start_at' => Carbon::parse($start_at),
            'id_user' => $id_user,
            'id_cat' => $id_cat,
        );

        db_item::where('id',$id_item)->update($values);

        $response = array(
            'status' => 'success',
            'msj' => 'Editado',
            'code' => 0
        );
        return response()->json($response);
    }

    public function get(Request $request){

        $act = $request->act;
        if(!isset($act)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Act vacio',
                'code' => 5
            );
            return response()->json($response);
        }
        switch ($act){
            case 'all':
                $data = db_item::orderBy('id','desc')->get();
                foreach ($data as $d){
                    $image = null;
                    if(db_images::where('id',$d->id_image)->exists()){
                        $tmp = db_images::find($d->id_image);
                        $tmp->setAttribute('path',config("app.url").'/'.$tmp->path);
                        $image = $tmp;
                    }
                    $d->setAttribute('image',$image);
                }
                $response = array(
                    'status' => 'success',
                    'data' => $data,
                    'msj' => '',
                    'code' => 0
                );
                return response()->json($response);

                break;
            case 'recent':
                $id_user = $request->id_user;
                if(!isset($id_user)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'ID user vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }

                $data=db_has_categories::where('users_id',$id_user)->first();
                if(db_has_categories::where('users_id',$id_user)->exists()){
                    $items = db_item::where('id_cat',$data->db_categories_id)->orderBy('id','desc')->get();
                    foreach ($items as $item){
                        $date = Carbon::parse($item->start_at)->format('j, M Y');
                        $item->setAttribute('start_at',$date);
                    }

                    $data_media = db_images::where('id_categories',$data->db_categories_id)->orderBy('id','desc')->get();
                    foreach ($data_media as $media) {
                        if($media->type=='video'){
                            preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $media->youtube_link, $matches);
                            $media->setAttribute('url','https://img.youtube.com/vi/'.$matches[0].'/0.jpg');
                        }else{
                            $media->setAttribute('url',config("app.url"));
                        }

                    }
                }else{
                    $items = array();
                    $data_media = array();
                }



                $response = array(
                    'status' => 'success',
                    'msj' => '',
                    'data' => $items,
                    'recent_media' => $data_media,
                    'code' => 0
                );

                return response()->json($response);

                break;
            case 'once':
                $id_item = $request->id_item;

                if(!isset($id_item)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'ID item',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                if(!db_item::where('id',$id_item)->exists()){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'ID no existe',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $items = db_item::find($id_item);

                if(db_images::where('id',$items->id_image)->exists()){
                    $tmp = db_images::find($items->id_image);
                    $tmp->setAttribute('path',config("app.url").'/'.$tmp->path);
                    $image = $tmp;
                }else{
                    $image = null;
                }
                $items->setAttribute('image',$image);

                $response = array(
                    'status' => 'success',
                    'msj' => '',
                    'data' => $items,
                    'code' => 0
                );

                return response()->json($response);
                break;
        }
    }

    public function delete(Request $request)
    {
        $id_item = $request->id_item;

        if(!isset($id_item)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        db_item::where('id',$id_item)->delete();
        $response = array(
            'status' => 'success',
            'msj' => 'Eliminado',
            'code' => 0
        );

        return response()->json($response);

    }
}

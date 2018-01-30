<?php

namespace App\Http\Controllers;

use App\db_categories;
use App\db_images;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Datetime;
use Intervention\Image\ImageManagerStatic as Image;

class imageController extends Controller
{
    public function upload(Request $request){

        $act = $request->act;
        $id_usr = $request->id_usr;
        switch ($act){
            case 'youtube':
                $name = $request->name;
                $id_categorie = $request->id_categorie;
                $url_youtube = $request->url_youtube;

                if(!isset($url_youtube)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Youtube URL vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $values = array(
                    'id_user' => $id_usr,
                    'name' => $name,
                    'id_categories' => $id_categorie,
                    'created_at' => Carbon::now(),
                    'youtube_link' => $url_youtube,
                    'type' => 'video'
                );
                db_images::insertGetId($values);

                $response = array(
                    'status' => 'success',
                    'msj' => 'Se agrego video',
                    'code' => 0
                );

                return response()->json($response);
                break;

            default:
                $path = public_path();
                $imageName = time(microtime(true));

                $files = $request->file('file');
                $search_index = $request->search_index;

                if(isset($search_index)) $search_index = true;

                if(!isset($files)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'File vacio',
                        'code' => 0,
                    );
                    return response()->json($response);
                }


                $tmp_name = md5(($files->getClientOriginalName()).$imageName.'.'.($files->getClientOriginalExtension()));

                $files->move($path.'/upload', $tmp_name.'.'.$files->getClientOriginalExtension());



                $id=db_images::insertGetId(
                    [
                        'path' => 'upload/'.$tmp_name.'.'.$files->getClientOriginalExtension(),
                        'id_user' => $id_usr,
                        'name' => $tmp_name,
                        'created_at' => new Datetime('now'),
                        'search_index' => $search_index,
                    ]
                );

                $data_attached = db_images::where('id',$id)->first();
                $status = array(
                    'status' => 'success',
                    'id' => $id,
                    'data' => config('app.url').'/'.$data_attached->path
                );

                return response()->json($status);
                break;
        }

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
            case 'filter':
                $filter = $request->filter;
                if(!isset($filter)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Filter Vacia',
                        'code' => 5
                    );

                    return response()->json($response);
                }

                $data = db_images::where('id_categories',$filter)->get();
                foreach ($data as $i){
                    $i->setAttribute('path',config('app.url').'/'.$i->path);
                }
                $response = array(
                    'status' => 'success',
                    'data' => $data,
                    'code' => 0
                );

                return response()->json($response);
                break;
            case 'youtube':
                $id = $request->id;
                if(!isset($id)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Error Id vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }

                if(!$r=db_images::where('id',$id)->exists()){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'No existe id',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $data = db_images::find($id);
                preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $data->youtube_link, $matches);
                $data->setAttribute('youtube_img','https://img.youtube.com/vi/'.$matches[0].'/0.jpg');
                $data->setAttribute('id_youtube',$matches[0]);

                $response = array(
                    'status' => 'success',
                    'msj' => '',
                    'data' => $data,
                    'code' => 0
                );

                return response()->json($response);
                break;
            case 'once':
                $id = $request->id;
                if(!isset($id)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Error Id vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }

                if(!$r=db_images::where('id',$id)->exists()){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'No existe id',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $data = db_images::where('id',$id)->first();
                $data->setAttribute('path',config('app.url').'/'.$data->path);
                $data->setAttribute('id_categories',db_categories::where('id',$data->id_categories)->first());
                $data->setAttribute('url_origin',config('app.url'));

                if($data->type=='video'){
                    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $data->youtube_link, $matches);
                    $data->setAttribute('youtube_img','https://img.youtube.com/vi/'.$matches[0].'/0.jpg');
                    $data->setAttribute('id_youtube',$matches[0]);
                }else if($data->type=='img'){
                    $data->setAttribute('youtube_img',null);
                    $data->setAttribute('id_youtube',null);
                }else if($data->type=='pdf'){
                    $data->setAttribute('path',config('app.url').'/img/pdf_icon.jpg');
                }


                $response = array(
                    'status' => 'success',
                    'data' => $data,
                    'code' => 0
                );

                return response()->json($response);
                break;
            case 'all':
                $data = db_images::whereNull('search_index')->orderBy('id','desc')->get();
                foreach ($data as $i){
                    if($i->type=='video'){
                        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $i->youtube_link, $matches);
                        $i->setAttribute('path','https://img.youtube.com/vi/'.$matches[0].'/0.jpg');
                    }else if($i->type=='img'){
                        $i->setAttribute('path',config('app.url').'/'.$i->path);
                    }else if($i->type=='pdf'){
                        $i->setAttribute('pdf_url',config('app.url').'/'.$i->path);
                        $i->setAttribute('path',config('app.url').'/img/pdf_icon.jpg');
                    }else if($i->type=='video'){
                        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $i->youtube_link, $matches);
                        $i->setAttribute('youtube_img','https://img.youtube.com/vi/'.$matches[0].'/0.jpg');
                        $i->setAttribute('id_youtube',$matches[0]);
                    }

                }
                $response = array(
                    'status' => 'success',
                    'data' => $data,
                    'code' => 0
                );

                return response()->json($response);
                break;
            case 'download':
                $id = $request->id;
                if(!isset($id)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Error Id vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }

                if(!$r=db_images::where('id',$id)->exists()){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'No existe id',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $data = db_images::where('id',$id)->first();
                $path = public_path();

                return response()->download($path.'/'.$data->path);
                break;
        }

    }

    public function delete(Request $request){
        $id = $request->id;
        if(!isset($id)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Error Id vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        if(!$r=db_images::where('id',$id)->exists()){
            $response = array(
                'status' => 'fail',
                'msj' => 'No existe id',
                'code' => 5
            );

            return response()->json($response);
        }

        db_images::where('id',$id)->delete();
        $response = array(
            'status' => 'success',
            'msj' => 'Se elimino',
            'code' => 0
        );

        return response()->json($response);
    }

    public function save(Request $request){
        $id = $request->id;
        $id_user = $request->id_user;

        if(!isset($id)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Error Id vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        $name = $request->name;
        $id_categorie = $request->id_categories;

        if(!isset($name)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Error nombre vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        $values = array(
            'name' => $name,
            'id_categories' => $id_categorie,
            'id_user' => $id_user,
            'type' => $request->type,
        );

        db_images::where('id',$id)->update($values);

        $response = array(
            'status' => 'success',
            'msj' => 'Se actualizo',
            'code' => 0
        );

        return response()->json($response);

    }

    public function all(Request $request){

        $data = db_images::all();
        foreach ($data as $i){
            $i->setAttribute('path',config('app.url').'/'.$i->path);
        }
        $response = array(
            'status' => 'success',
            'data' => $data,
            'code' => 0
        );

        return response()->json($response);
    }

    public function optimize($id=null,$w=300,$h=300){
        if(!extension_loaded('gd')){
            $path = public_path().'/img/not_found.jpg';
            return response()->file($path);
            //return response()->json($response);
        }

        if(is_null($id)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID media vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        if(is_numeric($id)){
            if(!db_images::where('id',$id)->exists()){
                $response = array(
                    'status' => 'fail',
                    'msj' => 'Imagen No existe id',
                    'code' => 5
                );

                return response()->json($response);
            }

            $path = db_images::where('id',$id)->first();
        }else{
            $id = explode(".", $id);
            if(!db_images::where('name',$id[0])->exists()){
                $response = array(
                    'status' => 'fail',
                    'msj' => 'Imagen No existe str',
                    'data' => db_images::all(),
                    'str' => $id,
                    'code' => 5
                );

                return response()->json($response);
            }

            $path = db_images::where('name',$id[0])->first();
        }


        $path = public_path().'/'.$path->path;

        $img = Image::make($path);
        $img->fit($w, $h);
        return $img->response('jpg');
    }
}

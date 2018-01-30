<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\db_categories;
use App\db_images;
class categoriesController extends Controller
{
    public function create(Request $request){
        $name = $request->name;
        $description = $request->description;

        if(!isset($name)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Nombre Vacio',
                'code' => 0
            );
            return response()->json($response);
        }
        $values = array(
            'name' => $name,
            'description' => $description
        );
        $id = db_categories::insertGetId($values);

        $response = array(
            'status' => 'success',
            'msj' => 'Se agrego correctamente',
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
        switch ($act) {
            case 'once':
                $id = $request->id;
                if(!isset($id)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Id vacio',
                        'code' => 0
                    );
                    return response()->json($response);
                }

                $data = db_categories::where('id',$id)->first();

                $response = array(
                    'status' => 'success',
                    'msj' => '',
                    'data' => $data,
                    'code' => 0
                );
                return response()->json($response);
                break;
            case 'all':
                $data = db_categories::all();

                $response = array(
                    'status' => 'success',
                    'msj' => '',
                    'data' => $data,
                    'code' => 0
                );
                return response()->json($response);
        }

    }

    public function delete(Request $request){
        $id = $request->id;
        if(!isset($id)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Id vacio',
                'code' => 0
            );
            return response()->json($response);
        }

        $data = db_categories::where('id',$id)->delete();

        $response = array(
            'status' => 'success',
            'msj' => 'Se elimino',
            'code' => 0
        );
        return response()->json($response);
    }

    public function edit(Request $request){
        $id = $request->id_cat;
        $name = $request->name;
        $description = $request->description;


        if(!isset($id)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        if(!isset($name)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Name vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        if(!isset($description)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Description vacio',
                'code' => 5
            );
            return response()->json($response);
        }

        $values = array(
            'name' => $name,
            'description' => $description
        );


        db_categories::where('id',$id)->update($values);


        $response = array(
            'status' => 'success',
            'msj' => 'Se actualizo correctamente',
            'code' => 5
        );
        return response()->json($response);

    }

    public function assign(Request $request){

        $act = $request->act;
        if(!isset($act)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Act invalido',
                'code' => 5,
            );

            return response()->json($response);
        }

        switch ($act){
            case 'assing':
                $ids = $request->ids;
                $id_categories = $request->id_categories;
                if(count($ids)<1){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'No hay fotos para asignar',
                        'code' => 5
                    );
                    return 'algo';
                    return response()->json($response);
                }

                foreach($ids as $i){
                    db_images::where('id',$i)->update(['id_categories'=>$id_categories['id']]);
                }

                $response = array(
                    'status' => 'success',
                    'msj' => 'Si',
                    'code' => 0,
                    'algo' => $ids
                );
                return response()->json($response);
                break;
        }
    }
}

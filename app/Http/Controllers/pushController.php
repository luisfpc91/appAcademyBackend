<?php

namespace App\Http\Controllers;

use App\db_history_push;
use App\db_tokens;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class pushController extends Controller
{
    public function send(Request $request){

        $title = $request->title;
        $body = $request->body;

        $act = $request->act;
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = 'AAAAb5xea_o:APA91bG1L6pSdIvs44mmceg7BdwprltIHevs-A8sZi2JQDFBxGQsd4bZWVs-9pZ3ehOBUokG-Y36weWyO1RKJ09TvGrDG0LU8hP4KMuwoBguhUWG70ces2hpnWOUg5MewAqUiSL2Tey3';


        if(!isset($act)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Act invalido',
                'code' => 5
            );

            return response()->json($response);
        }

        if(!isset($title)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Title vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        if(!isset($body)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Message vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        switch ($act){
            case 'device':
                $id_user = $request->id_user;
                if(!isset($device_token)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'ID token FCM vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $device_token = $request->token_device;
                $token = $device_token;
                $notification = array(
                    'title' =>$title ,
                    'text' => $body,
                    'sound' => 'default',
                    'badge' => '1'
                );
                $arrayToSend = array(
                    'to' => $token,
                    'notification' => $notification,
                    'priority'=>'high'
                );

                $json = json_encode($arrayToSend);
                $headers = array();
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Authorization: key='. $serverKey;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                //Send the request
                //$output = curl_exec($ch);
                //curl_close($ch);

                db_history_push::insert([
                    'message' => $body,
                    'id_sender' => $id_user,
                    'created_at' => Carbon::now(),
                    'title' => $title,
                ]);

                $res = array(
                    'status' => 'success',
                    'msj' => 'Notificacion enviada',
                    'code' => 0,
                );

                //return response()->json($res);
                curl_exec($ch);
                curl_close($ch);


                break;
            case 'topic':
                $topic = $request->topic;
                $id_user = $request->id_user;

                if(!isset($topic)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Tema vacio',
                        'code' => 5
                    );

                    return response()->json($response);
                }

                $notification = array(
                    'title' =>$title ,
                    'text' => $body,
                    'sound' => 'default',
                    'badge' => '1'
                );
                $arrayToSend = array(
                    'to' => '/topics/'.$topic,
                    'notification' => $notification,
                    'priority'=>'high'
                );

                $json = json_encode($arrayToSend);
                $headers = array();
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Authorization: key='. $serverKey;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                //$output = curl_exec($ch);
                //Send the request

                db_history_push::insert([
                    'message' => $body,
                    'id_sender' => $id_user,
                    'id_to' => $topic,
                    'created_at' => Carbon::now(),
                    'title' => $title
                ]);

                curl_exec($ch);
                curl_close($ch);
                //return response()->json($res);


                break;
        }

    }

    public function getfcm(Request $request){
        $act = $request->act;

        if(!isset($act)){
            $response = array(
                'status' => 'fail',
                'msj' => 'Act invalido',
                'code' => 5
            );

            return response()->json($response);
        }

        switch ($act){
            case 'all':
                $id_usr = $request->id_usr;
                if(!isset($id_usr)){
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'ID user invalido',
                        'code' => 5
                    );

                    return response()->json($response);
                }
                $data = db_tokens::where('id_user',$id_usr)->orderBy('id','desc')->get();
                foreach ($data as $usr){
                    $user = User::find($usr->id_user);
                    $usr->setAttribute('user_data',$user);
                }


                $response = array(
                    'status' => 'success',
                    'msj' => '',
                    'data' => $data,
                    'code' => 0
                );

                return response()->json($response);

                break;
            case 'once':
                break;
        }
    }

    public function getall(Request $request){
        $id_user = $request->id_user;
        if(!isset($id_user)){
            $response = array(
                'status' => 'fail',
                'msj' => 'ID user vacio',
                'code' => 5
            );

            return response()->json($response);
        }

        $data=db_history_push::where('id_sender',$id_user)->orderBy('id','desc')->get();

        $response = array(
            'status' => 'successs',
            'msj' => '',
            'data' => $data,
            'code' => 0
        );

        return response()->json($response);
    }
}

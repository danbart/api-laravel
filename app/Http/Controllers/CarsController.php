<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\JwtAuth;
use App\Cars;

class CarsController extends Controller
{
    //
    public function index( Request $request ){
      $cars = Cars::all()->load('user');
      return response()->json(array(
        'cars' => $cars,
        'status' => 'success'
      ), 200);
    }

    // $hash = $request->header('Authorization', null);
    //
    // $jwtAuth = new JwtAuth();
    // $checkToken = $jwtAuth->checkToken($hash);
    // if($checkToken){
    //   echo 'Index de Car Controller Autenticado'; die();
    // }else{
    //   echo "No Autenticado --> Index de CarsController"; die();
    //
    // }

    public function show($id){
      $cars = Cars::find($id)->load('user');
      return response()->json(array(
        'cars' => $cars,
        'status' => 'success'
      ),200);
    }


    public function store( Request $request ){
      $hash = $request->header('Authorization', null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);
      if($checkToken){
        //Recoger el post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        //validation



          $validate = \Validator::make($params_array, [
            'title' => 'required|min:5',
            'description' => 'required',
            'price' => 'required',
            'status' => 'required'
          ]);

          if($validate->fails()){
            return response()->json($validate->errors(), 400);
          }


        //Guardar el Coche
        //enviamos token y true para que devuelva el arreglo del usuario registrado
        $user = $jwtAuth->checkToken($hash, true);
        $car = new Cars();
        $car->user_id = $user->sub;
        $car->title = $params->title;
        $car->description = $params->description;
        $car->price = $params->price;
        $car->status = $params->status;
        $car->save();

        $data = array(
          'car' => $car,
          'status' => 'success',
          'code' => 200
        );

      }else{
      //Devolver el Error
      $data = array(
        'message' => 'Login Incorrecto',
        'status' => 'error',
        'code' => 400
      );
      }

      return response()->json($data, 200);
    }

    public function update($id, Request $request){
      $hash = $request->header('Authorization', null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);
      if($checkToken){
        //recoger parametros post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //validad los datos
        $validate = \Validator::make($params_array, [
          'title' => 'required|min:5',
          'description' => 'required',
          'price' => 'required',
          'status' => 'required'
        ]);

        if($validate->fails()){
          return response()->json($validate->errors(), 400);
        }

        //Actualizar registro
        $car = Cars::where('id', $id)->update($params_array);
        $data = array(
          'car' => $params,
          'status' => 'success',
          'code' => 200
        );

      }else{
        $data = array(
          'message' => 'Login Incorrecto',
          'status' => 'error',
          'code' => 400
        );
      }
          return response()->json($data, 200);
    }

    public function destroy($id, Request $request){
      $hash = $request->header('Authorization', null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);
      if($checkToken){
       //comprobar que exista el registro
       $car = Cars::find($id);
       //borrarlo
       $car->delete();
       //devolverlo
       $data = array(
         'car' => $car,
         'status' => 'success',
         'code' => 200
       );
      }else{
        $data = array(
          'message' => 'Login Incorrecto',
          'status' => 'error',
          'code' => 400
        );
      }
      return response()->json($data, 200);
    }
}

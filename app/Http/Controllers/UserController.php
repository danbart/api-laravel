<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\User;

class UserController extends Controller
{
    //
    public function register(Request $request){
      //Recoger PDOS
      $json = $request->input('json', null);
      $params = json_decode($json);

      $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
      $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
      $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
      $role = 'ROLE_USER';
      $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

      if(!is_null($email) && !is_null($password) && !is_null($name)){

        //creamos e usuario
        $user = new User();
        $user->email = $email;
        $user->name = $name;
        $user->surname = $surname;
        $user->role = $role;

        $pwd = hash('sha256', $password);
        $user->password = $pwd;

        //comprobar usuario duplicado
        $isset_user = User::where('email', '=', $email)->first();
        if(count($isset_user) == 0 ){
          //guardar el usuario
          $user->save();
          $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Usuario registrado correctamente'
          );
        }else{
          //no guardar por que ya existe
          $data = array(
            'status' => 'error',
            'code' => 400,
            'message' => 'Usuario ya Existe y no puede ser creado'
          );
        }
      }else{
        $data = array(
          'status' => 'error',
          'code' => 400,
          'message' => 'Usuario no creado'
        );
      }
      return response()->json($data, 200);
    }
    public function login(Request $request){
      $jwtAuth = new JwtAuth();

      //recibir post
      $json = $request->input('json', null);
      $params = json_decode($json);

      $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
      $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
      $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;

      //Cifrar la contraseña
      $pwd = hash('sha256', $password);

      if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
        $singup = $jwtAuth->singup($email, $pwd);
      }elseif($getToken != null){
        $singup = $jwtAuth->singup($email, $pwd, $getToken);
      }else{
        $singup = array(
          'status' => 'error',
          'message' => 'Envia tus datos por post'
        );
      }

        return response()->json($singup, 200);
    }
}

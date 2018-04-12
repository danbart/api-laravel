<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

  public $key;

  public function __construct(){
    $this->key = 'esta-es-mi-clave-secreta-51651%$#%#$565454$#$53';
  }

  Public function singup($email, $password, $getToken=null){

    $user = User::where(
      array(
        'email' => $email,
        'password' => $password
      ))->first();

      $singup = false;
      if(is_object($user)){
        $singup = true;
      }

      if($singup){
        //generar token y devolver
        $token = array(
          'sub' => $user->id,
          'email' => $user->email,
          'name' => $user->name,
          'surname' => $user->surname,
          //para saber cusndo se creo el token
          'iat' => time(),
          //tiempo de expiracion del token
          'exp' => time() + ( 7 * 24 * 60 * 60 )
        );
        //enviamos nuestros datos en token la llave privada y nuestro algoritmo de encriptacion
        $jwt = JWT::encode($token, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, $this->key, array('HS256'));

        if(is_null($getToken)){
          return $jwt;
        }else{
          return $decoded;
        }

      }else{
        //devolver un error
        return array('status' => 'error', 'message' => 'Login ha Fallado!!');
      }

  }

  public function checkToken($jwt, $getIdentity = false){
    $auth = false;

    try{
      $decoded = JWT::decode($jwt, $this->key, array('HS256'));
    }catch(\UnexpectedValueException $e){
      $auth = false;
    }catch(\DomainException $e){
      $auth = false;
    }

    if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
      $auth = true;
    }else{
      $auth = false;
    }
    if($getIdentity){
      return $decoded;
    }

    return $auth;
  }


}

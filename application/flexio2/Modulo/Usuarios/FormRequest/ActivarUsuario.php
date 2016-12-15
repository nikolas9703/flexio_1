<?php

namespace Flexio\Modulo\Usuarios\FormRequest;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\Empresa\Models\Organizacion;

class ActivarUsuario{
  private $userRepository;

  function __construct(){
    $this->userRepository = new UsuariosRepository();
  }

  /**
   * $datos Array informacion para la activacion de la cuenta de un usuario creado
  */

  function activar($datos){

    $user = null;
    $campos = FormRequest::data_formulario($datos);
    $user = $this->userRepository->validar_token($datos);
    if(is_null($user)){
      return $user;
    }
    if($user->estado == 'Activo') return $user;
    $user->estado = 'Activo';
    $user->save();
    //se crea una organizacion cada vez que se activa un usuario
    //verificar si es miembro
    $org = Organizacion::create(['nombre' => 'Grupo '.random_int( 1 , 1000 )]);
    $user->organizacion()->save($org);
    return $user;

  }

}

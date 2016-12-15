<?php

namespace Flexio\Modulo\Usuarios\FormRequest;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Illuminate\Http\Request;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\AuthUser;

class ActualizarPerfil{

  protected $request;

  function __construct(){
    $this->request = Request::capture();
  }

  function guardar(){
    $campos = FormRequest::data_formulario($this->request->input('campo'));
    $usuario = AuthUser::getUser();
    $usuario->update($campos);
    return $usuario;
  }

}

<?php

namespace Flexio\Library\Util;

use Flexio\Modulo\Usuarios\Models\Usuarios;

class AuthUser{

  protected $session;

  function __construct(){
    //$this->$session = new FlexioSession();
  }

  public static function getUser(){
    $session = FlexioSession::now();
    return Usuarios::find($session->usuarioId());
  }



}

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

  public static function getId(){
      $session = FlexioSession::now();
      return $session->usuarioId();
  }

/**
* @return roles|null
*/
  public static function roles_empresa(){
      $session = FlexioSession::now();
      return self::getUser()->roles->where('pivot.empresa_id',$session->empresaId())->values();
  }
  /**
  * @return boolean
  */
  public static function is_owner(){
      $roles = self::roles_empresa();
      if(is_null($roles)){
          return false;
      }
      $roles_ids = $roles->pluck('id')->all();
      return in_array(2, $roles_ids);
  }


  public static function usuarioCategoriaItems()
    {
        $session = FlexioSession::now();
        $usuario = self::getUser();
        $categorias = $usuario->categorias_inventario->where('empresa_id',$session->empresaId())->values();
        return ($usuario->filtro_categoria == 'todos') ? ['todos'] : array_pluck($categorias, 'id');
    }





}

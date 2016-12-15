<?php namespace Flexio\Politicas;

use Flexio\Modulo\Politicas\Models\Politicas;
use Flexio\Library\Util\AuthUser;

/**
 * Class PoliticableTrait
 * @package Flexio\Politicas
 */
trait PoliticableTrait
{

  function politica(){
    
    $rol = $this->userLogIn();

    if(count($rol)==0){
      return collect([]);
    }


    $empresa_id = !is_null($this->id_empresa)?$this->id_empresa:$this->empresa_id;

    // debe de traer 1 solo rol
    $rol_id = $rol->first()->id;

    $policy = Politicas::where(function($query) use($empresa_id, $rol_id){
      $query->where('modulo',$this->politica);
      $query->where('empresa_id',$empresa_id);
      $query->where('estado_id',1);
      $query->where('role_id',$rol_id);
    })->get();

    if(!is_null($policy))$policy->load('estado_politica','categorias');

    return $policy;
  }

  /*esta function devuelve rol del usuario logeado*/
  function userLogIn(){

    $user = AuthUser::getUser();
    $empresa_id = !is_null($this->id_empresa)?$this->id_empresa:$this->empresa_id;

    //validar
    return $user->roles_reales->where('pivot.empresa_id',$empresa_id)->where('estado',1);
  }

}

<?php

namespace Flexio\Modulo\Empresa\FormRequest;

use Flexio\Library\Util\FormRequest;
use Illuminate\Http\Request;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Usuarios\Models\Usuarios;


class CrearEmpresaRequest{

  protected $request;

  function __construct() {
    $this->request = Request::capture();
  }

  function datos($campos) {

    $dato_empresa = FormRequest::data_formulario($this->request->input('campo'));
    $dato_empresa = array_merge($dato_empresa, $campos);
    if(!isset($dato_empresa['empresa_id']))$dato_empresa['empresa_id']=0;
    if(isset($dato_empresa['id']))return $this->actualizarEmpresa($dato_empresa);
    return $this->crearEmpresa($dato_empresa);
  }

  function crearEmpresa($dato_empresa) {

    $empresa = Empresa::registrar();

    $empresa->fill($dato_empresa)->save();

    $usuario = Usuarios::find($dato_empresa['usuario_id']);
    $roles_empresa = $usuario->roles()->where('usuarios_has_roles.empresa_id', 0)->first();

    if(!is_null($roles_empresa))
    {
        $roles_empresa->pivot->empresa_id = $empresa->id;
        $roles_empresa->pivot->save();
    }else{
      foreach($usuario->roles->unique() as $rolId){
        $usuario->roles()->attach($rolId,array('empresa_id'=>$empresa->id));
      }
    }

    if($usuario->empresas()->count() == 0){
       $usuario->empresas()->attach($empresa->id,array('default'=>1));
    }else{
        $usuario->empresas()->attach($empresa->id);
    }

    $usuario->owenerEmpresa()->save($empresa);

    return $empresa;
  }

  function actualizarEmpresa($dato_empresa) {
    $empresa = Empresa::find($dato_empresa['id']);
    $empresa->update($dato_empresa);
    return $empresa;
  }
}

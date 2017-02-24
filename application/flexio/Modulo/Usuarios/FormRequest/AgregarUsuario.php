<?php

namespace Flexio\Modulo\Usuarios\FormRequest;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\Utiles;
use Flexio\Library\Util\FlexioSession;

class AgregarUsuario{

  protected $request;

  function __construct(){
    $this->request = Request::capture();
  }


  protected function getCamposUsuario(){
    $campos = $this->request->only('nombre','apellido','email','empresa_id');
    $campos['usuario'] = $campos['email'];
    return $campos;
  }

  protected function getRoles(){

    $roles = [
              $this->request->input('rol'),
              $this->request->input('roles')
            ];
    return $roles;
  }

  protected function getCentrosContables()
  {
      return $this->request->input('centros_contables') ? : [];
  }

  protected function getCategorias(){
      return $this->request->input('categorias') ? : [];
  }

  protected function getTiposSubcontrato(){
      return $this->request->input('tipos_subcontrato') ? : [];
  }

  function addUsuario($campos){

    $data_usuario = $this->getCamposUsuario();

    $email = strtolower($data_usuario['email']);
    $usuario = Usuarios::where('email', $email)->first();

    if(is_null($usuario)){
      //el usuario es nuevo
      return $this->nuevoUsuario($data_usuario, $campos);
    }

    return $this->agregarUsuarioExistente($data_usuario, $usuario);

  }

  public function nuevoUsuario($data_usuario, $campos){

      $roles = $this->getRoles();
      $centros_contables = $this->getCentrosContables();
      $tipos_subcontrato = $this->getTiposSubcontrato();

      $guardar =  Capsule::transaction(function() use($data_usuario, $campos, $roles, $centros_contables, $tipos_subcontrato){
          $usuario = Usuarios::registrar();
          $data_usuario['password'] = $campos["password"];
          $data_usuario["estado"] = 'Activo';
          $data_usuario["fecha_creacion"] = date('Y-m-d H:i:s');
          $data_usuario['recovery_token'] = Utiles::generate_ramdom_token();
          $usuario->fill($data_usuario)->save();


      //sino pertenese a ninguna empresa se agregar y se selecciona por default
      if($usuario->empresas()->count() == 0){
        $usuario->empresas()->attach($data_usuario['empresa_id'],array('default'=>1));
      }else{
        $usuario->empresas()->attach($data_usuario['empresa_id']);
      }

      //Guardar roles
      $usuario->roles()->attach($roles,array('empresa_id'=>$data_usuario['empresa_id']));

      //Guardar centros contables
      if(!in_array("todos", $centros_contables))
      {
          $usuario->centros_contables()->attach($centros_contables,array('empresa_id'=>$data_usuario['empresa_id']));
      }
      else
      {
          $usuario->filtro_centro_contable = 'todos';
          $usuario->save();
      }
      $usuario = $this->GuardarUsuarioCategoria($usuario);

      //Guardar tipos de subcontratos, si existen
      if(!empty($tipos_subcontrato)){
        $usuario->tipos_subcontrato()->attach($tipos_subcontrato,array('empresa_id'=>$data_usuario['empresa_id']));
      }

      $respuesta = array(
        'error' => false,
        'nuevo' => true,
        'mensaje' => 'El usuario se registro en la Empresa',
        'usuario' => $usuario
      );
      return $respuesta;
    });

     return $guardar;
  }

    public function agregarUsuarioExistente($data_usuario, $usuario)
    {
        $rolesPost = $this->getRoles();
        $centros_contables = $this->getCentrosContables();
        $tipos_subcontrato = $this->getTiposSubcontrato();
        $guardar =  Capsule::transaction(function() use($data_usuario, $rolesPost, $usuario, $centros_contables, $tipos_subcontrato){
            $usuarioObj = $usuario;
            $usuario->fill($data_usuario)->save();
            $usuario = $usuarioObj->fresh();
            $roles = [];
            //                 dd($usuario, $data_usuario);
            $roles_usuarios = RolesUsuario::where('usuario_id', $usuario->id)->where("empresa_id", $data_usuario['empresa_id'])->get();
            if(!is_null($roles_usuarios)){
                $roles = $roles_usuarios->toArray();
                $roles = !empty($roles) ? array_map(function($roles){ return $roles["role_id"]; }, $roles) : [];
            }

            //Eliminar los Roles que ya no tiene
            foreach ($roles as $rol_id){
                if(!in_array($rol_id, $rolesPost)){
                    RolesUsuario::where(function($query) use($rol_id, $usuario, $data_usuario){
                        $query->where('role_id', $rol_id);
                        $query->where('usuario_id', $usuario->id);
                        $query->where("empresa_id", $data_usuario['empresa_id']);
                    })->delete();
                }
            }

            //Insertar nuehvo rol si ha seleccionado uno diferente
            $nuevo_roles = array_diff($rolesPost, $roles);
            if(!empty($nuevo_roles)){
                //Guardar Roles
                $usuario->roles()->attach($nuevo_roles, array('empresa_id'=>$data_usuario['empresa_id']));
            }
            //Guardar tipos de subcontratos, si existen
            if(!empty($tipos_subcontrato)){
              $actual_tipos_subcontrato = $usuario->tipos_subcontrato->where('empresa_id',$data_usuario['empresa_id'])->values();
              $usuario->tipos_subcontrato()->detach($actual_tipos_subcontrato);
              $usuario->tipos_subcontrato()->attach($tipos_subcontrato,array('empresa_id'=>$data_usuario['empresa_id']));
            }
            /// guardar getCategorias
            $usuario = $this->GuardarUsuarioCategoria($usuario);
            //Guardar centros contables
            $todos = in_array("todos", $centros_contables);
            $aux = [];
            foreach($centros_contables as $centro_contable)
            {
                if(!$todos)
                {
                    $aux[$centro_contable] = ['empresa_id' => $data_usuario['empresa_id']];
                }

            }
            $usuario->centros_contables()->sync($aux);
            $usuario->filtro_centro_contable = $todos ? 'todos' : '';
            $usuario->save();

            if(!in_array($data_usuario['empresa_id'], $usuario->empresas->pluck('id')->all())){
                $usuario->empresas()->attach($data_usuario['empresa_id']);
            }


            $respuesta = array(
                'error' => false,
                'nuevo' => false,
                'mensaje' => 'Se ha actualizado el usuario satisfactoriamente',
                'usuario' => $usuario
            );
            return $respuesta;
        });
        return $guardar;
    }

    protected function GuardarUsuarioCategoria($usuario){
        $categorias = $this->getCategorias();

        $session = FlexioSession::now();

      if(!in_array("todos", $categorias)) {
           $actual_categoria = $usuario->categorias_inventario->where('empresa_id',$session->empresaId())->values();
           $usuario->categorias_inventario()->detach($actual_categoria);
           $usuario->categorias_inventario()->attach($categorias,array('empresa_id'=>$session->empresaId()));
           $usuario->update(["filtro_categoria"=>'']);
      }
      else {
           $categoria = $usuario->categorias_inventario->where('empresa_id',$session->empresaId())->values();
           $usuario->categorias_inventario()->detach( $categoria);
           $usuario->update(["filtro_categoria"=>'todos']);
      }
      return $usuario;
    }

}

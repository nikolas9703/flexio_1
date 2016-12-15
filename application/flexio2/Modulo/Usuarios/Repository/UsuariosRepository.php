<?php namespace Flexio\Modulo\Usuarios\Repository;

use Flexio\Modulo\Usuarios\Models\Usuarios;

class UsuariosRepository{

    public function create($data){
        $usuario = new Usuarios;

        $usuario->nombre            = $data['nombre'];
        $usuario->apellido          = $data['apellido'];
        $usuario->email             = $data['email'];
        $usuario->usuario           = $data['email'];
        $usuario->password          = $data['password'];
        $usuario->estado            = $data['estado'];
        $usuario->recovery_token    = $data['recovery_token'];
        $usuario->last_login        = $data['last_login'];
        $usuario->fecha_creacion    = $data['fecha_creacion'];
        $usuario->last_login_ip_address = $data['last_login_ip_address'];
        $usuario->ip_address            = $data['ip_address'];

        $usuario->save();

        return $usuario;
    }


    private function _filtros($query, $clause)
    {
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->deEmpresa($clause['empresa_id']);}
        if(isset($clause['vendedor']) and $clause['vendedor']){$query->vendedor($clause['empresa_id']);}
        if(isset($clause['comprador']) and $clause['comprador']){$query->comprador($clause['empresa_id']);}
    }

    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $usuarios = Usuarios::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        if($sidx !== null && $sord !== null){$usuarios->orderBy($sidx, $sord);}
        if($limit != null){$usuarios->skip($start)->take($limit);}
        return $usuarios->get();
    }
    
    public function getCollectionUsuarios($usuarios){
        
        return $usuarios->map(function($usuario){
            
            return [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre_completo
            ];
            
        });
        
    }

    public function validar_token($clause){
      return Usuarios::where(function($query) use ($clause){
        $query->where('recovery_token',$clause['recovery_token']);
        $query->where('estado',$clause['estado']);
        })->orWhere(function($query) use ($clause){
        $query->where('estado','Activo');
        $query->where('recovery_token',$clause['recovery_token']);
        })->first();
    }

    public function findByUuid($uuid){
      return Usuarios::where('uuid_usuario',hex2bin($uuid))->first();
    }

    function rolVendedor($clause){
        return Usuarios::whereHas('roles',function($query) use($clause){
          $query->where('roles.nombre','like','%vendedor%');
          $query->where('roles.empresa_id','=',$clause['empresa_id']);
      })->activo()->get(['id','nombre','apellido']);
    }

    public function findIds($ids=null){
        return Usuarios::whereIn('id',$ids)->get();
    }

}

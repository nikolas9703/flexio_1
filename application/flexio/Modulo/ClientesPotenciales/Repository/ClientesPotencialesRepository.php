<?php
namespace Flexio\Modulo\ClientesPotenciales\Repository;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\ClientesPotenciales\Models\ClientesPotenciales;
use Flexio\Modulo\ClientesPotenciales\Models\Correos;
use Flexio\Modulo\ClientesPotenciales\Models\Telefonos;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;

class ClientesPotencialesRepository{
    protected $request;

    public function __construct()
    {
        $this->request = Request::capture();
     }


     public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null) {
         $clientes_potenciales = ClientesPotenciales::where(function($query) use ($clause){

             $this->_filtros($query, $clause);

         })->where('deleted_at', '=', null);

         if($sidx !== null && $sord !== null){$clientes_potenciales->orderBy($sidx, $sord);}
         if($limit != null){$clientes_potenciales->skip($start)->take($limit);}
         return $clientes_potenciales->get();
     }

     public function count($clause = array()) {
         $clientes_potenciales = ClientesPotenciales::where(function($query) use ($clause){

             $this->_filtros($query, $clause);

         });

         return $clientes_potenciales->count();
     }

    private function _filtros($clientes_potenciales, $clause) {
        
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$clientes_potenciales->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['nombre']) and !empty($clause['nombre'])){$clientes_potenciales->DeNombre($clause['nombre']);}
        if(isset($clause['uuid_cliente_potencial']) and !empty($clause['uuid_cliente_potencial'])){$clientes_potenciales->whereUuidClientePotencial(hex2bin($clause['uuid_cliente_potencial']));}
        if(isset($clause['id_cliente_potencial']) and !empty($clause['id_cliente_potencial'])){$clientes_potenciales->whereIdClientePotencial($clause['id_cliente_potencial']);}
        if(isset($clause['telefono']) and !empty($clause['telefono'])){
            $clientes_potenciales->DeTelefonos($clause['telefono']);
        }
        if(isset($clause['correo']) and !empty($clause['correo'])){
            $clientes_potenciales->DeCorreos($clause['correo']);
        }
        
    }

    function getAll($clause,$columns=['*']) {
  		return ClientesPotenciales::where(function ($query) use($clause, $columns) {
  			$query->where('empresa_id', '=', $clause['empresa_id']);
                        if(isset($clause['cliente_id'])&&!empty('cliente_id'))$query->where('id', '=', $clause['cliente_id']);
                        if(isset($clause['q'])&&!empty($clause['q']))$query->where("nombre",'like',"%".$clause['q']."%");
  		})->get($columns);
  	}

    function find($id) {
    	return ClientesPotenciales::find($id);
    }


    public function findBy($clause) {
        $cliente_potencial = ClientesPotenciales::whereEmpresaId($clause['empresa_id']);

        //filtros
        $this->_filtros($cliente_potencial, $clause);

        return $cliente_potencial->first();
     }



    public function getCollectionClientesPotenciales($clientes_potenciales){

        $_SERVER['REQUEST_URI_PATH'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', $_SERVER['REQUEST_URI_PATH']);
        $oportunidad_id = '';

        if (isset($segments[3]) && preg_match('/oportunidad/', $segments[3])) {

            $oportunidad_id = str_replace('oportunidad', '',  $segments[3]);

        }

        return $clientes_potenciales->map(function($cliente_potencial) use ($oportunidad_id){
            return [
                'id' => $cliente_potencial->id_cliente_potencial,
                'nombre' => "{$cliente_potencial->nombre}",
                'cliente_id' => $cliente_potencial->id_cliente_potencial,
                'centros_facturacion' => [],
                'centro_facturacion_id' => '',
                'oportunidad_id' => $oportunidad_id
            ];
        });

    }

    public function delete($clause) {
        $cliente_potencial = ClientesPotenciales::whereEmpresaId($clause['empresa_id']);

        //filtros
        $this->_filtros($cliente_potencial, $clause);

        return $cliente_potencial->delete();
    }

    public function _update($post, $cliente_potencial ) {

        $cliente_potencial->nombre              = $post["nombre"];
        $cliente_potencial->id_toma_contacto    = $post["id_toma_contacto"];
        $cliente_potencial->comentarios         = $post["comentarios"];

        $this->_relationships($cliente_potencial);

        return $cliente_potencial->save();
    }

    private function _create($tabla_cliente_potencial)
    {
        return  Capsule::transaction(function() use($tabla_cliente_potencial){

            $cliente_potencial = ClientesPotenciales::create($tabla_cliente_potencial);
            $this->_relationships($cliente_potencial);

             return $cliente_potencial;
        });
    }
    public function guardar($campos, $cliente_potencial = array())
    {

      $tabla_cliente_potencial = $campos['campo'];

      if(isset($cliente_potencial['id_cliente_potencial'])){
          return $this->_update($tabla_cliente_potencial, $cliente_potencial );
      }
      else{
         unset($cliente_potencial['id_cliente_potencial']);
         return $this->_create($tabla_cliente_potencial);
      }
    }

    private function _relationships($cliente_potencial)
    {
       //Limpiar la data de telefonos y correos
        $cliente_potencial->telefonos_asignados()->delete();
        $cliente_potencial->correos_asignados()->delete();

        $telefonos = $this->request->input('telefonos') ? : [];
        $correos = $this->request->input('correos') ? : [];

        $cliente_potencial->telefonos_asignados()->saveMany($this->collectionMutatorTelefonos($telefonos));
        $cliente_potencial->correos_asignados()->saveMany($this->collectionMutatorCorreos($correos));

    }

    private function collectionMutatorTelefonos($telefonos)
    {
       $telefono_lista = [];

      foreach($telefonos as $telefono)
      {
            $telefono_lista[] 				= new Telefonos([
               "tipo" => $telefono["tipo"],
               "telefono" => $telefono["telefono"]
           ]);
       }
       return $telefono_lista;
    }

    private function collectionMutatorCorreos($correos)
    {
       $correo_lista = [];

      foreach($correos as $correo)
      {
           	$correo_lista[] 				= new Correos([
               "tipo" => $correo["tipo"],
               "correo" => $correo["correo"]
           ]);
       }
       return $correo_lista;
    }

 function agregarComentario($ordenId, $comentarios) {
   	$cliente = ClientesPotenciales::find($ordenId);
  	$comentario = new Comentario($comentarios);
    $cliente->comentario_timeline()->save($comentario);
  	return $cliente;
  }

}

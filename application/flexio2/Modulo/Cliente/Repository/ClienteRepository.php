<?php
namespace Flexio\Modulo\Cliente\Repository;

use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Cliente\Models\CatalogoTomaContacto;
//utils
use Flexio\Library\Util\FlexioSession;

class ClienteRepository
{

  protected $FlexioSession;

  public function __construct()
  {
    $this->FlexioSession = new FlexioSession;
  }

	function find($id) {
		return Cliente::find($id);
	}

  function getTomaContacto() {
		return CatalogoTomaContacto::all();
	}



	function getAll($clause,$columns=['*']) {
		return Cliente::where(function ($query) use($clause, $columns) {
			$query->where('empresa_id', '=', $clause['empresa_id']);
		})->get($columns);
	}

    function getClientesEstadoIP($clause) {
        return Cliente::where(function ($query) use($clause) {
			$query->where('empresa_id', '=', $clause['empresa_id'])
                  ->where('estado', '!=', 'por_aprobar')
                  ->where('estado', '!=', 'inactivo');
		});
    }
    function getClientesEstadoActivo($clause) {
        return Cliente::where(function ($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id'])
                ->where('estado', '=', 'activo');
        });
    }
   /* function getIdentificacionClientes($clause) {
        return Cliente::where(function ($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id'])
                ->where('identificacion', '=', $clause['identificacion']);
        });
    }*/
    function getClientesPorTipo($clause) {
        return Cliente::where(function ($query) use($clause) {
			$query->where('empresa_id', '=', $clause['empresa_id']);
			$query->where('tipo_identificacion', '=', $clause['tipo_identificacion']);
                        $query->orderBy('nombre', 'ASC');
		});
    }

    public static function findByUuid($uuid) {
        return Cliente::where('uuid_cliente', hex2bin($uuid))->first();
    }

    private function _filtros($query, $clause) {
        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
    }
    public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null) {
        $clientes = Cliente::where(function($query) use ($clause){

            $this->_filtros($query, $clause);

        });

        if($sidx !== null && $sord !== null){$clientes->orderBy($sidx, $sord);}
        if($limit != null){$clientes->skip($start)->take($limit);}
        return $clientes->get();
    }
      public function getCollectionCliente($clientes){
         return $clientes->map(function($cliente) {
             //$centro_facturable = $cliente->centro_facturable;
             return [
                'id' => $cliente->id,
                'cliente_id' => $cliente->id,
                'nombre' => "{$cliente->nombre}",
                'empezar_desde_id' => $cliente->id,
                'fecha_inicio'=> date("d/m/Y"),
                'saldo' =>$cliente->saldo_pendiente,
                'credito'=>$cliente->credito_favor,
                'centros_facturacion'=>$cliente->centro_facturable,
             ];
        });

    }
    public function getCollectionClientes($clientes)
    {

        $segmento3 = $this->FlexioSession->uri()->segment(3, '');
        $oportunidad_id = str_replace('oportunidad', '',  $segmento3);
        return $clientes->map(function($cliente) use ($oportunidad_id){

            $centro_facturable = $cliente->centro_facturable;
            return [
                'id' => $cliente->id,
                'nombre' => "{$cliente->codigo} - {$cliente->nombre}",
                'cliente_id' => $cliente->id,
                'centros_facturacion' => $centro_facturable,
                'centro_facturacion_id' => count($centro_facturable) == 1 ? $centro_facturable->first()->id : '',
                'oportunidad_id' => $oportunidad_id

            ];
        });

    }

  function agregarComentario($ordenId, $comentarios) {
  	$cliente = Cliente::find($ordenId);
 	  $comentario = new Comentario($comentarios);
    $cliente->comentario_timeline()->save($comentario);
  	return $cliente;
  }
  /**
   * [clienteCatalogo description]
   * @param  [Illuminate\Database\Eloquent\Model] $clientes [description]
   * @return [Illuminate\Database\Eloquent\Collection] devuelve la collection de clientes
   */
  function clienteCatalogo($clientes){
    return $clientes->map(function($cliente){
        return collect([
            'id' => $cliente->id,
            'nombre' => "{$cliente->codigo} - {$cliente->nombre}",
            'cliente_id' => $cliente->id,
            'saldo_pendiente' =>$cliente->saldo_pendiente,
            'credito'=>$cliente->credito_favor,
        ]);
    });
  }


}

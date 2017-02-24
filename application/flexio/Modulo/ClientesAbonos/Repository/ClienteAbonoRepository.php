<?php
namespace Flexio\Modulo\ClientesAbonos\Repository;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\ClientesAbonos\Models\ClienteAbono;
use Carbon\Carbon as Carbon;


class ClienteAbonoRepository
{
    public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $clientes = ClienteAbono::where(function($query) use($clause){
            if(isset($clause['campo']) && !empty($clause['campo'])){$query->deFiltro($clause['campo']);}
            if(isset($clause['cliente_id']) && !empty($clause['cliente_id'])){$query->whereClienteId($clause['cliente_id']);}
            $query->where('empresa_id','=',$clause['empresa_id']);
    	});

        return $clientes->get();
    }

    public function listar_totales($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $clientes = ClienteAbono::where(function($query) use($clause){
            if(isset($clause['campo']) && !empty($clause['campo'])){$query->deFiltro($clause['campo']);}
            if(isset($clause['cliente_id']) && !empty($clause['cliente_id'])){$query->whereClienteId($clause['cliente_id']);}
            $query->where('empresa_id','=',$clause['empresa_id']);
    	});

        return $clientes->count();
    }

    function guardar(Cobro_orm $cobro,$posts){
    $array_cobro = Util::set_fieldset("campo");
    $array_cobro['fecha_abono'] = $posts['campo']['fecha_abono'];
    $array_cobro['fecha_desde'] = Carbon::createFromFormat('m/d/Y',$array_cobro['fecha_abono'],'America/Panama');
    $array_cobro['empresa_id'] = $this->empresa_id;
    $total = Cobro_orm::where('empresa_id','=',$this->empresa_id)->count();
    $year = Carbon::now()->format('y');
    $codigo = Util::generar_codigo('PAY'.$year,$total + 1);
    $array_cobro['codigo'] = $codigo;

    $cobro = Cobro_orm::create($array_factura);
    $cobro = $cobro->fresh();
    return $cobro;
  }

  function tipo_abono($metodo_abono, array $item)
    {
        $tipo = array();

        if($metodo_abono == 'cheque')
        {
            $tipo = array('numero_cheque' => 'E.D.','nombre_banco_cheque'=> 'E.D.');
        }
        elseif($metodo_abono == 'ach')
        {
            $tipo = array('nombre_banco_ach' => $item['nombre_banco_ach'],'cuenta_proveedor'=> $item['cuenta_proveedor']);
        }
        elseif($metodo_abono == 'tarjeta_de_credito')
        {
            $tipo = array('numero_tarjeta' => $item['numero_tarjeta'],'numero_recibo'=> $item['numero_recibo']);
        }

        return  empty($tipo)? '' : json_encode($tipo);
    }

}

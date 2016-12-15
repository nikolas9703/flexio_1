<?php
namespace Flexio\Modulo\Contratos\Repository;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Flexio\Modulo\Contratos\Models\ContratoTipo as ContratoTipo;
use Flexio\Modulo\Contratos\Models\ContratoMonto as ContratoMonto;
//use Flexio\Repository\InterfaceRepository as InterfaceRepository;

class ContratoRepository implements ContratoInterface{
  protected $contrato;

  function findBy($id)
  {
    return Contrato::find($id);
  }

  function getContratos($clause){
    return Contrato::where('empresa_id', '=', $clause['empresa_id'])->get();
  }

  function conFacturas($empresa_id){
   return Contrato::whereHas('facturas',function($query) use($empresa_id){
      $query->where('cont_contratos.empresa_id','=',$empresa_id);
      $query->where('fac_facturas.estado','=','por_cobrar');
    })->get();

  }

  function conFacturasVer($empresa_id){
   return Contrato::whereHas('facturas',function($query) use($empresa_id){
      $query->where('cont_contratos.empresa_id','=',$empresa_id);
      $query->whereIn('fac_facturas.estado',['cobrado_parcial','cobrado_completo']);
    })->get();

  }

  function create($created){
    $array_monto = [];
    $contrato = $created['contrato'];
    $contrato_abono = $created['abono'];
    $contrato_retenido = $created['retenido'];
    $contrato_monto = $created['montos'];

    $model_contrato = Contrato::create($contrato);

    $abono = new ContratoTipo($contrato_abono);

    $model_contrato->tipo_abono()->save($abono);
    $retenido = new ContratoTipo($contrato_retenido);
    $model_contrato->tipo_retenido()->save($retenido);

    foreach($contrato_monto as $monto){
      $array_monto[] = new ContratoMonto($monto);
    }
   //dd($model_contrato);
    $model_contrato->contrato_montos()->saveMany($array_monto);

    return $model_contrato;
  }

  function update($update){

  }

  function findByUuid($uuid){
    return Contrato::where('uuid_contrato',hex2bin($uuid))->first();
  }

  function lista_totales($clause=array())
  {
    return Contrato::where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
      if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
      if(isset($clause['monto_original']))$query->where('monto_contrato','=' ,$clause['monto_original']);
      if(isset($clause['codigo']))$query->where('codigo','=',$clause['codigo']);
			if(isset($clause['centro_id']))$query->where('centro_id','=',$clause['centro_id']);
    })->count();
  }

  function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
  {
    $contratos = Contrato::where(function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
        if(isset($clause['cliente_id']))$query->where('cliente_id','=' ,$clause['cliente_id']);
        if(isset($clause['id']))$query->where('id','=' ,$clause['id']);
        if(isset($clause['monto_original']))$query->where('monto_contrato','=' ,$clause['monto_original']);
        if(isset($clause['codigo']))$query->where('codigo','like',"%".$clause['codigo']."%");
  			if(isset($clause['centro_id']))$query->where('centro_id','=',$clause['centro_id']);
    });
    if($sidx!==NULL && $sord!==NULL) $contratos->orderBy($sidx, $sord);
    if($limit!=NULL) $contratos->skip($start)->take($limit);
    return $contratos->get();
  }

}

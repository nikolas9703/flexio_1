<?php
namespace Flexio\Modulo\SubContratos\Repository;

use Flexio\Modulo\SubContratos\Models\SubContrato      as SubContrato;
use Flexio\Modulo\SubContratos\Models\SubContratoTipo  as SubContratoTipo;
use Flexio\Modulo\SubContratos\Models\SubContratoMonto as SubContratoMonto;
use Flexio\Modulo\Comentario\Models\Comentario;
//use Flexio\Repository\InterfaceRepository as InterfaceRepository;

class SubContratoRepository
{
    protected $subcontrato;

    public function findBy($id)
    {
        return SubContrato::find($id);
    }

    private function _filtros($query, $clause)
    {
        if(isset($clause['empresa_id']) && !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['id']) && !empty($clause['id'])){$query->whereId($clause['id']);}
    }

    public function getSubContratos($clause)
    {
        return SubContrato::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        })->get();
    }

    public function conFacturas($empresa_id)
    {
        return SubContrato::whereHas('facturas',function($query) use($empresa_id){
            $query->where('sub_subcontratos.empresa_id','=',$empresa_id);
            $query->where('fac_facturas.estado','=','por_cobrar');
        })->get();

    }

    public function conFacturasVer($empresa_id)
    {
        return SubContrato::whereHas('facturas',function($query) use($empresa_id){
            $query->where('sub_subcontratos.empresa_id','=',$empresa_id);
            $query->whereIn('fac_facturas.estado',['cobrado_parcial','cobrado_completo']);
        })->get();

    }

    public function create($created)
    {
        $subcontrato = $created['campo'];
        $subcontrato_montos = $created['items'];
        $subcontrato_movimientos = $created['movimientos'];

        if(empty($subcontrato['id']))
        {
             $model_subcontrato = SubContrato::create($subcontrato);
        }
        else
        {
          $model_subcontrato = SubContrato::find($subcontrato['id']);
          $model_subcontrato->subcontrato_montos()->delete();
          $model_subcontrato->tipo()->delete();

          $model_subcontrato->update($subcontrato);
         }

        if(count($subcontrato_montos) > 0){
          foreach($subcontrato_montos as $monto)
          {
              $array_monto[] = new SubContratoMonto($monto);
          }
          $model_subcontrato->subcontrato_montos()->saveMany($array_monto);
        }

        if(count($subcontrato_movimientos) > 0){
          //movimientos
          foreach($subcontrato_movimientos as $movimiento)
          {
              $array_movimiento[] = new SubContratoTipo($movimiento);
          }
          $model_subcontrato->tipo()->saveMany($array_movimiento);
        }


        return $model_subcontrato;
    }

  public function update($update){

    }

    public function getCollectionSubcontrato($subcontrato)
    {
        $monto_adenda = $subcontrato->adenda->sum(function($adenda){
          return $adenda->adenda_montos->sum('monto');
        });

        return Collect(array_merge(
            $subcontrato->toArray(),
            [
                'movimientos' => $subcontrato->tipo,
                'montos' => $subcontrato->subcontrato_montos,
                "proveedor" => count($subcontrato->proveedor)?$this->formatProveedor($subcontrato->proveedor):[],
                'monto_adenda' => $monto_adenda
            ]
        ));
    }


    public function getCollectionSubcontratos($subcontratos){

        return $subcontratos->map(function($subcontrato){

            return $this->getSubContrato($subcontrato);

        });

    }

    public function getCollectionSubcontratosAjax($subcontratos){

        return $subcontratos->map(function($subcontrato){

            return [
                'id' => $subcontrato->id,
                'nombre' => $subcontrato->proveedor->nombre .' - '.$subcontrato->codigo
            ];

        });

    }

    public function getSubContrato($subcontrato)
    {
        $articulo = new \Flexio\Library\Articulos\SubcontratoArticulo;

        return [
            'id' => $subcontrato->id,
            'nombre' => $subcontrato->proveedor->nombre .' - '.$subcontrato->codigo,
            'proveedor_id' => $subcontrato->proveedor_id,
            'centro_contable_id' => $subcontrato->centro_id,
            "porcentaje_retencion" => isset($subcontrato->tipo_retenido[0])?$subcontrato->tipo_retenido[0]->porcentaje:0,
            "saldo_proveedor" => 0,
            "credito_proveedor" => 0,
            'articulos' => $articulo->get([], null),
            "proveedor" => count($subcontrato->proveedor)? $this->formatProveedor($subcontrato->proveedor):[],
            "por_facturar" => $subcontrato->por_facturar()
        ];
    }

     public function formatProveedor($proveedor){

        return [
                'id' => $proveedor->uuid_proveedor,
                'saldo_pendiente' => $proveedor->saldo_pendiente,
                'credito' => $proveedor->credito,
                'nombre' => $proveedor->nombre,
                'proveedor_id' => $proveedor->id,
                'retiene_impuesto' => $proveedor->retiene_impuesto,
                'estado' => $proveedor->estado
            ];

    }

    public function getCollectionSubcontratosPago($subcontratos){

        $subcontratos->load('facturas_por_pagar');
        return $subcontratos->filter(function($subcontrato){
            return count($subcontrato->facturas_por_pagar) > 0;
        })->map(function($subcontrato){

            return [
                'id' => $subcontrato->id,
                'nombre' => $subcontrato->proveedor->nombre .' - '.$subcontrato->codigo,
                'proveedor_id' => $subcontrato->proveedor_id,
                "saldo_proveedor" => 0,
                "credito_proveedor" => 0,
                'pagables' => $subcontrato->facturas_por_pagar->map(function($factura){
                    return [
                        'pagable_id' => $factura->id,
                        'pagable_type' => get_class($factura),
                        'monto_pagado' => 0,
                        'numero_documento' => $factura->codigo,
                        'fecha_emision' => $factura->fecha_desde,
                        'total' => $factura->total,
                        'pagado' => $factura->pagos_aplicados_suma,
                        'saldo' => $factura->saldo
                    ];
                })
            ];

        });

    }

    public function getCollectionSubcontratosPagoRetenido($subcontratos){

        return $subcontratos->map(function($subcontrato){

            return [
                'id' => $subcontrato->id,
                'proveedor_id' => $subcontrato->proveedor_id,
                'depositable_type' => "cuenta_contable",
                'depositable_id' => count($subcontrato->tipo_retenido) ? $subcontrato->tipo_retenido[0]->cuenta_id : '',
                'pagables' => $subcontrato->facturas_habilitadas->map(function($factura) use ($subcontrato){
                    return [
                        'pagable_id' => $factura->id,
                        'pagable_type' => get_class($factura),
                        'monto_pagado' => 0,//editable form
                        'numero_documento' => $factura->codigo,
                        'fecha_emision' => $factura->fecha_desde,
                        'total' => $factura->retencion,//monto retenido
                        'pagado' => $factura->retenido_pagado,//retenido pagado
                        'saldo' => $factura->retenido_por_pagar//retenido por pagar
                    ];
                })
            ];

        });

    }

    public function findByUuid($uuid)
    {
      //$registro->with(array('comentario_timeline', 'adenda.adenda_montos'));
        return SubContrato::where('uuid_subcontrato', hex2bin($uuid))->first();
    }

    public function lista_totales($clause = array())
    {
        return SubContrato::where(function($query) use ($clause){
            $query->where('empresa_id', '=', $clause['empresa_id']);

            if(isset($clause['proveedor_id']))   $query->where('proveedor_id','=' ,$clause['proveedor_id']);
            if(isset($clause['id']))             $query->where('id','=' ,$clause['id']);
            if(isset($clause['monto_original'])) $query->where('monto_subcontrato','=' ,$clause['monto_original']);
            if(isset($clause['monto1'])) $query->deMontoDesde($clause["monto1"]);
            if(isset($clause['monto2'])) $query->deMontoHasta($clause["monto2"]);
            if(isset($clause['codigo']))         $query->where('codigo','=',$clause['codigo']);
            if(isset($clause['centro_id']))      $query->where('centro_id','=',$clause['centro_id']);
            if(isset($clause['estado']))      $query->where('estado','=',$clause['estado']);
            if(isset($clause["campo"]) and !empty($clause["campo"])){$query->deFiltro($clause["campo"]);}
        })->count();
    }

    public function listar($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $subcontratos = SubContrato::where(function($query) use ($clause){
            $query->where('empresa_id','=',$clause['empresa_id']);

            if(isset($clause['proveedor_id']))   $query->where('proveedor_id','=' ,$clause['proveedor_id']);
            if(isset($clause['id']))             $query->where('id','=' ,$clause['id']);
            if(isset($clause['monto_original'])) $query->deMontoOriginal($clause["empresa_id"], $clause["monto_original"]);
            if(isset($clause['monto1'])) $query->deMontoDesde($clause["monto1"]);
            if(isset($clause['monto2'])) $query->deMontoHasta($clause["monto2"]);
            if(isset($clause['codigo']))         $query->where('codigo','like',"%".$clause['codigo']."%");
            if(isset($clause['centro_id']))      $query->where('centro_id','=',$clause['centro_id']);
            if(isset($clause['estado']))      $query->where('estado','=',$clause['estado']);
            //bool -> true and false
            if(isset($clause["facturables"]) and $clause["facturables"]){$query->facturables($clause["empresa_id"]);}
            if(isset($clause["pagables"]) and $clause["pagables"]){$query->pagables($clause["empresa_id"]);}
            if(isset($clause["campo"]) and !empty($clause["campo"])){$query->deFiltro($clause["campo"]);}
        });
        if($sidx !== null && $sord !== null) $subcontratos->orderBy($sidx, $sord);
        if($limit != null) $subcontratos->skip($start)->take($limit);
        return $subcontratos->get();
    }

    function agregarComentario($id, $comentarios) {
        $subcontratos = SubContrato::find($id);
        $comentario = new Comentario($comentarios);
        $subcontratos->comentario_timeline()->save($comentario);
        return $subcontratos;
    }

}

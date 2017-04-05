<?php
namespace Flexio\Modulo\Pagos\Repository;

use Flexio\Modulo\Pagos\Models\Pagos as Pagos;
use Flexio\Modulo\Anticipos\Models\Anticipo;
//service
use Flexio\Modulo\Base\Services\Numero as Numero;
use Flexio\Modulo\FacturasVentas\Services\FacturaVentaEstado as FacturaEstado;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
//repositories
use Flexio\Modulo\Bancos\Repository\BancosRepository as bancosRep;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository as ordenesCompraRep;
use Flexio\Modulo\ComisionesSeguros\Models\SegComisionesParticipacion;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros;
use Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros;

//utils
use Flexio\Modulo\Pagos\Validators\PagoValidator;

class PagosRepository implements PagosInterface{

    //repositories
    private $bancosRep;
    private $ordenesCompraRep;
    protected $PagoValidator;
    protected $ChequesRepository;

    public function __construct()
    {
        $this->bancosRep        = new bancosRep();
        $this->ordenesCompraRep = new ordenesCompraRep();
        $this->PagoValidator = new PagoValidator;
     }
    public function findByUuid($uuid){
        return Pagos::where("uuid_pago",hex2bin($uuid))->first();
    }
    function find($id) {
   		return Pagos::find($id);
  	}
    public function anular_pago($pago_id)
    {
        $pago = Pagos::find($pago_id);

        Capsule::transaction(function () use ($pago){

            $pago->estado = 'anulado';
            $pago->save();

            //pendiente actualizar el estado de la factura/planilla asociada al pago
                //pendiente actualizar el estado del elemento asociado a la factura/planilla

        });

        return true;
    }

    private function _getSyncFacturas($post)
    {
        $aux = [];

        foreach($post['items'] as $item)
        {
            $monto_pagado = str_replace(",", "", $item['monto_pagado']);
            if($monto_pagado > 0)
            {
              $aux[$item['pagable_id']] = [
                  'monto_pagado' => $monto_pagado,
                  'empresa_id' => $post['campo']['empresa_id']
              ];
            }
        }

        return $aux;
    }

    private function saveAnticipo($pago, $post, $anticipo)
    {
        foreach($post['items'] as $item)
        {
            $monto_pagado = str_replace(",", "", $item['monto_pagado']);
            if($monto_pagado > 0)
            {
              $pago->anticipo()->save($anticipo,['monto_pagado' => $monto_pagado,'empresa_id' => $post['campo']['empresa_id']]);
            }
        }
    }

    public function create($post)
    {
        $this->PagoValidator->post_validate($post);
        $post['campo']['codigo'] = Pagos::whereEmpresaId($post['campo']['empresa_id'])->count() + 1;
        $pago = Pagos::create($post['campo']);
        if($pago->empezable_type == "anticipo"){
            $anticipo = Anticipo::find($pago->empezable_id);
            $this->saveAnticipo($pago,$post,$anticipo);
        }else{
            $pago->facturas()->sync($this->_getSyncFacturas($post));
        }


        $metodo_pago = $pago->metodo_pago()->firstOrNew($post['metodo_pago'][0]);
        $metodo_pago->save();

        return $pago;
    }

    public function save($post)
    {
        $pago = Pagos::find($post["campo"]["id"]);
        $this->PagoValidator->change_state_validate($pago, $post);
        $pago->estado = $post['campo']['estado'];
        $pago->save();

        $this->_actualizarEstadoPagable($pago->fresh());
        return $pago;
    }

    private function _actualizarEstadoPagable($pago) {
        $pagables = ($pago->formulario !== "planilla") ? $pago->facturas : $pago->planillas;
        foreach ($pagables as $pagable) {//facturas o planillas

            if (round($pagable->pagos_aplicados_suma, 2) == 0) {
                $pagable->estado_id = 14; //por pagar
            } elseif(round($pagable->saldo, 2) > 0) {
                $pagable->estado_id = 15; //pagada parcial
            } elseif (round($pagable->saldo, 2) == 0 || round($pagable->saldo, 2) < 0) {
                $pagable->estado_id = 16; //pagada completa
            }
            $pagable->save();
        }
    }

    public function findBy($clause)
    {
        $pagos = Pagos::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($pagos, $clause);

        return $pagos->first();
    }

    public function get($clause, $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL)
    {
        $pagos = Pagos::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($pagos, $clause);

        if($sidx!=NULL && $sord!=NULL){$pagos->orderBy($sidx, $sord);}
        if($limit!=NULL){$pagos->skip($start)->take($limit);}

        return $pagos->get();
    }

    public function getColletionPago($pago)
    {
		if($pago->empezable_type == "anticipo"){
            return $this->pago_anticipo($pago);
        }else if($pago->formulario == "retenido"){
            return $this->pago_retenido($pago);
        }else if($pago->formulario == "movimiento_monetario"){
            return $this->pago_movimiento_monetario($pago);
        }
		else if($pago->empezable_type == "participacion"){
            return $this->pago_participacion($pago);
        }
		else if($pago->empezable_type == "remesas_salientes"){
            return $this->pago_remesas_salientes($pago);
        }
        $proveedor = is_null($pago->proveedor)? []: $this->formatProveedor($pago->proveedor);
        return collect(array_merge(
            $pago->toArray(),
            [   'proveedor' => $proveedor,
                'pagables' => $pago->facturas->map(function($factura){
                    return [
						'ruta_url'=>'',
                        'numero_documento' => $factura->codigo,
                        'fecha_emision' => $factura->fecha_desde,
                        'total' => $factura->total,
                        'pagado' => $factura->pagos_aplicados_suma,
                        'saldo' => $factura->saldo,
                        'monto_pagado' => $factura->pivot->monto_pagado,
                        'pagable_id' => $factura->pivot->pagable_id,
                        'pagable_type' => $factura->pivot->pagable_type
                    ];
                }),
                'metodos_pago' => $pago->metodo_pago
            ]
        ));
    }

    public function pago_movimiento_monetario($pago)
    {

        return collect(array_merge(
            $pago->toArray(),
            [
                'pagables' => $pago->movimientos_monetarios->map(function($movimiento){
                    $aux = $movimiento->items->sum('debito');
                    return [
						'ruta_url'=>'',
                        'numero_documento' => $movimiento->codigo,
                        'fecha_emision' => $movimiento->created_at->format('d/m/Y'),
                        'total' => $aux,
                        'pagado' => $movimiento->pagado,
                        'saldo' => $movimiento->saldo,
                        'monto_pagado' => $movimiento->pivot->monto_pagado,
                        'pagable_id' => $movimiento->pivot->pagable_id,
                        'pagable_type' => $movimiento->pivot->pagable_type
                    ];
                }),
                'metodos_pago' => $pago->metodo_pago
            ]
        ));
    }

    public function pago_retenido($pago)
    {

        return collect(array_merge(
            $pago->toArray(),
            [
                'pagables' => $pago->facturas->map(function($factura){
                    return [
						'ruta_url'=>'',
                        'numero_documento' => $factura->codigo,
                        'fecha_emision' => $factura->fecha_desde,
                        'total' => $factura->retencion,
                        'pagado' => $factura->retenido_pagado,
                        'saldo' => $factura->retenido_por_pagar,
                        'monto_pagado' => $factura->pivot->monto_pagado,
                        'pagable_id' => $factura->pivot->pagable_id,
                        'pagable_type' => $factura->pivot->pagable_type
                    ];
                }),
                'metodos_pago' => $pago->metodo_pago
            ]
        ));
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

    private function _filtros($pagos, $clause)
    {
        if(isset($clause["uuid_pagos"]) and !empty($clause["uuid_pagos"])){$pagos->deUuids($clause["uuid_pagos"]);}
        if(isset($clause["uuid_pago"]) and !empty($clause["uuid_pago"])){$pagos->deUuid($clause["uuid_pago"]);}
    }

    public function getCollectionExportar($pagos)
    {
        $aux = [];

        foreach($pagos as $pago)
        {
            $aux[] = $this->getCollectionExportarRow($pago);
        }

        return $aux;
    }

    public function getFacturaOperacionNumeroDocumentos($pago)
    {
        $numeros_documentos = [];
        foreach ($pago->facturas as $factura)
        {
            if(!empty($factura->operacion_type))
            {
                $numeros_documentos[] = ($factura->operacion_type == "Ordenes_orm") ? $this->ordenesCompraRep->find($factura->operacion_id)->numero_documento : '';
            }
        }
        return $numeros_documentos;
    }

    public function getCollectionExportarRow($pago)
    {
        $monto_pagado       = new \Flexio\Modulo\Base\Services\Numero("moneda", $pago->monto_pagado);
        //$numeros_documentos = $this->getFacturaOperacionNumeroDocumentos($pago);
     //   dd($pago->proveedor->toArray());
        $nombre_pago = count($pago->proveedor) ? utf8_decode($pago->proveedor->nombre) : '';
        if ($pago->formulario == 'planilla') {
            $nombre = !empty($pago->colaborador) && !empty($pago->colaborador->nombre) ? $pago->colaborador->nombre : '';
            $apellido = !empty($pago->colaborador) && !empty($pago->colaborador->apellido) ? $pago->colaborador->apellido : '';
            $nombre_pago ="$nombre $apellido";
        } elseif ($pago->formulario == 'pago_extraordinario') {
            $nombre = !empty($pago->colaborador) && !empty($pago->colaborador->nombre) ? $pago->colaborador->nombre : '';
            $apellido = !empty($pago->colaborador) && !empty($pago->colaborador->apellido) ? $pago->colaborador->apellido : '';
            $nombre_pago ="$nombre $apellido";
        }


        return [
            $pago->numero_documento,
            $pago->created_at,
            $nombre_pago,
            count($pago->facturas) ? $pago->facturas->implode("codigo", ", ") : '',//hasta que haga la integracion con contratos
            $this->_metodo_pago($pago->metodo_pago),
            $this->_banco($pago->metodo_pago),
            isset($pago->catalogo_estado->valor)?$pago->catalogo_estado->valor:'',
            ltrim($monto_pagado->getSalida(), '$')
        ];
    }

    private function _metodo_pago($metodo_pago) {
        $tipo_pago="";

        foreach($metodo_pago as $metodo){
             if(isset($metodo->catalogo_metodo_pago->valor)){
                $tipo_pago .=$metodo->catalogo_metodo_pago->valor. " ";
            }else{
              $tipo_pago.='';
            }

        }

        return $tipo_pago;
    }

    private function _banco($metodo_pago) {
        $banco="";

        foreach($metodo_pago as $metodo){
          if($metodo->referencia != null){
           //  $aux    = json_decode($metodo->referencia); //Error reportado 1012-1012-compras-exportar-error-en-el-excel
            $banco .= (isset($metodo->referencia['nombre_banco_ach']) && $metodo->referencia['nombre_banco_ach'] !='') ? $this->bancosRep->find($metodo->referencia['nombre_banco_ach'])->nombre : "";
            $banco .= (isset($metodo->referencia['nombre_banco_cheque']) && is_numeric($metodo->referencia['nombre_banco_cheque'])) ? $this->bancosRep->find($metodo->referencia['nombre_banco_cheque'])->nombre : "";
            //$banco .= (isset($aux->nombre_banco_ach) && $aux->nombre_banco_ach > 0) ? $this->bancosRep->find($aux->nombre_banco_ach)->nombre : "";
            //$banco .= (isset($aux->nombre_banco_cheque) && is_numeric($aux->nombre_banco_cheque)) ? $this->bancosRep->find($aux->nombre_banco_cheque)->nombre : "";
          }

        }

        return $banco;
    }
    function agregarComentario($id, $comentarios) {
        $pagos = Pagos::find($id);
        $comentario = new Comentario($comentarios);
        $pagos->comentario_timeline()->save($comentario);
        return $pagos;
    }

    function pago_anticipo($pago){
        $pagable_id = $pago->empezable_id;
        $pagable_type = 'Flexio\Modulo\Anticipos\Models\Anticipo';
        $proveedor = is_null($pago->proveedor)? []: $this->formatProveedor($pago->proveedor);
        return collect(array_merge(
            $pago->toArray(),

            [   'proveedor' =>$proveedor,
                'pagables' => $pago->anticipo->map(function($anticipo) use($pagable_type) {

                    return [
						'ruta_url'=>'',
                        'numero_documento' => $anticipo->codigo,
                        'fecha_emision' => $anticipo->fecha_anticipo,
                        'total' => $anticipo->monto,
                        'pagado' => $anticipo->pago_pagado,
                        'saldo' => $anticipo->pago_saldo,
                        'monto_pagado' => $anticipo->pivot->monto_pagado,
                        'pagable_id' => $anticipo->pivot->pagable_id,
                        'pagable_type' => $pagable_type
                    ];
                }),
                'metodos_pago' => $pago->metodo_pago
            ]
        ));
    }
	
	function pago_participacion($pago){
        $pagable_id = $pago->empezable_id;
        $pagable_type = 'Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros';
        $proveedor = is_null($pago->proveedor)? []: $this->formatProveedor($pago->proveedor);
		
		return collect(array_merge(
            $pago->toArray(),
            [   'proveedor' => $proveedor,
                'pagables' => $pago->honorario->map(function($comision) use ($pago){
					$monto_part=SegComisionesParticipacion::where('agente_id',$pago->proveedor_id)->where('comision_id',$comision->id)->first();
					$comdatos=ComisionesSeguros::find($comision->id);
                    return [
						'ruta_url'=>base_url('comisiones_seguros/ver/'.bin2hex($comdatos->uuid_comision)),
                        'numero_documento' => $comision->no_comision,
                        'fecha_emision' => $comision->fecha,
                        'total' => $monto_part->monto,
                        'pagado' => number_format(0,2),
                        'saldo' => number_format(0,2),
                        'monto_pagado' => $monto_part->monto,
                        'pagable_id' => $comision->pivot->pagable_id,
                        'pagable_type' => $comision->pivot->pagable_type
                    ];
                }),
                'metodos_pago' => $pago->metodo_pago
            ]
        ));
    }
	
	function pago_remesas_salientes($pago)
	{
		$pagable_id = $pago->empezable_id;
        $pagable_type = 'Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros';
        $proveedor = is_null($pago->proveedor)? []: $this->formatProveedor($pago->proveedor);
		
		return collect(array_merge(
            $pago->toArray(),
            [   'proveedor' => $proveedor,
                'pagables' => $pago->cobrosseguros->map(function($cobros) use ($pago){
					$link=Cobros_seguros::find($cobros->id);
                    return [
						'ruta_url'=>base_url('cobros_seguros/ver/'.$link->uuid_cobro),
                        'numero_documento' => $cobros->codigo,
                        'fecha_emision' => $cobros->fecha_pago,
                        'total' => $cobros->pivot->monto_pagado,
                        'pagado' => number_format(0,2),
                        'saldo' => number_format(0,2),
                        'monto_pagado' => $cobros->pivot->monto_pagado,
                        'pagable_id' => $cobros->pivot->pagable_id,
                        'pagable_type' => $cobros->pivot->pagable_type
                    ];
                }),
                'metodos_pago' => $pago->metodo_pago
            ]
        ));
	}

    function getLastEstadoHistory($id){
        return Capsule::table('revisions as i')
                ->select(capsule::raw('CONCAT(usr.nombre, " " , usr.apellido) as usuario, i.*'))
                ->join('usuarios as usr', 'i.user_id','=', 'usr.id')
                ->where('revisionable_id', '=', $id)
                ->where('revisionable_type', '=', 'Flexio\\Modulo\\Pagos\\Models\\Pagos')
                ->where('new_value','=','aplicado')
                ->where('key', 'estado')
                ->orderBy('i.created_at', 'desc')
                ->first();
    }
 }

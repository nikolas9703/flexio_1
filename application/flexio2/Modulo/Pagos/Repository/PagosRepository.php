<?php
namespace Flexio\Modulo\Pagos\Repository;

use Flexio\Modulo\Pagos\Models\Pagos as Pagos;

//service
use Flexio\Modulo\Base\Services\Numero as Numero;
use Flexio\Modulo\FacturasVentas\Services\FacturaVentaEstado as FacturaEstado;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
//repositories
use Flexio\Modulo\Bancos\Repository\BancosRepository as bancosRep;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository as ordenesCompraRep;

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

    public function create($post)
    {
        $this->PagoValidator->post_validate($post);
        $post['campo']['codigo'] = Pagos::whereEmpresaId($post['campo']['empresa_id'])->count() + 1;
        $pago = Pagos::create($post['campo']);
        $pago->facturas()->sync($this->_getSyncFacturas($post));

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
        return Collect(array_merge(
            $pago->toArray(),
            [
                'pagables' => $pago->facturas->map(function($factura){
                    return [
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

        return [
            $pago->numero_documento,
            $pago->created_at,
            count($pago->proveedor) ? utf8_decode($pago->proveedor->nombre) : '',
            count($pago->facturas) ? $pago->facturas->implode("codigo", ", ") : '',//hasta que haga la integracion con contratos
            $this->_metodo_pago($pago->metodo_pago),
            $this->_banco($pago->metodo_pago),
            isset($pago->catalogo_estado->valor)?$pago->catalogo_estado->valor:'',
            $monto_pagado->getSalida()
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
            $aux    = json_decode($metodo->referencia);
            $banco .= (isset($aux->nombre_banco_ach) && $aux->nombre_banco_ach > 0) ? $this->bancosRep->find($aux->nombre_banco_ach)->nombre : "";
            $banco .= (isset($aux->nombre_banco_cheque) && is_numeric($aux->nombre_banco_cheque)) ? $this->bancosRep->find($aux->nombre_banco_cheque)->nombre : "";
        }

        return $banco;
    }
    function agregarComentario($id, $comentarios) {
        $pagos = Pagos::find($id);
        $comentario = new Comentario($comentarios);
        $pagos->comentario_timeline()->save($comentario);
        return $pagos;
    }
 }

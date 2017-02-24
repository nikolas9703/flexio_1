<?php

namespace Flexio\Modulo\FacturasCompras\Repository;

use Flexio\Modulo\FacturasCompras\Models\FacturaCompra as FacturaCompra;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompraCatalogo as FacturaCompraCatalogo;
use Flexio\Modulo\Cotizaciones\Models\LineItemTransformer as LineItemTransformer;
use Flexio\Modulo\Inventarios\Models\Unidades as Unidades;
use Flexio\Modulo\Comentario\Models\Comentario;
//repositories
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository as ordenescomprasRep;
use Flexio\Modulo\SubContratos\Repository\SubContratoRepository as subcontratosRep;
//service
use Flexio\Modulo\Base\Services\Numero as Numero;
use Flexio\Modulo\FacturasVentas\Services\FacturaVentaEstado as FacturaEstado;

class FacturaCompraRepository {

    private $ordenescomprasRep;
    private $subcontratosRep;

    public function __construct() {
        $this->ordenescomprasRep = new ordenescomprasRep();
        $this->subcontratosRep = new subcontratosRep();
    }

    function find($id) {
        return FacturaCompra::find($id);
    }
    function findById($id) {
        return FacturaCompra::where("id", "=",$id);
    }

    function agregarComentario($facturaId, $comentarios) {


        $factura_compra = FacturaCompra::find($facturaId);
        $comentario = new Comentario($comentarios);

        $factura_compra->comentario()->save($comentario);

        return $factura_compra;
    }

    public function count($clause) {
        $facturas = FacturaCompra::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($facturas, $clause);

        return $facturas->count();
    }

    public function getCollectionCampos($factura_compra) {

        $articulo = new \Flexio\Library\Articulos\FacturaCompraArticulo;

        return collect([
            "id" => $factura_compra->id,
            "proveedor_id" => $factura_compra->proveedor_id,
            "proveedor" => count($factura_compra->proveedor)?$this->formatProveedor($factura_compra->proveedor):[],
            "terminos_pago" => $factura_compra->termino_pago,
            "nro_factura_proveedor" => $factura_compra->factura_proveedor,
            "porcentaje_retencion" => $factura_compra->porcentaje_retencion,
            "fecha" => $factura_compra->fecha_desde,
            "creado_por" => $factura_compra->created_by,
            "referencia" => $factura_compra->referencia,
            "centro_contable_id" => $factura_compra->centro_contable_id,
            "recibir_en_id" => $factura_compra->bodega_id,
            "estado" => $factura_compra->estado_id,
            "observaciones" => $factura_compra->comentario,
            "pagos" => $factura_compra->pagos_aplicados_suma,
            "saldo" => $factura_compra->saldo,
            "saldo_proveedor" => 0,
            "credito_proveedor" => 0,
            "articulos" => $articulo->get($factura_compra->facturas_items, $factura_compra),
            "comentario" => $factura_compra->landing_comments,
            "operacion_type" => ($factura_compra->operacion_type == 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato')?'subcontrato':'otro'
        ]);

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

    public function get($clause, $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $facturas = FacturaCompra::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($facturas, $clause);

        if ($sidx != NULL && $sord != NULL) {
            $facturas->orderBy($sidx, $sord);
        }
        if ($limit != NULL) {
            $facturas->skip($start)->take($limit);
        }

        return $facturas->get();
    }

    public function getOperaciones($clause) {
        //clause -> empresa_id (int)
        //clause -> facturables (bool)
        $ordenes_compras_facturables = $this->ordenescomprasRep->get($clause);
        $aux1 = $ordenes_compras_facturables->sortBy(function($orden_compra_facturable) {
            return $orden_compra_facturable->proveedor->nombre;
        });

        $subcontratos_faturables = $this->subcontratosRep->listar($clause);
        $aux2 = $subcontratos_faturables->sortBy(function($subcontrato_facturable) {
            return $subcontrato_facturable->proveedor->nombre;
        });

        $aux3 = [];

        foreach ($aux1 as $row) {
            $aux3[] = ["registro" => $row, "tipo" => "orden_compra"];
        }
        foreach ($aux2 as $row) {
            $aux3[] = ["registro" => $row, "tipo" => "subcontrato"];
        }

        return $aux3;
    }

    public function getCollectionFacturasPago($facturas)
    {
        return $facturas->map(function($factura){
            return [
                'id' => $factura->id,
                'nombre' => "{$factura->codigo} - {$factura->proveedor->nombre}",
                'proveedor_id' => $factura->proveedor_id,
                'pagables' => [
                    ['pagable_id' => $factura->id,
                        'pagable_type' => get_class($factura),
                        'monto_pagado' => 0,
                        'numero_documento' => $factura->codigo,
                        'fecha_emision' => $factura->fecha_desde,
                        'total' => $factura->total,
                        'pagado' => $factura->pagos_aplicados_suma,
                        'saldo' => $factura->saldo]
                ]
            ];
        });
    }

    private function _filtros($facturas, $clause) {

        if (isset($clause["item_id"]) and ! empty($clause["item_id"])) {
            $facturas->deItem($clause["item_id"]);
        }
        if (isset($clause["uuid_facturas_compra"]) and ! empty($clause["uuid_facturas_compra"])) {
            $facturas->deUuids($clause["uuid_facturas_compra"]);
        }
        if(isset($clause["factura_proveedor"]) and !empty($clause["factura_proveedor"])){$facturas->whereFacturaProveedor($clause["factura_proveedor"]);}
        if(isset($clause["proveedor_id"]) and !empty($clause["proveedor_id"])){$facturas->whereProveedorId($clause["proveedor_id"]);}
        if(isset($clause["por_pagar"]) and $clause["por_pagar"]){$facturas->whereIn('faccom_facturas.estado_id',[14, 15]);}

    }

    public function getCollectionExportar($facturas) {
        $aux = [];

        foreach ($facturas as $factura) {
            $aux[] = $this->getCollectionExportarRow($factura);
        }

        return $aux;
    }

    public function getCollectionExportarRow($factura) {
        $monto = new \Flexio\Modulo\Base\Services\Numero("moneda", $factura->total);
        $saldo = new \Flexio\Modulo\Base\Services\Numero("moneda", $factura->saldo);

        return [
            $factura->numero_documento,
            $factura->created_at,
            count($factura->proveedor) ? utf8_decode($factura->proveedor->nombre) : '',
            //'',referencia
            !empty($factura->operacion_type) ? $factura->operacion->numero_documento : '',
            count($factura->centro_contable) ? utf8_decode($factura->centro_contable->nombre) : '',
            count($factura->estado) ? $factura->estado->valor : '',
            $monto->getSalida(),
            $saldo->getSalida()
        ];
    }

    public function getCollectionCellDeItem($factura, $item_id) {
        $estado = new FacturaEstado;
        $estado->setType($factura->estado_id);
        $estado->setValor($factura->estado->valor);

        $item = $factura->facturas_items->filter(function($factura_item) use ($item_id) {
            return $factura_item->item_id == $item_id;
        })->values(); //reset index

        $precio_unidad = new Numero('moneda', $item[0]->precio_unidad);
        $total = new Numero('moneda', $item[0]->total);

        $unidad = Unidades::find($item[0]->unidad_id);

        return [
            $factura->created_at,
            $factura->numero_documento_enlace,
            count($factura->proveedor) ? $factura->proveedor->nombre_enlace : '',
            //count($factura->estado) ? $estado->getValorSpan() : '',
            $factura->present()->estado_label,
            $item[0]->cantidad . " " . $unidad->nombre,
            $precio_unidad->getSalida(),
            $total->getSalida()
        ];
    }

    function getAll($clause) {
        return FacturaCompra::where(function($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            if (!empty($clause['formulario']))
                $query->whereIn('formulario', $clause['formulario']);
            if (!empty($clause['estado']))
                $query->whereIn('estado', $clause['estado']);
        })->get();
    }

    function create($created) {
        $factura_compra = FacturaCompra::create($created['facturacompra']);
        $lineItem = new LineItemTransformer;




        $items = $lineItem->crearInstancia($created['lineitem']);

        $factura_compra->items()->saveMany($items);

        return $factura_compra;
    }

    function crear($crear) {
        if (empty($crear['factura_id'])) {
            $factura = FacturaCompra::create($crear);
        } else {
            $factura = FacturaCompra::find($crear['factura_id']);
            $factura->update($crear);
        }
        return $factura;
    }

    function update($update) {
        $factura_compra = FacturaCompra::find($update['facturacompra']['factura_id']);
        $factura_compra->update($update['facturacompra']);
        $lineItem = new LineItemTransformer;
        $items = $lineItem->crearInstancia($update['lineitem']);
        $factura_compra->items()->saveMany($items);
        return $factura_compra;
    }

    function findByUuid($uuid) {
        return FacturaCompra::where('uuid_factura', hex2bin($uuid))->first();
    }
    //Facturas_compras_orm::whereIn('uuid_factura', $uuid)->get();
    function findByInUuid($uuid) {
        return FacturaCompra::whereIn('uuid_factura', $uuid)->get();
    }
    function lista_totales($clause = array()) {
        return FacturaCompra::where(function($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            if (isset($clause['cliente_id']))
                $query->where('cliente_id', '=', $clause['cliente_id']);
            if (isset($clause['etapa']))
                $query->where('estado', '=', $clause['etapa']);
            if (isset($clause['creado_por']))
                $query->where('created_by', '=', $clause['creado_por']);
            if (isset($clause['fecha_desde']))
                $query->where('fecha_desde', '<=', $clause['fecha_desde']);
            if (isset($clause['fecha_hasta']))
                $query->where('fecha_hasta', '>=', $clause['fecha_hasta']);
        })->count();
    }

    /**
     * @function de listar y busqueda
     */
    public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $facturas = FacturaCompra::where(function($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            if (isset($clause['cliente_id']))
                $query->where('cliente_id', '=', $clause['cliente_id']);
            if (isset($clause['etapa']))
                $query->where('estado', '=', $clause['etapa']);
            if (isset($clause['creado_por']))
                $query->where('created_by', '=', $clause['creado_por']);
            if (isset($clause['fecha_desde']))
                $query->where('fecha_desde', '<=', $clause['fecha_desde']);
            if (isset($clause['fecha_hasta']))
                $query->where('fecha_hasta', '>=', $clause['fecha_hasta']);
        });
        if ($sidx != NULL && $sord != NULL)
            $facturas->orderBy($sidx, $sord);
        if ($limit != NULL)
            $facturas->skip($start)->take($limit);
        return $facturas->get();
    }

    public function getCollectionFacturasNotaDebito($facturas)
    {
        return $facturas->map(function($factura){
            return [
                'id' => $factura->id,
                'nombre' => (!empty($factura->proveedor) ? $factura->proveedor->nombre : " -"). " {$factura->codigo}",
                'proveedor_id' => $factura->proveedor_id,
                'monto_factura' => $factura->total,
                'fecha_factura' => $factura->fecha_desde,
                'centro_contable_id' => $factura->centro_contable_id,
                'filas' => $factura->facturas_items->map(function($item){
                    return [
                        'cuenta_id' => $item->cuenta_id,
                        'monto' => $item->subtotal,
                        'precio_total' => $item->subtotal,
                        'descripcion' => !empty($item->item) ? $item->item->nombre : "",
                        'impuesto_total' => $item->impuestos,
                        'impuesto_id' => $item->impuesto_id,
                        'item_id' => $item->item_id
                    ];
                })
            ];
        });
    }

    function cobradoCompletoSinNotaDebito($clause) {
        return FacturaCompra::estadosValidos()->has('nota_debito', '<', 1)->where(function($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
        })->get();
    }
    function cobradoCompletoSinNotaDebitoSinEstados($clause) {
        return FacturaCompra::has('nota_debito', '<', 1)->where(function($query) use($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
        })->get();
    }


    /**
     * Funcion usada para obtener las facturas de un proveedor filtrando el proveedor por nombre
     * @param $clause
     * @param null $limit
     * @return mixed
     */
    function cobradoCompletoSinNotaDebitoSinEstadosPorProveedor($clause, $limit = null)
    {
        $query = FacturaCompra::has('nota_debito', '<', 1);
        $query->select("faccom_facturas.*");
        if (isset($clause['q']) && !empty($clause['q'])) {
            $query->join("pro_proveedores", "pro_proveedores.id", "=", "proveedor_id")
                ->where(function ($query) use ($clause) {
                    $query->where("pro_proveedores.nombre", "like", "%" . $clause['q'] . "%");
                    $query->orWhere("faccom_facturas.factura_proveedor", "like", "%" . $clause['q'] . "%");
                });
        }
        $query->where(function ($query) use ($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            $query->where('estado_id', '!=', 12); // todas menos las facturas anuladas 12 segun catalogo:fac_factura_catalogo
        });
        if ($limit != null) {
            $query->take($limit);
        }
        return $query->get();
    }

    public function _sync_items($factura, $items){

        $factura->items_factura()->whereNotIn('id',array_pluck($items,'id_pedido_item'))->delete();

        foreach ($items as $row) {

            $factura_item_id = (isset($row['id_pedido_item']) and !empty($row['id_pedido_item'])) ? $row['id_pedido_item'] : '';

            $factura_item = $factura->items_factura()->firstOrNew(['id'=>$factura_item_id]);
            $factura_item->item_id = $row["item_id"];
            $factura_item->categoria_id = $row["categoria"];
            $factura_item->cantidad = str_replace(',','',$row["cantidad"]);
            $factura_item->unidad_id = $row["unidad"];
            $factura_item->precio_unidad = str_replace(',','',$row["precio_unidad"]);
            $factura_item->impuesto_id = $row["impuesto"];
            $factura_item->descuento = $row["descuento"];
            $factura_item->cuenta_id = $row["cuenta"];
            $factura_item->total = str_replace(',','',$row["precio_total"]) - str_replace(',','',$row["descuento_total"]) + str_replace(',','',$row['impuesto_total']);
            $factura_item->subtotal = str_replace(',','',$row["precio_total"]);
            $factura_item->descuentos = str_replace(',','',$row["descuento_total"]);
            $factura_item->impuestos = str_replace(',','',$row["impuesto_total"]);
            $factura_item->retenido = str_replace(',','',$row["retenido_total"]);

            //opcionales
            $factura_item->atributo_id = (isset($row['atributo_id']) and !empty($row['atributo_id'])) ? $row['atributo_id'] : 0;
            $factura_item->atributo_text = (isset($row['atributo_text']) and !empty($row['atributo_text'])) ? $row['atributo_text'] : '';
            $factura_item->comentario = (isset($row['comentario']) and !empty($row['comentario'])) ? $row['comentario'] : '';

            $factura_item->save();

        }

    }

}

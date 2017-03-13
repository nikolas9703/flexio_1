<?php
namespace Flexio\Modulo\OrdenesTrabajo\Repository;
use Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\OrdenesTrabajo\Models\Servicios;
use Flexio\Modulo\OrdenesTrabajo\Transform\PiezasTransformer;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Comentario\Models\Comentario;

class OrdenesTrabajoRepository
{
    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $CotizacionRepository;

    public function __construct()
    {
        $this->CuentasRepository = new CuentasRepository();
        $this->ImpuestosRepository = new ImpuestosRepository();
    }

    function find($id)
    {
        return OrdenTrabajo::find($id);
    }

    function getAll($clause)
    {
        return OrdenTrabajo::with(array("centro", "cliente", "estado"))->where(function ($query) use ($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            if (!empty($clause['formulario']))
                $query->whereIn('formulario', $clause['formulario']);
            if (!empty($clause['estado']))
                $query->whereIn('estado', $clause['estado']);
            if (!empty($clause['id']))
                $query->whereIn('id', $clause['id']);
        })->get();
    }

    //Listar ordenes en estado por facturar
    function getOrdenesValidas($clause)
    {
        return OrdenTrabajo::deEmpresa($clause)->estadoValido()->get();
    }
    public function getCollectionOrdenTrabajo($orden){

        $articulo = new \Flexio\Library\Articulos\ArticuloVenta;
        return collect(array_merge(
            [
              //  'centros_facturacion' => count($orden->cliente->centro_facturable) ? $orden->cliente->centro_facturable : [],
            ],
            $orden->toArray(),
            [
                'articulos' => $articulo->get($orden->items, $orden)->toArray(),
                'observaciones' => $orden->comentarios,
                'saldo_cliente' => 0,
                'credito_cliente' => 0,
                'creado_por' => $orden->created_by

            ]
        ));

    }
    /*function ordenesVentasValidasVer($clause) {
        return OrdenVenta::deEmpresa($clause)->facturadoCompleto()->get();
    }*/
    public function _sync_items($orden_trabajo, $servicios)
    {
        foreach ($servicios as $servicio) {
            $items = !empty($servicio) ? $servicio["items"] : array();
            $servicio["orden_id"] = $orden_trabajo->id;
            unset($servicio["items"]);

            //Guardar Servicio
            if (empty($servicio["id"])) {
                $servicioModelo = Servicios::create($servicio);
            } else {
                $servicioModelo = Servicios::find($servicio['id']);
                $servicioModelo->update($servicio);
            }

            //Guardar items
            //relacionadnos al servicio
            $servicioModelo->items()->whereNotIn('id', array_pluck($items, 'id'))->delete();
            foreach ($items as $item) {

                $impuesto_id = !empty($item['impuesto_id']) ? $item['impuesto_id'] : "";
                $cuenta_id = !empty($item['cuenta_id']) ? $item['cuenta_id'] : "";
                $impuesto = is_numeric($item['impuesto_id']) ? $this->ImpuestosRepository->find($item['impuesto_id']) : $this->ImpuestosRepository->findByUuid($impuesto_id);
                $cuenta = is_numeric($item['cuenta_id']) ? $this->CuentasRepository->find($item['cuenta_id']) : $this->CuentasRepository->findByUuid($cuenta_id);
                $total_impuesto = !empty($impuesto) ? ($impuesto->impuesto / 100) * ($item['cantidad'] * $item['precio_unidad']) : "";
                $total_descuento = ($item['descuento'] / 100) * ($item['cantidad'] * $item['precio_unidad']);

                $servicio_item_id = (isset($item['id']) and !empty($item['id'])) ? $item['id'] : '';
                $servicio_item = $servicioModelo->items()->firstOrNew(['id' => $servicio_item_id]);
                $servicio_item->empresa_id = !empty($orden_trabajo->empresa_id) ? $orden_trabajo->empresa_id : "";
                $servicio_item->categoria_id = !empty($item['categoria_id']) ? $item['categoria_id'] : "";
                $servicio_item->item_id = !empty($item['item_id']) ? $item['item_id'] : "";
                $servicio_item->cantidad = !empty($item['cantidad']) ? $item['cantidad'] : "";
                $servicio_item->unidad_id = !empty($item['unidad_id']) ? $item['unidad_id'] : "";
                $servicio_item->precio_unidad = !empty($item['precio_unidad']) ? $item['precio_unidad'] : "";
                $servicio_item->impuesto_id = !empty($impuesto) ? $impuesto->id : "";
                $servicio_item->descuento = !empty($item['descuento']) ? $item['descuento'] : "0";
                $servicio_item->cuenta_id = !empty($cuenta) ? $cuenta->id : "";
                $servicio_item->precio_total = !empty($item['precio_total']) ? $item['precio_total'] : "";
                $servicio_item->atributo_id = !empty($item['atributo_id']) ? $item['atributo_id'] : '0';
                $servicio_item->atributo_text = !empty($item['atributo_text']) ? $item['atributo_text'] : '';
                $servicio_item->impuesto_total = $total_impuesto;
                $servicio_item->descuento_total = $total_descuento;
                $servicio_item->comentario = (isset($item['comentario'])) ? $item['comentario'] : '';
                $servicio_item->save();
            }
        }
    }
    public function _sync_items2($orden_trabajo, $item_odt)
    {
        $items = !empty($item_odt) ? $item_odt : array();
        $servicioModelo = $orden_trabajo;
             //dd($items);
            //Guardar items
            //relacionadnos a la ODT
            $servicioModelo->items()->whereNotIn('id', array_pluck($items, 'id_pedido_item'))->delete();
            foreach ($items as $item) {

                $impuesto_id = !empty($item['impuesto']) ? $item['impuesto'] : "";
                $cuenta_id = !empty($item['cuenta']) ? $item['cuenta'] : "";
                $impuesto = $this->ImpuestosRepository->find($impuesto_id);
                $cuenta =$this->CuentasRepository->find($cuenta_id);
                $total_impuesto = ($impuesto->impuesto / 100) * ($item['cantidad'] * $item['precio_unidad']);
                $total_descuento = ($item['descuento'] / 100) * ($item['cantidad'] * $item['precio_unidad']);

                $servicio_item_id = (isset($item['id_pedido_item']) and !empty($item['id_pedido_item'])) ? $item['id_pedido_item'] : '';
                $servicio_item = $servicioModelo->items()->firstOrNew(['id' => $servicio_item_id]);

                $servicio_item->empresa_id = !empty($orden_trabajo->empresa_id) ? $orden_trabajo->empresa_id : "";
                $servicio_item->categoria_id = !empty($item['categoria']) ? $item['categoria'] : "";
                $servicio_item->item_id = !empty($item['item_id']) ? $item['item_id'] : "";
                $servicio_item->cantidad = !empty($item['cantidad']) ? $item['cantidad'] : "";
                $servicio_item->unidad_id = !empty($item['unidad']) ? $item['unidad'] : "";
                $servicio_item->precio_unidad = !empty($item['precio_unidad']) ? $item['precio_unidad'] : "0.00";
                $servicio_item->impuesto_id = $impuesto->id;
                $servicio_item->descuento = !empty($item['descuento']) ? $item['descuento'] : "0";
                $servicio_item->cuenta_id = $cuenta->id;
                $servicio_item->precio_total = !empty($item['precio_total']) ? str_replace(',','',$item['precio_total']) : "0.00";
                $servicio_item->atributo_id = !empty($item['atributo_id']) ? $item['atributo_id'] : '0';
                $servicio_item->atributo_text = !empty($item['atributo_text']) ? $item['atributo_text'] : '0';
                $servicio_item->impuesto_total = $total_impuesto;
                $servicio_item->descuento_total = $total_descuento;
                $servicio_item->comentario = (!empty($item['comentario'])) ? $item['comentario'] : '';
                $servicio_item->save();
            }

    }
    function create($created){
       // dd($created);
        $orden_trabajo = OrdenTrabajo::create($created);
      //  dd($orden_trabajo->toArray());
        if(!empty($created['servicios'])){
            $servicios = $created['servicios'];
            unset($created['servicios']);
            $this->_sync_items($orden_trabajo, $servicios);
        }else{
            //$servicios = $created['items'];
            $items = !empty($created['items']) ? $created["items"] : array();
            unset($created['items']);
            $this->_sync_items2($orden_trabajo, $items);
        }
        return $orden_trabajo;
    }

    function update($update)
    {
        //dd($update);
            //Actualizar Orden de Trabajo
        $orden_trabajo = OrdenTrabajo::find($update['id']);
        if(isset($update['orden_de']))$orden_trabajo->orden_de = $update['orden_de'];
        if(isset($update['orden_de_id']))$orden_trabajo->orden_de_id = $update['orden_de_id'];
        $orden_trabajo->cliente_id = $update['cliente_id'];
        $orden_trabajo->tipo_orden_id = $update['tipo_orden_id'];
        $orden_trabajo->equipo_trabajo_id = $update['equipo_trabajo_id'];
        $orden_trabajo->centro_facturable_id = $update['centro_facturable_id'];
        $orden_trabajo->fecha_inicio = $update['fecha_inicio'];
        $orden_trabajo->fecha_planificada_fin = isset($update['fecha_planificada_fin'])?$update['fecha_planificada_fin']:"";
        $orden_trabajo->centro_id = $update['centro_id'];
        $orden_trabajo->lista_precio_id = $update['lista_precio_id'];
        $orden_trabajo->facturable_id = $update['facturable_id'];
        $orden_trabajo->bodega_id = $update['bodega_id'];
        $orden_trabajo->estado_id = $update['estado_id'];
        $orden_trabajo->comentario = $update['comentario'];
        $orden_trabajo->save();
           // $orden_trabajo->update($update);
            if(!empty($update['servicios'])) {
                $delete_servicios = !empty($update['delete_servicios']) ? $update['delete_servicios'] : "";
                unset($_POST["delete_servicios"]);
                $servicios = $update['servicios'];
                unset($update['servicios']);
            // Guardar/Actualizar Servicios/Items
            $this->_sync_items($orden_trabajo, $servicios);

            //Eliminar servicios e items relacionados
            if (!empty($delete_servicios)) {
                $ids = explode(',', $delete_servicios);
                foreach ($ids AS $id) {
                    //Elminar items relacionados
                    Servicios::find($id)->items()->delete();

                    //Eliminar Servicio
                    Servicios::find($id)->delete();
                }
            }
        }else{
                $items = !empty($update['items']) ? $update["items"] : array();
                unset($update['items']);
                $this->_sync_items2($orden_trabajo, $items);
        }

        return $orden_trabajo;
    }

    public function saveComent($comment) {
        //crear un ajax
    }

    function findByUuid($uuid)
    {
        return OrdenTrabajo::where('uuid_orden_trabajo', hex2bin($uuid))
        ->with(array("items","servicios" => function ($query) {$query->with(array("items", 'items.impuesto', 'items.cuenta'));}))
        ->first();
    }

    public function delete($condicion)
    {
        return OrdenTrabajo::where(function ($query) use ($condicion) {
            $query->where('empresa_id', '=', $condicion ['empresa_id']);
        })->delete();
    }

    public function deleteServicio($clause)
    {
        Piezas::where('servicio_id', '=', $clause['id'])->delete();
        return Servicios::where('id', '=', $clause['id'])->delete();
    }

    /**
     * @function de listar y busqueda
     */
    public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL)
    {

        $cliente = !empty($clause["cliente"]) ? $clause["cliente"] : array();
        $equipo_id = !empty($clause["equipo_id"]) ? $clause["equipo_id"] : array();

        $query = OrdenTrabajo::with(array("facturable", "centro" => function ($query) use ($sidx, $sord) {
            if (!empty($sidx) && preg_match("/centro_id/i", $sidx)) {
                $query->orderBy("nombre", $sord);
            }
        }, "cliente" => function ($query) use ($sidx, $sord) {
            if (!empty($sidx) && preg_match("/cliente/i", $sidx)) {
                $query->orderBy("nombre", $sord);
            }
        }, "estado" => function ($query) use ($sidx, $sord) {
            if (!empty($sidx) && preg_match("/estado/i", $sidx)) {
                $query->orderBy("etiqueta", $sord);
            }
        }, "servicios" => function ($query) use ($sidx, $sord) {

        }));

        //Filtrar por Equipo de Trabajo
        if (!empty($equipo_id)) {
            $ordenes = Servicios::where("equipo_id", $equipo_id)->get(array('orden_id'))->toArray();
            if (!empty($ordenes)) {
                $orden_id = (!empty($ordenes) ? array_map(function ($ordenes) {
                    return $ordenes["orden_id"];
                }, $ordenes) : "");
                $query->whereIn("id", $orden_id);
            }
        }

        //Filtrar Departamento
        if (!empty($cliente)) {
            $clientes = Cliente::where("nombre", $cliente[0], $cliente[1])->get(array('id'))->toArray();
            if (!empty($clientes)) {
                $cliente_id = (!empty($clientes) ? array_map(function ($clientes) {
                    return $clientes["id"];
                }, $clientes) : "");
                $query->whereIn("cliente_id", $cliente_id);
            }
        }

        //Si existen variables de limite
        if ($clause != NULL && !empty($clause) && is_array($clause)) {
            foreach ($clause AS $field => $value) {
                if ($field == "cliente" || $field == "equipo_id") {
                    continue;
                }

                //Verificar si el campo tiene el simbolo @ y removerselo.
                if (preg_match('/@/i', $field)) {
                    $field = str_replace("@", "", $field);
                }

                //verificar si valor es array
                if (is_array($value)) {
                    $query->where($field, $value[0], $value[1]);
                } else {
                    $query->where($field, '=', $value);
                }
            }
        }

        //Si existen variables de orden
        if ($sidx != NULL && $sord != NULL) {
            if (!preg_match("/(cliente|centro_id|estado)/i", $sidx)) {
                $query->orderBy($sidx, $sord);
            }
        }

        if ($limit != NULL) $query->skip($start)->take($limit);
        return $query->get();
    }

    function getOrdenes($clause)
    {
        return OrdenTrabajo::estadosActivos()->where(function ($query) use ($clause) {
                $query->whereIn('id', $clause);
        })->get();
    }
    function agregarComentario($id, $comentarios) {
        $ordenes = OrdenTrabajo::find($id);
        $comentario = new Comentario($comentarios);
        $ordenes->comentario_timeline()->save($comentario);
        return $ordenes;
    }

    public function getLastEstadoHistory($id) {
        return Capsule::table('revisions as i')
                ->select(capsule::raw('CONCAT(usr.nombre, " " , usr.apellido) as usuario, i.*'))
                ->join('usuarios as usr', 'i.user_id','=', 'usr.id')
                ->where('revisionable_id', '=', $id)
                ->where('key', 'estado_id')
                ->orderBy('i.created_at', 'desc')
                ->first();
    }
}

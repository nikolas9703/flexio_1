<?php

namespace Flexio\Modulo\FacturasSeguros\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro;
use Flexio\Modulo\FacturasSeguros\Events\CambiandoEstadoOrdenVenta;

use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Strategy\Transacciones\TransaccionFactura;


class GuardarFacturaSeguro{

    protected $request;
    protected $session;
    protected $disparador;
    protected $modeloEmpezable = [];

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->modeloEmpezable['orden_venta'] = '\Flexio\Modulo\OrdenesVentas\Models\OrdenVenta';
        $this->modeloEmpezable['contrato_venta'] = '\Flexio\Modulo\Contratos\Models\Contrato';
        $this->modeloEmpezable['orden_alquiler'] = '\Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler';
    }

    function guardar(){
          $factura = FormRequest::data_formulario($this->request->input('campo'));

          $items = FormRequest::array_filter_dos_dimenciones($this->request->input('items'));

          $items_alquiler = FormRequest::array_filter_dos_dimenciones($this->request->input('items_alquiler'));

          if(isset($factura['id'])){
              return $this->actualizar($factura, $items, $items_alquiler);
          }

          $factura['codigo'] = $this->getLastCodigo();
          $factura['empresa_id'] = $this->session->empresaId();

          return $this->crear($factura, $items, $items_alquiler);
    }

    function crear($campos, $items, $items_alquiler){
        return  Capsule::transaction(function() use($campos, $items, $items_alquiler){

            //1. se crea la factura
            $factura = FacturaSeguro::create($campos);
            //2. salvar empezable;
            $this->guardarEmpezable($factura);
            //3.salvar items
            $lineItem = $this->getLinesItems($items);
            $factura->items()->saveMany($lineItem);

            //items alquiler
            if(!empty($items_alquiler)){
                $lineItemAlquiler = $this->getLinesItems($items_alquiler);
                $factura->items()->saveMany($lineItemAlquiler);
            }

            return $factura;
        });
    }

    function actualizar($campos, $items, $items_alquiler){
        return  Capsule::transaction(function() use($campos, $items, $items_alquiler){

            //1. se crea la factura
            $factura = FacturaSeguro::find($campos['id']);
            $estado_actual = $factura->estado;
            $factura->update($campos);

            //2. se eliminan los items quitados en el UI
            $this->eliminarItems($factura,$items);
            //3.se actualizan los items
            $lineItem = $this->getLinesItems($items);
            $factura->items()->saveMany($lineItem);

            //items alquiler
            if(!empty($items_alquiler)){
                $lineItemAlquiler = $this->getLinesItems($items_alquiler);
                $factura->items()->saveMany($lineItemAlquiler);
            }

            //realizar transaccion
            if($estado_actual != $campos['estado'] && $campos['estado'] == 'por_cobrar')
            {
                $transaccion = new Transaccion;
                $transaccion->hacerTransaccion($factura->fresh(), new TransaccionFactura);
            }
            return $factura;
        });
    }

    function getLinesItems($items){
        $linesItems = new \Flexio\Modulo\LineItems\Formato\FormatoLineItem;
        return $linesItems->crearInstancia($items);
    }

    function eliminarItems($modelo,$items){

        $line_item_id = $modelo->items->pluck('id')->all();
        $id_comparar = array_pluck($items, 'id');

        $not_in = array_values(array_diff($line_item_id,$id_comparar));
        if(!empty($not_in)){
            $modelo->items()->whereIn('id',$not_in)->delete();
        }
    }

    function guardarEmpezable($modelo){

        if($this->request->has('empezable')){
            $empezable = $this->request->input('empezable');

            if(method_exists($this, $empezable['empezable_type'])){
                //llama el metodo orden_compra o contrato etc,
                call_user_func_array([$this, $empezable['empezable_type']], [$modelo,$empezable['empezable_id']]);
            }
        }
    }

    function orden_venta($modelo, $empezable_id){
        $orden =  (new $this->modeloEmpezable['orden_venta'])->find($empezable_id);
        $modelo->orden_venta()->save($orden,['empresa_id'=>$this->session->empresaId()]);

        $this->disparador->listen(
        [
            CambiandoEstadoOrdenVenta::class
        ],
         'Flexio\Modulo\FacturasSeguros\Listeners\OrdenVentaEstadoListener');

        $this->disparador->fire(new CambiandoEstadoOrdenVenta($orden));
    }

    function contrato_venta($modelo, $empezable_id){
        $orden =  (new $this->modeloEmpezable['contrato_venta'])->find($empezable_id);
        $modelo->contrato_venta()->save($orden,['empresa_id'=>$this->session->empresaId()]);
    }

    function orden_alquiler($modelo, $empezable_id){
        $orden_alquiler =  (new $this->modeloEmpezable['orden_alquiler'])->find($empezable_id);
        $modelo->orden_alquiler()->save($orden_alquiler,['empresa_id'=>$this->session->empresaId()]);
    }


    function getLastCodigo(){
        $clause = ['empresa_id' => $this->session->empresaId()];
        $year = Carbon::now()->format('y');
        $factura = FacturaSeguro::where($clause)->get()->last();
        $codigo_actual = is_null($factura)? 0: $factura->codigo;
        $codigo = (int)str_replace('INV'.$year, "", $codigo_actual);
        return $codigo + 1;
    }

}

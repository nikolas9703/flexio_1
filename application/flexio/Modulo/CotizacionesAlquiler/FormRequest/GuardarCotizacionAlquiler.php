<?php

namespace Flexio\Modulo\CotizacionesAlquiler\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\CotizacionesAlquiler\Models\CotizacionesAlquiler;
use Flexio\Modulo\CotizacionesAlquiler\Transform\CotizacionAlquilerItemsTransform;

class GuardarCotizacionAlquiler{

    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $disparador;

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->clientable = ['clientes'=>'Flexio\Modulo\Cliente\Models\Cliente', 'clientes_potenciales'=>'Flexio\Modulo\ClientesPotenciales\Models\ClientesPotenciales'];
    }


    function guardar(){
        $cotizacion = FormRequest::data_formulario($this->request->input('campo'));
        $items = FormRequest::array_filter_dos_dimenciones($this->request->input('items'));

        if(isset($cotizacion['id'])){
            return $this->actualizar($cotizacion, $items);
        }

        if($this->request->has('empezable_type')){
            $cotizacion['cliente_tipo'] = $this->request->input('empezable_type');
        }
        $cotizacion['codigo'] = $this->getLastCodigo();
        $cotizacion['tipo'] = 'alquiler';
        $cotizacion['empresa_id'] = $this->session->empresaId();

        return $this->crear($cotizacion, $items);
    }

    function crear($campos, $items){

        return  Capsule::transaction(function() use($campos, $items){
            $cotizacion = CotizacionesAlquiler::create($campos);
            $lineItem = new CotizacionAlquilerItemsTransform;
            $cotizacion_items = $lineItem->crearInstancia($items);
            $cotizacion->items()->saveMany($cotizacion_items);
            return  $cotizacion;
        });
    }

    function actualizar($campos, $items){

        return  Capsule::transaction(function() use($campos, $items){
            $cotizacion = CotizacionesAlquiler::find($campos['id']);
            $cotizacion->update($campos);
            $this->eliminarItems($cotizacion,$items);
            $lineItem = new CotizacionAlquilerItemsTransform;
            $cotizacion_items = $lineItem->crearInstancia($items);

            $cotizacion->items()->saveMany($cotizacion_items);
            return  $cotizacion;
        });
    }


    function eliminarItems($cotizacion,$items){

        $line_item_id = $cotizacion->items->pluck('id')->all();
        $id_comparar = array_pluck($items, 'id');

        $not_in = array_values(array_diff($line_item_id,$id_comparar));
        if(!empty($not_in)){
            $cotizacion->items()->whereIn('id',$not_in)->delete();
        }
    }


    function getLastCodigo(){
        $clause = ['empresa_id' => $this->session->empresaId(),'tipo'=>'alquiler'];
        $year = Carbon::now()->format('y');
        $cotizacion = CotizacionesAlquiler::where($clause)->get()->last();
        $codigo = (int)str_replace('QTA'.$year, "", $cotizacion->codigo);
        return $codigo + 1;
    }
}

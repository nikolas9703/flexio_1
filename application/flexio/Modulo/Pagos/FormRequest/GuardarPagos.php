<?php
namespace Flexio\Modulo\Pagos\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Pagos\Models\Pagos;
use Flexio\Modulo\Anticipos\Models\Anticipo;

use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\Pagos\Transacciones\PagosProveedor;
use Flexio\Modulo\Pagos\Validators\PagoValidator;

class GuardarPagos{
    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $PagoValidator;

    function __construct(){
        $this->PagoValidator = new PagoValidator();
    }

    function save($data){

        if(array_get($data, 'campo.id', '') !=''){
            return $this->actualizar($data);
        }
         return $this->crear($data);
    }

    function crear($campo){

        return  Capsule::transaction(function() use($campo){
            $this->PagoValidator->post_validate($campo);
            $campo["campo"]['codigo'] = Pagos::whereEmpresaId($campo["campo"]['empresa_id'])->count() + 1;
            $pago = Pagos::create($campo["campo"]);
            if($pago->empezable_type == "anticipo"){
                $anticipo = Anticipo::find($pago->empezable_id);
                $this->saveAnticipo($pago,$campo,$anticipo);
            }else{
                $pago->facturas()->sync($this->_getSyncFacturas($campo));
            }

            //validate creating...
            $campo["campo"]["estado"] = 'creado';
            $this->PagoValidator->change_state_validate($pago, $campo);

            $metodo_pago = $pago->metodo_pago()->firstOrNew($campo['metodo_pago'][0]);
            $metodo_pago->save();

            return $pago;
        });
    }

    function actualizar($campo){
      return  Capsule::transaction(function() use($campo){
             $pago = Pagos::find($campo["campo"]["id"]);

            if(isset($campo['metodo_pago']) && !empty($campo['metodo_pago'])){
                $pago->metodo_pago()->delete();
                $metodo_pago = $pago->metodo_pago()->firstOrNew($campo['metodo_pago'][0]);
                $metodo_pago->save();
                $this->PagoValidator->change_state_validate($pago, $campo);
                $pago->update(['estado'=>$campo['campo']['estado'], 'depositable_type'=>$campo['campo']['depositable_type'], 'depositable_id'=>$campo['campo']['depositable_id']]);
            }else{
                $this->PagoValidator->change_state_validate($pago, $campo);
                $pago->update(['estado'=>$campo['campo']['estado']]);
            }
             $this->_actualizarEstadoPagable($pago->fresh());
             return $pago;
         });
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

}

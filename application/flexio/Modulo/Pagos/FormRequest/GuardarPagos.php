<?php
namespace Flexio\Modulo\Pagos\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Util\AuthUser;
use Flexio\Modulo\Pagos\Models\Pagos;
use Flexio\Modulo\Anticipos\Models\Anticipo;

use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\Pagos\Transacciones\PagosProveedor;
use Flexio\Modulo\Pagos\Validators\PagoValidator;
use Flexio\Modulo\Politicas\Models\Politicas;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros;
use Flexio\Modulo\Pagos\Models\PagosPagables;

class GuardarPagos{
    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $PagoValidator;
    protected $states_validations = ['por_aplicar' => 'validatePorAplicar', 'aplicado' => 'validateAplicado'];

    function __construct(){
        $this->PagoValidator = new PagoValidator();
        $this->session = new FlexioSession();
        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->AuthUser = new AuthUser();
    }

    function save($data,$metodo_pago_reg=null,$pagables=null){
        $data['campo']['empresa_id'] = $this->session->empresaId();
		
        if(array_get($data, 'campo.id', '') !=''){
            return $this->actualizar($data);
        }
         return $this->crear($data,$metodo_pago_reg,$pagables);
    }

    function crear($campo,$metodo_pago_reg=null,$pagables=null){
	
        return  Capsule::transaction(function() use($campo, $metodo_pago_reg, $pagables){
            $this->PagoValidator->post_validate($campo);
            $campo["campo"]['codigo'] = Pagos::whereEmpresaId($campo["campo"]['empresa_id'])->count() + 1;
            $pago = Pagos::create($campo["campo"]);
            if($pago->empezable_type == "anticipo"){
                $anticipo = Anticipo::find($pago->empezable_id);
                $this->saveAnticipo($pago,$campo,$anticipo);
            }
			else if($pago->empezable_type == "participacion")
			{
				$metodo_pago_hon['tipo_pago']=$metodo_pago_reg['tipo_pago'];
				$metodo_pago_hon['total_pagado']=$metodo_pago_reg['total_pagado'];
			}
			else{
                $pago->facturas()->sync($this->_getSyncFacturas($campo));
            }

            //validate creating...
            $campo["campo"]["estado"] = 'creado';
            $this->PagoValidator->change_state_validate($pago, $campo);
			
			if($pago->empezable_type !== "participacion")
			{
				$metodo_pago = $pago->metodo_pago()->firstOrNew($campo['metodo_pago'][0]);
				$metodo_pago->save();
			}
			else
			{
				$metodo_pago = $pago->metodo_pago()->firstOrNew($metodo_pago_hon);
				$metodo_pago->save();
				
				$totalmontopago=0;
				foreach($pagables as $pag)
				{
					//consulto el monto de cada agente de participacion
					$monto=ComisionesSeguros::find($pag);
					
					$paga['pago_id']=$pago->id;
					$paga['pagable_id']=$monto->id;
					$paga['pagable_type']='Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros';
					$paga['monto_pagado']=$monto->monto_recibo;
					$paga['empresa_id']=$campo["campo"]['empresa_id'];
					
					$pagospagables=PagosPagables::create($paga);
					
					$totalmontopago+=$monto->monto_recibo;
				}
				
				/*$pagoupdate['monto_pagado']=$totalmontopago;
				$pagoupdate['estado']='por_aplicar';
				$pago=Pagos::find($pago->id)->update($pagoupdate);*/
			}

            return $pago;
        });
    }

    function actualizar($campo){
      return  Capsule::transaction(function() use($campo){
             $pago = Pagos::find($campo["campo"]["id"]);
             $this->update_validations($pago, $campo);
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

    private function update_validations($pago, $campo)
    {
        if($pago->estado != $campo['campo']['estado']){
            call_user_func_array([$this, $this->states_validations[$campo['campo']['estado']]], [$pago, $campo]);
        }
    }

    private function validatePorAplicar($pago, $campo)
    {
        if($pago->estado != 'por_aprobar')throw new \Exception("El pago Nro. {$pago->codigo} requiere estar 'Por Aprobar' antes de cambiar el estado");
        if(!count($this->getPoliticas($pago, $campo['campo'])))throw new \Exception("No tiene permisos para cambiar el estado del pago Nro. {$pago->codigo}");
    }

    private function validateAplicado($pago, $campo)
    {
        if($pago->estado != 'por_aplicar')throw new \Exception("El pago Nro. {$pago->codigo} requiere estar 'Por Aplicar' antes de cambiar el estado");
        if(!count($this->getPoliticas($pago, $campo['campo'])))throw new \Exception("No tiene permisos para cambiar el estado del pago Nro. {$pago->codigo}");
    }

    private function getPoliticas($pago, $campo)
    {
        $usuario = Usuarios::find($this->session->usuarioId());
        if(count($usuario->roles_admin)){
            return [1];
        }
        $campo['role_id'] = count($usuario->roles_reales->first()) ? $usuario->roles_reales->first()->id : -1;
        return Politicas::select('ptr_transacciones.*')->where(function($q) use ($pago, $campo){
            $q->where('ptr_transacciones.empresa_id', $campo['empresa_id']);
            $q->where('ptr_transacciones.role_id', $campo['role_id']);
            $q->where('ptr_transacciones.estado_id', 1);
            $q->whereHas('estado_politica', function($estado_politica) use ($pago, $campo){
                $estado_politica->where('ptr_transacciones_catalogo.estado1', $pago->estado);
                $estado_politica->where('ptr_transacciones_catalogo.estado2', $campo['estado']);
            });
        })
        ->where(function($aux) use ($pago, $campo){
        $aux->where('ptr_transacciones.monto_limite', ">=", $pago->monto_pagado);
        })
        ->groupBy('ptr_transacciones.id')
        ->get();
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

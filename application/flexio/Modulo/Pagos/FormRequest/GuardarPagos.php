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
use Flexio\Modulo\HonorariosSeguros\Models\HonorariosSeguros as HonorariosSeguros;
use Flexio\Modulo\HonorariosSeguros\Models\SegHonorariosPart as SegHonorariosPart;
use Flexio\Modulo\ComisionesSeguros\Models\SegComisionesParticipacion;
use Flexio\Modulo\Remesas\Models\Remesa as Remesas;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros as cobros;
use Flexio\Modulo\Cobros_seguros\Models\CobroFactura as CobroFactura;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro;

class GuardarPagos{
    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $PagoValidator;
    protected $states_validations = ['por_aplicar' => 'validatePorAplicar', 'aplicado' => 'validateAplicado', 'anulado' => 'validateAnulado'];

    function __construct(){
        $this->PagoValidator = new PagoValidator();
        $this->session = new FlexioSession();
        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->AuthUser = new AuthUser();
    }

    function save($data,$metodo_pago_reg=null,$pagables=null){
        $data['campo']['empresa_id'] = $this->session->empresaId();
		
        if(array_get($data, 'campo.id', '') !=''){
            $guardar_pago=$this->actualizar($data);
			
			if($guardar_pago->estado=='aplicado'){
				if($guardar_pago->formulario == 'honorario'){
					$datos_hon['estado']='pagada';
                    $datos_hon['id_pago']=$guardar_pago->id;
					$act_honorario=HonorariosSeguros::find($guardar_pago->empezable_id)->update($datos_hon);
					
					$participacionhonorarios=SegHonorariosPart::where('id_honorario',$guardar_pago->empezable_id)->get();
					
					foreach ($participacionhonorarios as $comisionpag)
					{
						$actparpago['id_pago']=$guardar_pago->id;
						$actpagopart=SegComisionesParticipacion::where('agente_id',$guardar_pago->proveedor_id)->where('comision_id',$comisionpag->id_comision_part)->update($actparpago);
					
						$comision=ComisionesSeguros::find($comisionpag->id_comision_part);
						
						if($comision->estado!='pagada'){
							$partcomision=SegComisionesParticipacion::where('comision_id',$comision->id)->whereNull('id_pago')->count();
							
							if($partcomision==0)
							{
								$datosupdate['estado']='pagada';
								$comision->update($datosupdate);
							}
							else
							{
								$datosupdate['estado']='pagada_parcial';
								$comision->update($datosupdate);
							}
						}
					}
				}elseif($guardar_pago->formulario == 'remesa'){

                    $datosRemesas = Remesas::find($guardar_pago->empezable_id);
                    $datosRemesas->update(['estado' => 'Pagada']);
                    foreach ($datosRemesas->remesas_cobros as $key => $value) {
                        cobros::find($value['id_cobro'])->update(['num_remesa' => $datosRemesas->remesa]);
                        $id_factura = CobroFactura::where(['cobro_id' => $value['id_cobro']])->get(array('cobrable_id'));
                        foreach ($id_factura as $key => $value) {
                            
                            if(($value->facturas->remesa_saliente == NULL || $value->facturas->remesa_saliente == '' ) && ($value->facturas->estado == 'cobrado_completo' || $value->facturas->estado == 'cobrado_parcial') ){//
                                FacturaSeguro::where(['id' => $value->facturas->id])->update(['remesa_saliente' => $datosRemesas->remesa]);
                            }elseif($value->facturas->remesa_saliente != NULL && !preg_match("/".$datosRemesas->remesa."/i",$value->facturas->remesa_saliente)){
                                $numeroRemesa = $value->facturas->remesa_saliente.",".$datosRemesas->remesa;
                                FacturaSeguro::where(['id' => $value->facturas->id])->update(['remesa_saliente' => $numeroRemesa]);
                            }
                        }
                    }
                }
			}

            if($guardar_pago->estado == 'anulado'){
                if($guardar_pago->formulario == 'honorario'){

                    $datos_hon['estado'] = 'en_proceso';
                    $datos_hon['id_pago'] = NULL;
                    $act_honorario = HonorariosSeguros::find($guardar_pago->empezable_id);
                    $act_honorario->update($datos_hon);
                    $participacionhonorarios = SegHonorariosPart::where('id_honorario',$guardar_pago->empezable_id)->get();
                    foreach ($participacionhonorarios as $comisionpag){
                        $actparpago['no_recibo'] = NULL;//$guardar_pago->id;
                        SegComisionesParticipacion::where('agente_id',$act_honorario->agente_id/*$guardar_pago->proveedor_id*/)->where('comision_id',$comisionpag->id_comision_part)->update($actparpago);  
                        $actpagopart = SegComisionesParticipacion::where('comision_id',$comisionpag->id_comision_part)->where('no_recibo','<>',NULL)->where('no_recibo','<>','')->get();
                        if(count($actpagopart) == 0){
                            $comiseguro['estado'] = 'liquidada';
                            $comision = ComisionesSeguros::find($comisionpag->id_comision_part)->update($comiseguro);
                        }elseif(count($actpagopart) > 1 ){
                            $comiseguro['estado'] = 'pagada_parcial';
                            $comision = ComisionesSeguros::find($comisionpag->id_comision_part)->update($comiseguro);
                        }
                    }
                    

                }elseif($guardar_pago->formulario == 'remesa'){

                    $datosRemesas = Remesas::find($guardar_pago->empezable_id);
                    $datosRemesas->update(['estado' => 'En Proceso']);
                    foreach ($datosRemesas->remesas_cobros as $key => $value) {
                        cobros::find($value['id_cobro'])->update(['num_remesa'=> NULL]);
                        $id_factura = CobroFactura::where(['cobro_id' => $value['id_cobro']])->get(array('cobrable_id'));
                        foreach ($id_factura as $key => $value) {

                            if(preg_match("/,".$datosRemesas->remesa."/i",$value->facturas->remesa_saliente)){
                                $datosFactura = str_replace(",".$datosRemesas->remesa, "", $value->facturas->remesa_saliente);
                            }elseif(preg_match("/".$datosRemesas->remesa.",/i",$value->facturas->remesa_saliente)){
                                $datosFactura = str_replace($datosRemesas->remesa.",","",$value->facturas->remesa_saliente);
                            }else{
                                $datosFactura = str_replace($datosRemesas->remesa,NULL,$value->facturas->remesa_saliente);
                            }
                            FacturaSeguro::where(['id' => $value->facturas->id])->update(['remesa_saliente' => $datosFactura]);   
                        }
                    }
                    
                }
            }
			
			return  $guardar_pago;
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
            //var_dump([$this, $this->states_validations[$campo['campo']['estado']]]);    
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

    private function validateAnulado($pago, $campo){
        //if($pago->estado == 'anulado')throw new \Exception("El pago Nro. {$pago->codigo} requiere estar 'Por Aplicar' antes de cambiar el estado");
        //$pago->estado != 'por_aplicar' || $pago->estado != 'por_aprobar'|| $pago->estado != 'aplicado'
        //if(!count($this->getPoliticas($pago, $campo['campo'])))throw new \Exception("No tiene permisos para cambiar el estado del pago Nro. {$pago->codigo}");
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

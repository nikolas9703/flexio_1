<?php

namespace Flexio\Modulo\Cobros_seguros\HttpRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros as Cobro;
use Flexio\Modulo\Cobros_seguros\Events\ActualizarCreditoCliente;
use Flexio\Modulo\Cobros_seguros\Events\ActualizarEstadoFactura;
use Flexio\Modulo\Cobros_seguros\Events\ActualizarSaldoCaja;
use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\Cobros_seguros\Transaccion\TransaccionCobro;
use Flexio\Modulo\Cobros_seguros\Models\CobroFactura;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro as facturaSeg;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\Polizas\Models\PolizasBitacora;

class FormGuardar{

    protected $request;
    protected $session;
    protected $tipo_deposito;
    protected $disparador;

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->tipo_deposito = ['banco'=>'Flexio\Modulo\Contabilidad\Models\Cuentas', 'caja'=>'Flexio\Modulo\Cajas\Models\Cajas'];
        $this->disparador = new \Illuminate\Events\Dispatcher();
    }

    function guardar(){
		$cobro = FormRequest::data_formulario($this->request->input('campo'));
		$facturas = collect(FormRequest::array_filter_dos_dimenciones($this->request->input('factura')));
		$metodo_cobros = $this->request->input('metodo_pago');

		//dd($this->request->all());
		//if(isset($cobro['tipo_deposito']))$cobro['depositable_type'] = $this->tipo_deposito[$cobro['tipo_deposito']];

		if(isset($cobro['id'])){
			return $this->actualizar($cobro, $facturas, $metodo_cobros);
		}
		$empezable = $this->request->input('empezable');

		$cobro['empezable_id'] = $empezable['empezable_id'];
		$cobro['empezable_type'] = $empezable['empezable_type'];
		//$year = Carbon::now()->format('y');
		//$cobro['codigo'] = 'PAY'.$year.str_pad($this->getLastCodigo(),6,"0",STR_PAD_LEFT);
		$cobro['codigo'] = $this->getLastCodigo();
		if(isset($cobro['validacion_estado'])){
			if($cobro['validacion_estado'] == "individual" || $cobro['validacion_estado'] == "masivio"){
				$cobro['estado'] = 'agendado';
			}
		}else{
			$cobro['estado'] = 'aplicado';	
		}
		
		$cobro['formulario'] = 'seguros';
		//estado
	
		$factura_ids = array_get($facturas,'cobrable_id');
		
		//print_r('convertir a ids');
		//print_r($facturas);
		return $this->crear($cobro, $facturas, $metodo_cobros);
    }


    function crear($campos, $facturas, $metodo_cobros){
		//return  Capsule::transaction(function() use($campos, $facturas, $metodo_cobros){
			$cobro = new Cobro($campos);
			//1. se crea el cobros
			$cobro->save();
			//se obtiene los ids de las facturas para cambiarle el estado

			$factura_ids = $facturas->map(function($item){
				return $item['cobrable_id'];
			});
			//registro de metodo de pago
			$metodoCobros =  FormatoMetodoCobro::formato($metodo_cobros);
			$cobro->metodo_cobro()->saveMany($metodoCobros);

			//se registran los cobros a las facturas
			$cobrables = FormatoCobrables::formato($facturas);
			$cobro->cobros_facturas()->saveMany($cobrables);
			//actualizar credito del cliente; cunando se paga con credito
			ActualizarCreditoCliente::actualizar($cobro);
			//cambiar es estado de la factura
			ActualizarEstadoFactura::manupilarEstado($factura_ids);
			//actualizar saldo de caja
			if($campos['depositable_type']=='caja'){
				ActualizarSaldoCaja::nuevoSaldo($cobro->depositable_id, $cobro->monto_pagado);
			}
			//realizar transaccion
			$transaccion = new Transaccion;
			$transaccion->hacerTransaccion($cobro, new TransaccionCobro);
			
			if($cobro->cliente_id > 0){

				$datFac = (array)$facturas;
				
				$clauseR = ['empresa_id' => $this->session->empresaId()];
				$year = Carbon::now()->format('y');
				$cobroRes = Cobro::where($clauseR)->get()->last();
				$cobro->codigo = $cobroRes->codigo;
				

				$arrFacs = array();
				foreach($datFac as $dFac){
					for($i=0;$i<count($dFac);$i++){
						$idFac = $dFac[$i]["cobrable_id"];
						
						$facturasObj = facturaSeg::where(["id"=>$idFac])->get();
						if(count($facturasObj) == 0){
							$facturasObj = facturaSeg::where(["id_poliza"=>$idFac])->get();
						}
						$facturasObj->toArray();

						foreach($facturasObj as $facInfo){ // este no tiene datos
							
							$id_fac_final = $facInfo["id"];
							$id_poliza = $facInfo["id_poliza"];

							$factUpdate = CobroFactura::where(array("cobrable_id" => $id_poliza, "cobro_id" => $cobro->id));
							$factUpdate->update(array("cobrable_id" => $id_fac_final));

							$datosCobros = Cobro::where(['id' => $cobro->id])->first();
							foreach ($datosCobros->cobros_facturas as $key => $cobFact) {
								if($cobFact['cobrable_id'] == $facInfo["id"]){
									$bus = Polizas::find($id_poliza);
									if($bus->count()!=0){
										$tipo = "Cobro_seguros";
										if($cobro->estado == "agendado" || $cobro->estado == "por_aplicar" ){

											$comentario = "Cobro agendado: ".$datosCobros->codigo."<br>Fecha de cobro: ".date('d/m/Y', strtotime($datosCobros->fecha_pago))."<br>Factura: ".$facInfo["codigo"]."<br>Monto: $ ".number_format($datosCobros->monto_pagado,2, '.', '')."<br>";
										}else{
											$comentario = "Cobro realizado: ".$datosCobros->codigo."<br>Factura: ".$facInfo["codigo"]."<br>Monto: $ ".number_format($datosCobros->monto_pagado,2, '.', '')."<br>";	
										}
										$fecha_creado = date('Y-m-d H:i:s');
										$Bitacora = new PolizasBitacora;
										$comment = ['comentario'=>$comentario,'usuario_id'=>$this->session->usuarioId(), 'comentable_id' =>$id_poliza, 'comentable_type'=>$tipo, 'created_at'=>$fecha_creado, 'empresa_id'=>$this->session->empresaId() ];
										$msg = $Bitacora->create($comment);
										$cobFacs = CobroFactura::where(array("cobrable_id" => $id_fac_final, "cobro_id" => $cobro->id));
										$cobFacs->update(array("id_ramo" => $bus->ramo_id));
									}
								}
							}

						}
					}
				}

			}
			ActualizarEstadoFactura::manupilarSaldo($factura_ids);
			
            return $cobro;
        //});
    }

    function actualizar($campos, $items, $metodo_cobros)
    {
        return  Capsule::transaction(function() use($campos, $items, $metodo_cobros){
            $cobro = Cobro::find($campos['id']);
            if(isset($campos['fecha_pago']) && isset($metodo_cobros)){
            	$cobro->metodo_cobro()->delete();
            	$metodoCobros =  FormatoMetodoCobro::formato($metodo_cobros);
				$cobro->metodo_cobro()->saveMany($metodoCobros);
            	$cobro->update(['estado'=>$campos['estado'],'fecha_pago' => $campos['fecha_pago'],'num_remesa'=>$campos['num_remesa']]);
            }else{
            	$cobro->update(['estado'=>$campos['estado'],'num_remesa'=>$campos['num_remesa']]);
            }
            
            return $cobro;
        });
    }

    function getLastCodigo(){
        $clause = ['empresa_id' => $this->session->empresaId()];
        $year = Carbon::now()->format('y');
        $cobro = Cobro::where($clause)->get()->last();
		if(count($cobro)>0)
			$codigocobro=$cobro->codigo;
		else
			$codigocobro=0;
        $codigo = (int)str_replace('PAY'.$year, "", $codigocobro);
        return $codigo + 1;
      }
}

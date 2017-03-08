<?php

namespace Flexio\Modulo\FacturasCompras\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Toast;

//repositorios
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;

class FacturasComprasTransacciones {

    protected $SysTransaccionRepository;
    protected $ImpuestosRepositoy;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
        $this->ImpuestosRepositoy       = new ImpuestosRepository();
    }

    public function haceTransaccion($factura_compra)
    {


        //factura_compra->bdega->entrada_id = '1'; Es una entrada manual
         $clause      = [
            "empresa_id"    => $factura_compra->empresa_id,
            "nombre"        => 'TransaccionFacturaCompra'.'-'.$factura_compra->codigo.'-'.$factura_compra->empresa_id.'-'.$factura_compra->proveedor_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$factura_compra->empresa_id,'linkable_id'=>$factura_compra->id,'linkable_type'=> get_class($factura_compra));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $factura_compra){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($factura_compra));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }

    }

    public function deshaceTransaccion($factura_compra)
    {
        //...
    }

    public function transacciones($factura_compra)
    {
        return array_merge($this->_debito_ciclo($factura_compra),$this->_debito($factura_compra),$this->_credito($factura_compra));
    }

    private function _getProceso($factura_compra, $item)
    {

        if($factura_compra->bodega->raiz->entrada_id != '1')//la condicion debe ser distinta de 1
        {
            return "BodegaAutomatica";
        }
        elseif(!$item->inventariado)
        {
            return "NoInventariado";
        }

        // Validacion de cuando la factura se crea sin  orden de compra
        if(empty($factura_compra->operacion_type) || count($factura_compra->operacion) == 0){
            return "NoRecibido";
        }
        $recibido = (empty($factura_compra->operacion_type) || $factura_compra->operacion_type == 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato') ? 0 : $factura_compra->operacion->items->filter(function($item2) use ($item){
            return $item2->id == $item->id and $item2->pivot->cantidad2 == $item2->pivot->cantidad ;
        })->count();

        if($recibido == 0)
        {
            return "NoRecibido";
        }
        else
        {
            return "Recibido";
        }

    }

    private function _debito_ciclo($factura_compra)
    {

        $asientos   = [];

        //sumatoria agrupada por tipos de impuestos
        foreach($factura_compra->items  as $item)
        {
            $className  = "Flexio\\Modulo\\FacturasCompras\\Transacciones\\" . $this->_getProceso($factura_compra, $item);
            //echo $className."<br>\n";
            $asientos[] = ( new $className )->debito($item, $factura_compra);
        }
        return $asientos;
    }

    private function _debito($factura_compra)
    {



        $asientos   = [];

        //sumatoria agrupada por tipos de impuestos
        foreach($factura_compra->items_groupByImpuestos()  as $item)
        {

            $cuenta_proveedores     = $this->_getCuentaIdCredito($factura_compra);
            $cuenta = $this->_getCuentaDebito($item);
            $debito = $item->sum(function($item2){
                return $item2->pivot->impuestos;
            });

            $credito_retenido = $item->sum(function($item2){
                return $item2->pivot->retenido;
            });

            $asientos[] = new AsientoContable([
                'codigo'        => $factura_compra->codigo,
                'nombre'        => $factura_compra->codigo. ' - '.$factura_compra->proveedor->nombre,
                'debito'        => $debito,
                'cuenta_id'     => $cuenta->id,
                'centro_id'     => $factura_compra->centro_contable_id,
                'empresa_id'    => $factura_compra->empresa_id,
                'created_at'    => date('Y-m-d H:i:s', strtotime($factura_compra->fecha_desde))
            ]);

            ///retenido por items
            $impuesto = $this->ImpuestosRepositoy->find($item->first()->pivot->impuesto_id);

            if($factura_compra->empresa->retiene_impuesto =="si" && $factura_compra->proveedor->retiene_impuesto=='no' && $factura_compra->total > 0 && $impuesto->retiene_impuesto == 'si'){
                //$cuenta_retenido = $this->_getImpuesto($item);
                $cuenta_retenido = $this->_getImpuesto($impuesto);
                $asientos[] = new AsientoContable([
                    'codigo'        => $factura_compra->codigo,
                    'nombre'        => $factura_compra->codigo. ' - '.$factura_compra->proveedor->nombre,
                    'credito'        => round_up($credito_retenido),
                    'cuenta_id'     => $cuenta_retenido->cuenta_retenida_id,
                    'centro_id'     => $factura_compra->centro_contable_id,
                    'empresa_id'    => $factura_compra->empresa_id,
                    'created_at'    => date('Y-m-d H:i:s', strtotime($factura_compra->fecha_desde))
                ]);
            }
        }

        if( $factura_compra->retencion > 0 && count($factura_compra->contrato_relacionado->tipo_retenido->first())){


          $asientos[] = new AsientoContable([
              'codigo'        => $factura_compra->codigo,
              'nombre'        => $factura_compra->codigo. ' - '.$factura_compra->proveedor->nombre,
              'debito'        => $factura_compra->retencion,
              'cuenta_id'     => $cuenta_proveedores->cuenta_id,
              'centro_id'     => $factura_compra->centro_contable_id,
              'empresa_id'    => $factura_compra->empresa_id,
              'created_at'    => date('Y-m-d H:i:s', strtotime($factura_compra->fecha_desde))
          ]);

        }

        return $asientos;
    }

    private function _credito($factura_compra){

        $cuenta     = $this->_getCuentaIdCredito($factura_compra);
        $asientos   = [];

        $asientos[] = new AsientoContable([
            'codigo'        => $factura_compra->codigo,
            'nombre'        => $factura_compra->codigo. " - " .$factura_compra->proveedor->nombre,
            'credito'       => $factura_compra->total,
            'centro_id'     => $factura_compra->centro_contable_id,
            'cuenta_id'     => $cuenta->cuenta_id,
            'empresa_id'    => $factura_compra->empresa_id,
            'created_at'    => date('Y-m-d H:i:s', strtotime($factura_compra->fecha_desde))
        ]);
        ///retenido de impuesto a proveedor
        if($factura_compra->empresa->retiene_impuesto =='si' && $factura_compra->proveedor->retiene_impuesto == 'no' && $factura_compra->total > 0){
            $total_retenido = $factura_compra->facturas_compras_items->sum('retenido');
            $asientos[] = new AsientoContable([
                'codigo'        => $factura_compra->codigo,
                'nombre'        => $factura_compra->codigo. " - " .$factura_compra->proveedor->nombre,
                'debito'       => round_up($total_retenido),
                'cuenta_id'     => $cuenta->cuenta_id,
                'centro_id'     => $factura_compra->centro_contable_id,
                'empresa_id'    => $factura_compra->empresa_id,
                'created_at'    => date('Y-m-d H:i:s', strtotime($factura_compra->fecha_desde))
            ]);
        }
        if($factura_compra->retencion>0 && count($factura_compra->contrato_relacionado->tipo_retenido->first())){

          $subcontrato = $factura_compra->contrato_relacionado->tipo_retenido->first();

          $asientos[] = new AsientoContable([
              'codigo'        => $factura_compra->codigo,
              'nombre'        => $factura_compra->codigo. " - " .$factura_compra->proveedor->nombre,
              'credito'       => $factura_compra->retencion,
              'cuenta_id'     => $subcontrato->cuenta_id,
              'centro_id'     => $factura_compra->centro_contable_id,
              'empresa_id'    => $factura_compra->empresa_id,
              'created_at'    => date('Y-m-d H:i:s', strtotime($factura_compra->fecha_desde))
          ]);
        }

        return $asientos;
    }

    private function _getCuentaDebito($item)
    {
        $impuesto = $this->ImpuestosRepositoy->find($item->first()->pivot->impuesto_id);
        if(!(count($impuesto) && count($impuesto->cuenta)))
        {
            throw new \Exception('No se logr&oacute; determinar la cuenta para realizar el debito del impuesto.');
        }

        return $impuesto->cuenta;
    }

    //private function _getImpuesto($item)
    private function _getImpuesto($impuesto)
    {
        //$impuesto = $this->ImpuestosRepositoy->find($item->first()->pivot->impuesto_id);
        if(!(count($impuesto) && !empty($impuesto->cuenta_retenida_id)))
        {
            throw new \Exception('No se logr&oacute; determinar la cuenta para realizar el cr&eacute;dito por la retenci&oacute;n de impuestos.');
        }

        return $impuesto;
    }

    private function _getCuentaIdCredito($factura_compra)
    {
        $cuentas = $factura_compra->empresa->cuenta_por_pagar_proveedores;
        if(!count($cuentas))
        {
            throw new \Exception('No se logr&oacute; determinar la cuenta por pagar a proveedores.');
        }

        return $cuentas->first();
    }

}

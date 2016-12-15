<?php
namespace Flexio\Modulo\Presupuesto\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra;
use Flexio\Modulo\SubContratos\Models\SubContrato;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;

class Comprometido{

    public $itemPresupuesto;

    function __construct($cuentaPresupuesto){
        $this->itemPresupuesto = $cuentaPresupuesto;
    }

    function ordenesCompra(){
        //estado por_aprobar = 1 , por_facturar = 2, parcial = 3 completo = 4, anualado = 5
        // centro contable, cuenta de gasto

        $ordenCompra = OrdenesCompra::where(function($query){
            $query->where('id_empresa',$this->itemPresupuesto->empresa_id);
            $query->where('fecha_creacion','>=',$this->itemPresupuesto->presupuesto->fecha);
            $query->whereIn('id_estado',[2,3]);
            $query->where('uuid_centro',hex2bin($this->itemPresupuesto->centro_contable->uuid_centro));
        })->get();
        if(is_null($ordenCompra)){
            return 0;
        }

        $total = $ordenCompra->map(function($orden){
            return $orden->items->filter(function($item){
                    return $item->pivot->cuenta_id == $this->itemPresupuesto->cuentas_id;
            })->sum(function($item){
                return $item->pivot->cantidad * $item->pivot->precio_unidad;
            });
        })->all();
        return array_sum($total);
    }

    function subContrados(){

        $subcontratos = SubContrato::where(function($query){
            $query->where('empresa_id',$this->itemPresupuesto->empresa_id);
            $query->where('centro_id',$this->itemPresupuesto->centro_contable_id);
            $query->where('fecha_inicio','>=',$this->itemPresupuesto->presupuesto->fecha);
        })->get();

        if(is_null($subcontratos)){
            return 0;
        }

        $total = $subcontratos->map(function($sub){
            return $sub->subcontrato_montos->map(function($monto_contrato){
                return $monto_contrato->cuenta_id == $this->itemPresupuesto->cuentas_id;
            })->sum('monto');
        })->all();
        return array_sum($total);
    }

    function facturasCompras(){
        // 14 por_pagar 13 por_aprobar 15 pagado_parcial 16 pagado completo
        $facturaCompra = FacturaCompra::where(function($query){
            $query->where('empresa_id',$this->itemPresupuesto->empresa_id);
            $query->where('fecha_desde','>=',$this->itemPresupuesto->presupuesto->fecha);
            $query->whereIn('estado_id',[14,15]);
            $query->where('centro_contable_id',$this->itemPresupuesto->centro_contable_id);
        })->get();
        if(is_null($facturaCompra)){
            return 0;
        }

        $total = $facturaCompra->map(function($factura){
            return $factura->items->filter(function($item){
                return $item->pivot->cuenta_id == $this->itemPresupuesto->cuentas_id;
            })->sum(function($item){
                return $item->pivot->total;
            });
        })->all();
        return array_sum($total);

    }

    function total(){
        return $this->ordenesCompra() + $this->subContrados() + $this->facturasCompras();
    }


}

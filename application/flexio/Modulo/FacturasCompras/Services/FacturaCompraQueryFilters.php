<?php
namespace Flexio\Modulo\FacturasCompras\Services;

use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class FacturaCompraQueryFilters extends QueryFilters{

    public function empezable_type($empezable_type)
    {
        //invoice to pay
        if($empezable_type == 'factura')
        {
            return $this->builder->whereIn('faccom_facturas.estado_id',[14, 15]);
        }
        return $this->builder;
    }

    public function q($q)
    {
        return $this->builder->where(function($query) use ($q){
            $query->where('factura_proveedor', 'like', "%$q%");
            $query->orWhereHas('proveedor', function($proveedor) use ($q){
                $proveedor->where('nombre', 'like', "%$q%");
            });
        });
    }

    public function uuid($uuid)
    {
        return $this->builder->where('uuid_factura', hex2bin($uuid));
    }

    public function id($id)
    {
        return $this->builder->where('id', $id);
    }

  function empresa($empresa){
    return $this->builder->where('empresa_id',$empresa);
  }

  function proveedor ($proveedor){
    return $this->builder->where('proveedor_id',$proveedor);
  }

  function subcontrato($subcontrato){
    return $this->builder->whereHas('subcontrato', function ($query) use($subcontrato) {
        $query->where('id', $subcontrato);
    });
  }
   
   function creacion_min($fecha){
      $fecha = Carbon::createFromFormat('d-m-Y', $fecha, 'America/Panama');
      return $this->builder->where('created_at','>=',$fecha);
   }


   function creacion_max($fecha){
      $fecha = Carbon::createFromFormat('d-m-Y', $fecha, 'America/Panama');
      return $this->builder->where('created_at','<=',$fecha);
   }

   function termino_pago($termino){
    $termino = array_filter($termino);
    if(!empty($termino)){
        if(is_array($termino)){
        return $this->builder->whereIn('termino_pago', $termino);
    }
    return $this->builder->where('termino_pago', $termino);
    }
    

   }

   function pagos($pagos){

     
    if($pagos == 'si'){
        return $this->builder->has('pagos', '>', 0);
    }

    return $this->builder->has('pagos', '=', 0);
         
   }


   function numero_dias($numeros_dias){

    return $this->builder->whereRaw('DATEDIFF(CURDATE(),fecha_desde) >= '.$numeros_dias);

   }



}

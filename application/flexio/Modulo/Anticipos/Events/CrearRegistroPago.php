<?php
namespace Flexio\Modulo\Anticipos\Events;

use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Modulo\Pagos\Models\Pagos;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Pagos\Models\PagosMetodos;
use Flexio\Modulo\Proveedores\Models\Proveedores;
use Flexio\Modulo\Catalogos\Models\Catalogo;

class CrearRegistroPago{

  public $anticipo;

  public function __construct(Anticipo $anticipo){

    $this->anticipo = $anticipo;
  }

  public function hacer(){

    $anticipo = $this->anticipo;

    $pago=[
      'codigo' => $this->codigoPago($anticipo),
      'fecha_pago' => Carbon::now()->format('d/m/Y'),
      'proveedor_id' => $anticipo->anticipable_id,
      'monto_pagado' => $anticipo->monto,
      'depositable_id' => $anticipo->depositable_id,
      'depositable_type' => 'banco',
      'empresa_id' => $anticipo->empresa_id,
      'estado'=>'por_aprobar',
      'empezable_type'=> 'anticipo',
      'empezable_id'=> $anticipo->id
    ];
    $proveedor = Proveedores::find($anticipo->anticipable_id); 
    
    if(count($anticipo->metodo_anticipo)){
        $tipodepago = $anticipo->metodo_anticipo;
    }elseif (count($proveedor->metodo_pagos)) {
        $tipodepago = $proveedor->forma_de_pago;
    }else {
        $tipodepago = 'cheque';
    }

    //dd($tipodepago,$proveedor->metodo_pagos);
    $crearPago = Pagos::create($pago);
    //$referencia = $this->referencia($anticipo->referencia);
    //$referencia = null;
    $metodo = PagosMetodos::create(['tipo_pago'=>$tipodepago,'total_pagado'=>$anticipo->monto]);
    $crearPago->metodo_pago()->save($metodo);  
    $crearPago->anticipo()->save($anticipo,['monto_pagado'=>$anticipo->monto,'empresa_id'=>$anticipo->empresa_id]);
  }

  private function codigoPago($anticipo){
    $pago= Pagos::where(['empresa_id'=>$anticipo->empresa_id])->get()->last();

    $year = Carbon::now()->format('y');
    $codigo = empty($pago)? 0 : (int)str_replace('PGO'.$year, "", $pago->codigo);

    return $codigo + 1;
  }

  private function referencia($referencia){

      if(array_key_exists('cuenta',$referencia)){
          $referencia['cuenta_proveedor'] =   $referencia['cuenta'];
          unset($referencia['cuenta']);
          return $referencia;
      }
      return $referencia;
  }
}

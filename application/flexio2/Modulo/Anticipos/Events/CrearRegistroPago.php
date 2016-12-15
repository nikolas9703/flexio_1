<?php
namespace Flexio\Modulo\Anticipos\Events;

use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Modulo\Pagos\Models\Pagos;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Pagos\Models\PagosMetodos;

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
      'estado'=>'por_aprobar'
    ];
    $crearPago = Pagos::create($pago);
    $metodo = PagosMetodos::create(['tipo_pago'=>$anticipo->metodo_anticipo,'total_pagado'=>$anticipo->monto,'referencia'=>$anticipo->referencia]);
    $crearPago->metodo_pago()->save($metodo);
    $crearPago->anticipo()->save($anticipo,['monto_pagado'=>$anticipo->monto,'empresa_id'=>$anticipo->empresa_id]);
  }

  private function codigoPago($anticipo){
    $pago= Pagos::where(['empresa_id'=>$anticipo->empresa_id])->get()->last();

    $year = Carbon::now()->format('y');
    $codigo = empty($pago)? 0 : (int)str_replace('PGO'.$year, "", $pago->codigo);

    return $codigo + 1;
  }
}

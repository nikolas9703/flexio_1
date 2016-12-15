<?php
namespace Flexio\Modulo\Anticipos\Events;

use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Modulo\Cobros\Models\Cobro;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Cobros\Models\MetodoCobros;
use Flexio\Modulo\Cobros\HttpRequest\FormatoMetodoCobro;
use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\Cobros\Transaccion\TransaccionCobro;

class CrearRegistroCobro{

  public $anticipo;

  public function __construct(Anticipo $anticipo){

    $this->anticipo = $anticipo;
  }

  public function hacer(){

    $anticipo = $this->anticipo;

    $cobro=[
      'codigo' => $this->codigoCobro($anticipo),
      'fecha_pago' => Carbon::now()->format('d/m/Y'),
      'cliente_id' => $anticipo->anticipable_id,
      'monto_pagado' => $anticipo->monto,
      'depositable_id' => $anticipo->depositable_id,
      'depositable_type' => 'Flexio\Modulo\Contabilidad\Models\Cuentas',
      'empresa_id' => $anticipo->empresa_id,
      'estado'=>'aplicado',
      'formulario' =>'anticipo'
    ];
    $crearCobro = Cobro::create($cobro);
    $item_pago = FormatoMetodoCobro::formato([['tipo_pago'=>'al_contado','total_pagado'=>$anticipo->monto,'referencia'=>$anticipo->referencia]]);

    $crearCobro->metodo_cobro()->saveMany($item_pago);
    $transaccion = new Transaccion;
    $transaccion->hacerTransaccion($crearCobro, new TransaccionCobro);
    //hasta que cobro sea polimorfico
    //$crearPago->anticipo()->save($anticipo,['monto_pagado'=>$anticipo->monto,'empresa_id'=>$anticipo->empresa_id]);

  }

  private function codigoCobro($anticipo){
    $cobro= Cobro::where(['empresa_id'=>$anticipo->empresa_id])->get()->last();

    $year = Carbon::now()->format('y');
    $codigo = empty($cobro)? 0 : (int)str_replace('PAY'.$year, "", $cobro->codigo);

    return $codigo + 1;
  }
}

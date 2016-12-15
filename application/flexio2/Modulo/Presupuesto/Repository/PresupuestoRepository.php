<?php

namespace Flexio\Modulo\Presupuesto\Repository;

use Carbon\Carbon;
use Flexio\Modulo\Presupuesto\Models\Presupuesto;


class PresupuestoRepository{

  function findByUuid($uuid){
    return Presupuesto::where('uuid_presupuesto',hex2bin($uuid))->first();
  }

  function find($id){
      return Presupuesto::find($id);
  }

  function getLastCodigo($clause=[]){
    $presupuesto = Presupuesto::where($clause)->get()->last();
    $codigo = (int)str_replace('PPTO',"",$presupuesto->codigo);
    return $codigo + 1;
  }

  function inId($id){
    return Presupuesto::whereIn('id',$id)->get();
  }
}

<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaAbonar as CuentaAbonar;
//cargar el modelo codeigniter transaccion

class CuentaPorAbonarRepository{

  public function find($id)
  {
    return CuentaAbonar::find($id);
  }

  public function create($create)
  {
    return CuentaAbonar::create($create);
  }

  public function update($update)
  {
      return CuentaAbonar::update($update);
  }

  public function delete($condicion){
    return CuentaAbonar::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaAbonar::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaAbonar::where($empresa)->get()->count() > 0){
        return  true;
    }
    return false;
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_pagar = CuentaAbonar::where($condicion)->get()->last();
    if(!is_null($cuenta_pagar)){
      return $cuenta_pagar->tienes_transacciones();
    }
    return false;
  }

}

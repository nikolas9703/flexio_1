<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaRemesaSaliente as CuentaRemesaSaliente;
//cargar el modelo codeigniter transaccion

class CuentaRemesaSalienteRepository{

  public function find($id)
  {
    return CuentaRemesaSaliente::find($id);
  }

  public function create($create)
  {
    return CuentaRemesaSaliente::create($create);
  }

  public function update($update)
  {
      return CuentaRemesaSaliente::update($update);
  }

  public function delete($condicion){
    return CuentaRemesaSaliente::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaRemesaSaliente::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaRemesaSaliente::where($empresa)->get()->count() > 0){
        return  true;
    }
    return false;
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_pagar = CuentaRemesaSaliente::where($condicion)->get()->last();
    if(!is_null($cuenta_pagar)){
      return $cuenta_pagar->tienes_transacciones();
    }
    return false;
  }

}

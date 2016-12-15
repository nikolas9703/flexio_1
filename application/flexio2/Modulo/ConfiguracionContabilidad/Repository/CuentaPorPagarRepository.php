<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorPagar as CuentaPorPagar;
//cargar el modelo codeigniter transaccion

class CuentaPorPagarRepository{

  public function find($id)
  {
    return CuentaPorPagar::find($id);
  }

  public function create($create)
  {
    return CuentaPorPagar::create($create);
  }

  public function update($update)
  {
      return CuentaPorPagar::update($update);
  }

  public function delete($condicion){
    return CuentaPorPagar::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaPorPagar::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaPorPagar::where($empresa)->get()->count() > 0){
        return  true;
    }
    return false;
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_pagar = CuentaPorPagar::where($condicion)->get()->last();
    if(!is_null($cuenta_pagar)){
      return $cuenta_pagar->tienes_transacciones();
    }
    return false;
  }

}

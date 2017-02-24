<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaContrato;
//cargar el modelo codeigniter transaccion

class CuentaContratoRepository{

  public function find($id)
  {
    return CuentaContrato::find($id);
  }

  public function create($create)
  {
    return CuentaContrato::create($create);
  }

  public function update($update)
  {
      return CuentaContrato::update($update);
  }

  public function delete($condicion){
    return CuentaContrato::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaContrato::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaContrato::where($empresa)->get()->count() > 0){
        return  true;
    }
    return false;
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_pagar = CuentaContrato::where($condicion)->get()->last();
    if(!is_null($cuenta_pagar)){
      return $cuenta_pagar->tienes_transacciones();
    }
    return false;
  }

}

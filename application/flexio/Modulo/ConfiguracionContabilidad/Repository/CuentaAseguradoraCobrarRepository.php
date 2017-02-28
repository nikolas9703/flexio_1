<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaAseguradoraCobrar as CuentaAseguradoraCobrar;
//cargar el modelo codeigniter transaccion

class CuentaAseguradoraCobrarRepository{

    public function findBy($clause)
    {
        //...
    }
  public function find($id)
  {
    return CuentaAseguradoraCobrar::find($id);
  }

  public function create($create)
  {
    return CuentaAseguradoraCobrar::create($create);
  }

  public function update($update)
  {
      return CuentaAseguradoraCobrar::update($update);
  }

  public function delete($condicion){
    return CuentaAseguradoraCobrar::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaAseguradoraCobrar::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaAseguradoraCobrar::where($empresa)->get()->count() > 0){
          return  true;
    }else{
          return false;
    }
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_cobro = CuentaAseguradoraCobrar::where($condicion)->get()->last();
    return $cuenta_cobro->tienes_transacciones();
  }

}

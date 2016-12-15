<?php
namespace Flexio\Repository\ConfiguracionContabilidad;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorCobrar as CuentaPorCobrar;
use Flexio\Repository\InterfaceRepository as InterfaceRepository;
//cargar el modelo codeigniter transaccion

class CuentaPorCobrarRepository implements InterfaceRepository{

    public function findBy($clause)
    {
        //...
    }
  public function find($id)
  {
    return CuentaPorCobrar::find($id);
  }

  public function create($create)
  {
    return CuentaPorCobrar::create($create);
  }

  public function update($update)
  {
      return CuentaPorCobrar::update($update);
  }

  public function delete($condicion){
    return CuentaPorCobrar::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaPorCobrar::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaPorCobrar::where($empresa)->get()->count() > 0){
          return  true;
    }else{
          return false;
    }
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_cobro = CuentaPorCobrar::where($condicion)->get()->last();
    return $cuenta_cobro->tienes_transacciones();
  }

}

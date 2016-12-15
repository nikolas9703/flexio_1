<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaInventario;
//cargar el modelo codeigniter transaccion

class CuentaInventarioRepository{

  public function find($id)
  {
    return CuentaInventario::find($id);
  }

  public function create($create)
  {
    return CuentaInventario::create($create);
  }

  public function update($update)
  {
      return CuentaInventario::update($update);
  }

  public function delete($condicion){
    return CuentaInventario::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaInventario::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaInventario::where($empresa)->get()->count() > 0){
        return  true;
    }
    return false;
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_pagar = CuentaInventario::where($condicion)->get()->last();
    if(!is_null($cuenta_pagar)){
      return $cuenta_pagar->tienes_transacciones();
    }
    return false;
  }

}

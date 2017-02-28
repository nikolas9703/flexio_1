<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaAgentePagar as CuentaAgentePagar;
//cargar el modelo codeigniter transaccion

class CuentaAgentePagarRepository{

  public function find($id)
  {
    return CuentaAgentePagar::find($id);
  }

  public function create($create)
  {
    return CuentaAgentePagar::create($create);
  }

  public function update($update)
  {
      return CuentaAgentePagar::update($update);
  }

  public function delete($condicion){
    return CuentaAgentePagar::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return $empresa;
    return CuentaAgentePagar::where($empresa)->get();
  }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaAgentePagar::where($empresa)->get()->count() > 0){
        return  true;
    }
    return false;
  }

  public function tienes_transacciones($condicion = []){
    $cuenta_pagar = CuentaAgentePagar::where($condicion)->get()->last();
    if(!is_null($cuenta_pagar)){
      return $cuenta_pagar->tienes_transacciones();
    }
    return false;
  }

}

<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaBanco as CuentaBanco;

class CuentaBancoRepository{

  public function find($id)
  {
    return CuentaBanco::find($id);
  }

  public function create($create)
  {

    $existe = CuentaBanco::where(['cuenta_id' =>$create['cuenta_id'],'empresa_id'=>$create['empresa_id']])->count();
    if($existe == 0){
    return CuentaBanco::create($create);
    }
  }

  public function update($update)
  {
      return CuentaBanco::update($update);
  }

  public function delete($condicion){
    return CuentaBanco::where(function($query) use($condicion){
      $query->where('empresa_id','=',$condicion['empresa_id']);
      $query->where('cuenta_id','=',$condicion['cuenta_id']);
    })->delete();
  }

  public function getAll($empresa=[]){
    if(empty($empresa))return collect($empresa);
    return CuentaBanco::with('cuenta')->where($empresa)->get();
  }

    public function getCollectionCuentasBanco($cuentas_banco)
    {
        return $cuentas_banco->map(function($cuenta_banco){
            return $cuenta_banco->cuenta->toArray();
        });
    }

  public function tieneCuenta($empresa=[]){
    if(empty($empresa))return false;
    if(CuentaBanco::where($empresa)->get()->count() > 0){
        return  true;
    }
    return false;
  }

  public function cuentasConfigBancos($empresa=[]){
    $cuentas = $this->getAll($empresa);
    //dd($cuentas);
    if($cuentas->count() == 0){
      return [];
    }
    return $cuentas_bancos = $cuentas->map(function($cuenta){
      return ['id' => $cuenta->cuenta->id,'nombre'=> $cuenta->cuenta->nombre];
    });


  }

  public function tienes_transacciones($condicion = []){
    $cuenta_pagar = CuentaBanco::where($condicion)->get()->last();
    if(count($cuenta_pagar)){
    return  $cuenta_pagar->tienes_transacciones();
    }
    return false;

  }

}

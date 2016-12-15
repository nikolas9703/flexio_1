<?php
namespace Flexio\Modulo\ConfiguracionContabilidad\Repository;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPlanilla as CuentaPlanilla;
//cargar el modelo codeigniter transaccion

class CuentaPlanillaRepository{

    public function find($id)
    {
        return CuentaPlanilla::find($id);
    }

    public function create($create)
    {
        return CuentaPlanilla::create($create);
    }

    public function update($update)
    {
        return CuentaPlanilla::update($update);
    }

    public function delete($condicion){
        return CuentaPlanilla::where(function($query) use($condicion){
            $query->where('empresa_id','=',$condicion['empresa_id']);
            $query->where('cuenta_id','=',$condicion['cuenta_id']);
        })->delete();
    }

    public function getAll($empresa=[]){
        if(empty($empresa))return $empresa;
        return CuentaPlanilla::where($empresa)->get();
    }

    public function tieneCuenta($empresa=[]){
        if(empty($empresa))return false;
        if(CuentaPlanilla::where($empresa)->get()->count() > 0){
            return  true;
        }
        return false;
    }

    public function tienes_transacciones($condicion = []){
        $cuenta_pagar = CuentaPlanilla::where($condicion)->get()->last();
        if(!is_null($cuenta_pagar)){
            return $cuenta_pagar->tienes_transacciones();
        }
        return false;
    }
}

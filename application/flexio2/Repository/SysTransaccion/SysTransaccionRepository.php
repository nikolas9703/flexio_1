<?php
namespace Flexio\Repository\SysTransaccion;
use Flexio\Modulo\Transaccion\Models\SysTransaccion as SysTransaccion;
use Flexio\Repository\InterfaceRepository as InterfaceRepository;
//cargar el modelo codeigniter transaccion

class SysTransaccionRepository implements InterfaceRepository{

  public function find($id)
  {
    return SysTransaccion::find($id);
  }

  public function create($create)
  {
    return SysTransaccion::create($create);
  }

  public function update($update)
  {
      return SysTransaccion::update($update);
  }

  public static function findByNombre($nombre){
    return SysTransaccion::where('nombre','=',$nombre)->count();
  }

    public function findBy($clause)
    {
        $transaciones_sistema = SysTransaccion::whereEmpresaId($clause["empresa_id"]);

        $this->_filtros($transaciones_sistema, $clause);

        return $transaciones_sistema->first();
    }

    private function _filtros($transaciones_sistema, $clause)
    {
        if(isset($clause["nombre"]) and !empty($clause["nombre"])){$transaciones_sistema->whereNombre($clause["nombre"]);}
    }

}

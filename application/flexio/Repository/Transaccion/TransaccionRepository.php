<?php
namespace Flexio\Repository\Transaccion;
use Flexio\Modulo\EntradaManual\Models\AsientoContable as AsientoContable;
use Flexio\Repository\InterfaceRepository as InterfaceRepository;
//cargar modelo de codeigniter
class TransaccionRepository implements InterfaceRepository{

  public function find($id)
  {
    return AsientoContable::find($id);
  }
  
    public function findBy($clause)
    {
        //...
    }

  public function create($create)
  {
    return AsientoContable::create($create);
  }

  public function update($update)
  {
    return AsientoContable::update($update);
  }
  public static function findByNombre($nombre){
    return null;
  }
}

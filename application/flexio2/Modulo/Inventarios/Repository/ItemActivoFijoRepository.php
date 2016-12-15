<?php
namespace Flexio\Modulo\Inventarios\Repository;

use Flexio\Modulo\Inventarios\Models\Items as Items;
use Flexio\Modulo\Inventarios\Models\Seriales as Seriales;

class ItemActivoFijoRepository{

  function getItemActivoFijo($clause){
    return Items::getItemConSerial($clause)->get();
  }

  function activo_fijo($clause){
    return Seriales::serial_items($clause)->get();
  }

}

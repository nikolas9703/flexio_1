<?php
namespace Flexio\Modulo\Cliente\SaveMany;
use Flexio\Modulo\Cliente\Models\Asignados;

class AsignadosSync{

  /**
   * @method sync()
   * @param [$new, $old]
   *        $new data nueva son los ids que provienen del FormularioDocumentos
   *        $old data ya guardada provine de la base de datos
   */

  function sync($new, $old) {
    $result_array = array_values(array_diff($old, $new));
    foreach($result_array as $id){
      Asignados::find($id)->delete();
    }
  }
}

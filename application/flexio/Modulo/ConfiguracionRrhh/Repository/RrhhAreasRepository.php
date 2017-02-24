<?php
namespace Flexio\Modulo\ConfiguracionRrhh\Repository;

use Flexio\Modulo\ConfiguracionRrhh\Models\RrhhAreas;

class RrhhAreasRepository {
    public function getAll($clause) {
       return RrhhAreas::where(function ($query) use($clause) {
        $query->where('empresa_id', '=', $clause['empresa_id']);
          $query->where('estado', '=', 1);
      })->orderBy('nombre', 'asc')->get();
    }


}

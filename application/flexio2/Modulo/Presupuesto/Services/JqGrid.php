<?php

namespace Flexio\Modulo\Presupuesto\Services;

use Flexio\Modulo\Presupuesto\Models\Presupuesto;

class JqGrid{

    function listar($clause=array(), $sidx=null, $sord=null, $limit=null, $start=null){

        return $presupuestos = Presupuesto::where(function($query) use($clause){
          $query->where('empresa_id','=',$clause['empresa_id']);
          if(isset($clause['centro_contable_id']))$query->where('centro_contable_id','=' ,$clause['centro_contable_id']);
          if(isset($clause['nombre']))$query->where('nombre','like' ,"%".$clause['nombre']."%");
          if(isset($clause['fecha1']))$query->where('fecha_inicio','>',$clause['fecha1']);
          if(isset($clause['fecha2']))$query->where('fecha_inicio','<',$clause['fecha2']);
        });


    }
}

<?php
namespace Flexio\Modulo\Inventarios\Repository;

use Flexio\Modulo\Inventarios\Models\Precios;

class PreciosRepository{

    public function get($clause = []){

        $precios = Precios::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        });
        return $precios->get();

    }

    private function _filtros($query, $clause){

        if(isset($clause['empresa_id']) and !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['tipo_precio']) and !empty($clause['tipo_precio'])){$query->whereTipoPrecio($clause['tipo_precio']);}
        if(isset($clause['estado']) and !empty($clause['estado'])){$query->whereEstado($clause['estado']);}

    }

}

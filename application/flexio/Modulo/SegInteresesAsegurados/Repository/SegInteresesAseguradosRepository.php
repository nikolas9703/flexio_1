<?php

namespace Flexio\Modulo\SegInteresesAsegurados\Repository;

use Flexio\Modulo\SegInteresesAsegurados\Models\SegInteresesAsegurados;

class SegInteresesAseguradosRepository {

    public function listar_catalogo($catalogo, $orden = "") {
        //filtros
        $lista_catalogo = SegInteresesAsegurados::where('identificador', 'LIKE', $catalogo);

        if ($orden != "")
            $lista_catalogo->orderBy($orden, 'ASC');
        else
            $lista_catalogo->orderBy('valor', 'ASC');

        return $lista_catalogo->get();
    }

}


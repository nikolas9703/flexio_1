<?php

namespace Flexio\Modulo\SegCatalogo\Repository;

use Flexio\Modulo\SegCatalogo\Models\SegCatalogo;

class SegCatalogoRepository {

    public function listar_catalogo($catalogo, $orden = "") {
        //filtros
        $lista_catalogo = SegCatalogo::where('tipo', '=', $catalogo);

        if ($orden != "")
            $lista_catalogo->orderBy($orden, 'ASC');
        else
            $lista_catalogo->orderBy('valor', 'ASC');

        return $lista_catalogo->get();
    }
    public function listar_catalogo_excepcion($catalogo, $orden2, $excepcion) {
        //filtros
        $lista_catalogo2 = SegCatalogo::where('tipo', '=', $catalogo)
                ->where('valor', '!=', $excepcion);
        //var_dump($lista_catalogo2);
        if ($orden2 != "")
            $lista_catalogo2->orderBy($orden2, 'ASC');
        else
            $lista_catalogo2->orderBy('valor', 'ASC');

        return $lista_catalogo2->get();
    }
     public function listar_catalogo_excepcion2($catalogo, $orden2, $excepcion, $excepcion2) {
        //filtros
        $lista_catalogo2 = SegCatalogo::where('tipo', '=', $catalogo)
                ->where('valor', '!=', $excepcion)
                ->where('valor', '!=', $excepcion2);
        //var_dump($lista_catalogo2);
        if ($orden2 != "")
            $lista_catalogo2->orderBy($orden2, 'ASC');
        else
            $lista_catalogo2->orderBy('valor', 'ASC');

        return $lista_catalogo2->get();
    }

}

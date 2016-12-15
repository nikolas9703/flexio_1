<?php
namespace Flexio\Modulo\Modulos\Repository;

use Flexio\Modulo\Modulos\Models\Catalogos as Catalogos;
use Flexio\Modulo\Modulos\Models\Modulos;

class ModulosRepository implements ModulosInterface{
    public function getFormasDePago() {
        return Catalogos::formasDePago()->get();
    }
    
    public function getTerminosDePago() {
        return Catalogos::terminosDePago()->get();
    }
    
    public function getTiposDeCuenta() {
        return Catalogos::tiposDeCuenta()->get();
    }
    public function getEstados() {
        return Catalogos::Estados()->get();
    }

    public function getModulos(){
        return Modulos::all();
    }
    public function find(){
        return Modulos::find('20');
    }
    public function getCollectionModulos($modulos){

        return $modulos->map(function($modulos){

            return [
                'id' => $modulos->id,
                'nombre' => json_decode($modulos->agrupador).'/'.$modulos->nombre
            ];

        });

    }
}

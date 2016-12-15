<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 19/8/16
 * Time: 9:37 AM
 */

namespace Flexio\Modulo\AccionPersonal\Repository;

use Flexio\Modulo\AccionPersonal\Models\AccionPersonal as AccionPersonal;

class AccionPersonalRepository
{
    public function find($id){
        return AccionPersonal::find($id);
    }

   /* function agregarComentario($id, $comentarios) {
        $proveedor = AccionPersonal::find($id);
        $comentario = new Comentario($comentarios);
        $proveedor->comentario_timeline()->save($comentario);
        return $proveedor;
    }*/
}
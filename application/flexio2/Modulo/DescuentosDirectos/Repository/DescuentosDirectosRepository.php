<?php
namespace Flexio\Modulo\DescuentosDirectos\Repository;

use Flexio\Modulo\DescuentosDirectos\Models\DescuentosDirectos;
use Flexio\Modulo\Comentario\Models\Comentario;
use Illuminate\Database\Capsule\Manager as Capsule;

class DescuentosDirectosRepository
{

    function findByUuid($uuid) {
        return DescuentosDirectos::where('uuid_descuento',hex2bin($uuid))->first();
    }
    function find($id){
        return DescuentosDirectos::find($id);
    }
    function agregarComentario($id, $comentarios) {
        $decuento = DescuentosDirectos::find($id);
        $comentario = new Comentario($comentarios);
        $decuento->comentario_timeline()->save($comentario);
        return $decuento;
    }
}
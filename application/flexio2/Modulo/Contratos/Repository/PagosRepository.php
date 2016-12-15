<?php
namespace Flexio\Modulo\Contratos\Repository;

use Flexio\Modulo\Contratos\Models\Pagos as Pagos;
use Flexio\Modulo\Comentario\Models\Comentario;

class PagosRepository
{
    function findByUuid($uuid) {
        return Pagos::where('uuid_pago',hex2bin($uuid))->first();
    }
    function agregarComentario($id, $comentarios) {
        $pagos = Pagos::find($id);
        $comentario = new Comentario($comentarios);
        $pagos->comentario_timeline()->save($comentario);
        return $pagos;
    }
}
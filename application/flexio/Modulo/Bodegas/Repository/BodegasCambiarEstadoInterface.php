<?php
namespace Flexio\Modulo\Bodegas\Repository;
interface BodegasCambiarEstadoInterface{
    public function cambiarEstado($bodega, $estado);
}

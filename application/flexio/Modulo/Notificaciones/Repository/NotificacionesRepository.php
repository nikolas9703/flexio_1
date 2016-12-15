<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 1/11/16
 * Time: 4:47 PM
 */

namespace Flexio\Modulo\Notificaciones\Repository;
use Flexio\Modulo\Notificaciones\Models\Notificaciones;

class NotificacionesRepository
{
    public function listar( $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $notificaciones = Notificaciones::all();
        return $notificaciones;
    }
}
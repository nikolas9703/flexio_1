<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 31/10/16
 * Time: 7:59 PM
 */

namespace Flexio\Modulo\Notificaciones\Repository;

use Flexio\Modulo\Notificaciones\Models\NotificacionesCatalog as NotificacionesCatalog;

class NotificacionesCatalogRepository
{

    function getEstados(){
        return NotificacionesCatalog::where('tipo','estado')->get(array('valor', 'etiqueta'));
    }
    function getNotificaciones(){
        return NotificacionesCatalog::where('tipo','notificacion')->get(array('valor', 'etiqueta'));
    }
    function getOperador(){
        return NotificacionesCatalog::where('tipo','operador')->get(array('valor', 'etiqueta'));
    }

    public function findIds($ids=null){
        return NotificacionesCatalog::whereIn('valor',$ids)->get();
    }
}
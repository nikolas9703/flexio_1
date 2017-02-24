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
    public function listar($empresa_id=NULL, $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $notificaciones = Notificaciones::where('empresa_id','=',$empresa_id);
        //dd($notificaciones->get()->toArray());

       // if($sidx!=NULL && $sord!=NULL){$notificaciones->orderBy($sidx, $sord);}
       // if($limit!=NULL){$notificaciones->skip($start)->take($limit);}

        return $notificaciones;
    }

    private function _filters($query, $clause)
    {
        if(isset($clause['empresa_id']) && !empty($clause['empresa_id'])){$query->whereEmpresaId($clause['empresa_id']);}
        if(isset($clause['modulo_id']) && !empty($clause['modulo_id'])){$query->whereModulo($clause['modulo_id']);}
        if(isset($clause['transaccion_id']) && !empty($clause['transaccion_id'])){$query->whereTransaccion($clause['transaccion_id']);}
        if(isset($clause['estado']) && !empty($clause['estado'])){$query->whereEstado($clause['estado']);}
    }

    public function get($clause=[], $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $notificaciones = Notificaciones::where(function($query) use ($clause) {
            $this->_filters($query, $clause);
        });

       if($sidx!=NULL && $sord!=NULL){$notificaciones->orderBy($sidx, $sord);}
       if($limit!=NULL){$notificaciones->skip($start)->take($limit);}

        return $notificaciones->get();
    }
}

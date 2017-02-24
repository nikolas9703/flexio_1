<?php

namespace Flexio\Notifications;

use Flexio\Modulo\Notificaciones\Repository\NotificacionesRepository;

//models

trait Notify
{
    public function sendNotify($transaccion_id)
    {
        $NotificacionesRepository = new NotificacionesRepository;
        $clause = [
            'modulo_id' => $this->modulo_id,
            'transaccion_id' => $transaccion_id,
            'empresa_id' => $this->empresa_id,
            'estado' => 'activo'
        ];        
        $notifications = $NotificacionesRepository->get($clause);
        $ChannelManager = new \Flexio\Notifications\ChannelManager();        
        foreach($notifications as $notification)
        {
            //Notifications
            //falta la logica de canales                  
            $evento = new $this->modulo_notificaciones($this, $this->_getNotifyTypes($notification->tipo_notificacion));      
     
            $usuarios = \Flexio\Modulo\Usuarios\Models\Usuarios::find($notification->usuarios);
            $ChannelManager->send($usuarios, $evento);
        }
    }

    private function _getNotifyTypes($notifications_types)
    {
        return array_map(function($type){
            if($type == 'alarma_sistema')
            {
                return 'database';
            }
            elseif($type == 'notificacion_escritorio')
            {
                return 'desktop';
            }
            else if($type == 'correo')
            {
                return 'mail';
            }
        }, $notifications_types);
    }
}

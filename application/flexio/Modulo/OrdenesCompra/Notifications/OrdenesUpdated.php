<?php

namespace Flexio\Modulo\OrdenesCompra\Notifications;

use Flexio\Notifications\Notification;
use Flexio\Notifications\Messages\MailMessage;

class OrdenesUpdated extends Notification
{

    private $orden;
    private $vias;

    public function __construct(\Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra $orden, $vias = ['database'])
    {
        $this->orden = $orden;
        $this->vias = $vias;
    }

    public function via($notifiable)
    {
        return $this->vias;
    }

    public function toMail($notifiable)
    {
        /*return (new MailMessage)
            ->line("<strong>{$this->pedido->codigo}</strong> cambi&oacute; a <strong>{$this->pedido->estado->etiqueta}</strong>.");*/
    }

    public function toDesktop($notifiable)
    {
        return $this->toArray($notifiable);
    }

    public function toArray($notifiable)
    {
        return [
            'href' => $this->orden->enlace,
            'class' => $this->orden->icono,
            'text' => "<strong>{$this->orden->codigo}</strong> cambi&oacute; a <strong>{$this->orden->estado->etiqueta}</strong>.",
            'to_desktop' => in_array('desktop', $this->vias)
        ];
    }

}

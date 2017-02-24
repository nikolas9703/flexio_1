<?php

namespace Flexio\Modulo\Pedidos\Notifications;

use Flexio\Notifications\Notification;
use Flexio\Notifications\Messages\MailMessage;

class PedidoUpdated extends Notification
{

    private $pedido;
    private $vias;

    public function __construct(\Flexio\Modulo\Pedidos\Models\Pedidos $pedido, $vias = ['database'])
    {
        $this->pedido = $pedido;
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
            'href' => $this->pedido->enlace,
            'class' => $this->pedido->icono,
            'text' => "<strong>{$this->pedido->codigo}</strong> cambi&oacute; a <strong>{$this->pedido->estado->etiqueta}</strong>.",
            'to_desktop' => in_array('desktop', $this->vias)
        ];
    }

}

<?php

namespace Flexio\Modulo\FacturasCompras\Notifications;

use Flexio\Notifications\Notification;
use Flexio\Notifications\Messages\MailMessage;

class FacturasUpdated extends Notification
{

    private $facturas;
    private $vias;

    public function __construct(\Flexio\Modulo\FacturasCompras\Models\FacturaCompra $facturas, $vias = ['database'])
    {
        $this->facturas = $facturas;
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
            'href' => $this->facturas->enlace,
            'class' => $this->facturas->icono,
            'text' => "<strong>{$this->facturas->codigo}</strong> cambi&oacute; a <strong>{$this->facturas->estado->valor}</strong>.",
            'to_desktop' => in_array('desktop', $this->vias)
        ];
    }

}

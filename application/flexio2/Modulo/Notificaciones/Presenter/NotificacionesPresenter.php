<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 7/11/16
 * Time: 4:24 PM
 */

namespace Flexio\Modulo\Notificaciones\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class NotificacionesPresenter extends Presenter
{
    protected $notificaciones;

    private $labelEstado = [
     //   1 => 'label-warning',
        'inactivo' => 'label-danger',
        //3 => 'label-warning',
        'activo' => 'label-successful',
        //5 => 'label-dark'
    ];

    private $labelMonto = [
        'inactivo' => 'outline-danger',
        'activo' => 'outline-success',
    ];
    public function __construct($notificacion) {
        $this->notificaciones = $notificacion;
    }
    function estado_label() {
        if(is_null($this->notificaciones->estados)){
            return '';
        }
        $color = $this->labelEstado[$this->notificaciones->estado];
        return '<label class="label '.$color.'">'.$this->notificaciones->estados->etiqueta.'</label>';

    }
    function montos() {
        if(is_null($this->notificaciones->estados)){
            return '';
        }
        try{
            $color = $this->labelMonto[$this->notificaciones->estado];
            return '<label class="label-outline '.$color.'">$' . FormatoMoneda::numero($this->notificaciones->monto) . '</label>';
        }catch(\Exception $e){
            return '<label class="label-outline">$' . FormatoMoneda::numero($this->notificaciones->monto) . '</label>';
        }
    }

}
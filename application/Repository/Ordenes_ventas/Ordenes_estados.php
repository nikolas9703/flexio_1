<?php

class Ordenes_estados{
    protected $orden_venta;

    protected $estado;

    function __construct($model){
        $this->orden_venta = $model;
        $this->fire();
    }

    function fire(){

      $estado = $this->orden_venta->estado;
      $cotizacion = $this->orden_venta->cotizacion;
        if($estado =='anulada' && !is_null($cotizacion)){
          $cotizacion->estado ='perdido';
          $cotizacion->comentario = 'Orden de venta '.$this->orden_venta->codigo. ' Anulada';
          $cotizacion->save();
        }
    }
}

<?php
class Factura_estados{


  function manipularEstado($facturaId){
    $this->facturaMontoEstado($facturaId);
  }

  function facturaMontoEstado($facturaId){

    foreach($facturaId as $id){
      $factura = Factura_orm::find($id);
          if($factura->total_facturado() < $factura->total){
            $this->setEstadoFactura($factura,'cobrado_parcial');
          }else if($factura->total_facturado() == $factura->total){
           $this->setEstadoFactura($factura);
          }
    }
  }

  function setEstadoFactura($factura, $estado = 'cobrado_completo'){
    $factura->estado = $estado;
    $factura->save();
  }

}

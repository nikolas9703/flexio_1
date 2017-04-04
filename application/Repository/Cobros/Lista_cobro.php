<?php
class Lista_cobro{

  function color_monto($estado=null){

    if($estado=="pago_parcial"){

      return 'totales-warning';

  }elseif($estado=="aplicado"){

      return 'totales-success';

    }elseif($estado=="anulada"){

      return 'totales-danger';

    }else{
      return '';
    }
  }

  function referencia($referencia){
      $ref="";
          foreach($referencia as $metodo){
              if($metodo->tipo_pago == "ach"){
                  $new_ref= json_decode($metodo->referencia);
                  $ref .= $new_ref->cuenta_cliente. ", ";
              }elseif($metodo->tipo_pago == "cheque"){
                  $new_ref= json_decode($metodo->referencia);
                  $ref .= $new_ref->numero_cheque. ", ";
              }elseif($metodo->tipo_pago == "tarjeta_de_credito"){
                  $new_ref= json_decode($metodo->referencia);
                  $ref .= $new_ref->numero_recibo. ", ";
              }
          }
          return $ref;
      }

function metodo_pago(Illuminate\Database\Eloquent\Collection $metodo_pago){
    $tipo_pago="";

    foreach($metodo_pago as $metodo){
      $tipo_pago .=$metodo->catalogo_metodo_pago->valor. " ";
    }

    return $tipo_pago;
}

}

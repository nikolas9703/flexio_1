<?php

namespace Flexio\Modulo\OrdenesVentas\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class OrdenesVentaPresenter extends Presenter{

  protected $ordenVenta;

  private $labelEstado = [
    'por_facturar'=>'label-danger',
    'facturado_parcial'=>'label-warning',
    'anulada'=>'label-dark',
    'facturado_completo'=>'label-successful',
    'abierta'=>'label-warning',
    'ganado'=>'label-warning',
    'por_aprobar'=>'label-warning',
  ];
  
  private $labelMonto = [
    1 => 'outline-warning',
    2 => 'outline-danger',
    3 => 'outline-warning',  
    4 => 'outline-success',
    5 => 'outline-dark'
  ];

  public function __construct($ordenVenta){
    $this->ordenVenta = $ordenVenta;
  }


  function estado_label(){
    if (is_null($this->ordenVenta->etapa_catalogo)) {
      return '';
    }
    try{
      $color = $this->labelEstado[$this->ordenVenta->estado];
      return '<label class="label '.$color.'">'.$this->ordenVenta->etapa_catalogo->valor.'</label>';
    }catch(\Exception $e){
      return '';
    }
  }
  
  function monto() {
   if(is_null($this->ordenVenta->total)){
        return '';
    }

    try{
        
        $total = (int) $this->ordenVenta->total;
        $color_id = '';
        if ($total < 500) {
            $color_id = 2;
        } else if ($total > 500 and $total < 1000) {
            $color_id = 3;
        } else if ($total > 1000 and $total < 5000) {
            $color_id = 4;
        } else if ($total > 5000) {
            $color_id = 5;
        }
    $color = $this->labelMonto[$color_id];
     return '<label class="label-outline '.$color.'">$' . FormatoMoneda::numero($this->ordenVenta->total) . '</label>';
     }catch(\Exception $e){
      return '<label class="label-outline">$' . FormatoMoneda::numero($this->ordenVenta->total) . '</label>';
    }
  }





}

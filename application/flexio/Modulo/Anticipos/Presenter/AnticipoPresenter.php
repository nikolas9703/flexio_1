<?php
namespace Flexio\Modulo\Anticipos\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;


class AnticipoPresenter extends Presenter{

  protected $anticipo;

  private $empezable = [  'orden_compra' => 'Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra'];

  private $labelEstado = [
    'por_aprobar' => 'label-warning',
    'anulado'=>'label-dark',
    'aprobado'=>'label-successful',

  ];

  private $labelOutline = [
    'por_aprobar' => 'outline-warning',
    'anulado' => 'outline-dark',
    'aprobado' => 'outline-success'
  ];

  public function __construct($anticipo) {
    $this->anticipo = $anticipo;
  }


  function estado_label() {
    if (is_null($this->anticipo->catalogo_estado)) {
      return '';
    }

      $color="";
      $cambiarEstado="";
      if(array_key_exists($this->anticipo->estado, $this->labelEstado)){
         $color = $this->labelEstado[$this->anticipo->estado];
         $cambiarEstado = $this->anticipo->estado == "por_aprobar"?'menu-estado cambiarEstado':'';
      }
      
      return '<label id="'.$this->anticipo->id.'" class="label '.$color.' '.$cambiarEstado.'">'.$this->anticipo->catalogo_estado->valor.'</label>';
  }

  function monto() {
    $color="";
    if(array_key_exists($this->anticipo->estado, $this->labelOutline)){
     $color = $this->labelOutline[$this->anticipo->estado];
     }
     return '<label class="label-outline '.$color.'">$' . FormatoMoneda::numero($this->anticipo->monto) . '</label>';
  }

  function metodo_anticipo(){
      if (is_null($this->anticipo->catalogo_anticipo)) {
        return '';
      }
      return $this->anticipo->catalogo_anticipo->valor;
  }

  function documento(){

        return '<a class="link" href="'. $this->anticipo->documento_enlace.'">'.$this->anticipo->numero_documento.'</a>';
  }

  function nombre_anticipable(){
      if(is_null($this->anticipo->anticipable)){
          return '';
      }

      return '<a class="link" href="'. $this->anticipo->anticipable->enlace.'">'.$this->anticipo->anticipable->nombre.'</a>';

  }
}

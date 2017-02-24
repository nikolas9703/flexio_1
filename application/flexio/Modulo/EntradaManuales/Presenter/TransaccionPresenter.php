<?php
namespace Flexio\Modulo\EntradaManuales\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class TransaccionPresenter extends Presenter{

    protected $scoped;

    private $labelEstado = [
      'anulado'=>'label-dark',
      'aplicado'=>'label-successful'
    ];

    private $labelOutline = [
      'anulado' => 'outline-dark',
      'aplicado' => 'outline-success'
    ];

    public function __construct($scoped) {
      $this->scoped = $scoped;
    }

    function debito(){
        return '<label class="label label-successful">$'.FormatoMoneda::numero($this->scoped->debito).'</label>';
    }
    function credito(){
        return '<label class="label label-warning">$'.FormatoMoneda::numero($this->scoped->credito).'</label>';
    }

    function credito_entrada(){
        return '<label class="label label-danger">$'.FormatoMoneda::numero($this->scoped->credito).'</label>';
    }

    function enlace(){
        $enlace = "";
        if($this->scoped->transaccionable_type =='Flexio\Modulo\EntradaManuales\Models\EntradaManual'){
            return $this->scoped->transaccionable->enlace;
        }else if(!is_null($this->scoped->transaccionable->linkable)){
            return $enlace = $this->scoped->transaccionable->linkable->enlace;
        }
        return $enlace;

    }
    function codigo(){
        /*
        * systema o manual
        */

        $codigo = $this->scoped->codigo;
        $enlace = "";
        if(empty($codigo)){
            $codigo = $this->scoped->nombre;
        }
        if(!empty($this->enlace())){
            return '<a href="'.$this->enlace().'">'.$codigo.'</a>';
        }
        return $codigo;
    }

    function cuenta_contable(){
        
    }


}

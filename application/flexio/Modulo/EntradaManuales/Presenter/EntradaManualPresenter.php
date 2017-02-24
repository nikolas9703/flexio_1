<?php
namespace Flexio\Modulo\EntradaManuales\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class EntradaManualPresenter extends Presenter{
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
        return '<label class="label label-successful">$'.FormatoMoneda::numero($this->scoped->transacciones->sum('debito')).'</label>';
    }
    function credito(){
        return '<label class="label label-danger">$'.FormatoMoneda::numero($this->scoped->transacciones->sum('credito')).'</label>';
    }

    function fecha_hora(){
        return $this->scoped->created_at->format('d/m/Y \\@ h:i:s A');
    }
}

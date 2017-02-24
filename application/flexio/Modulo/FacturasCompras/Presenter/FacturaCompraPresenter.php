<?php

namespace Flexio\Modulo\FacturasCompras\Presenter;

use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;
use Flexio\Modulo\Documentos\Repository\DocumentosRepository as DocumentosRepository;

class FacturaCompraPresenter extends Presenter
{
    protected $facturaCompra;
    protected $DocumentosRepository;

    private $labelEstado = [
        13 => 'label-danger',
        14 => 'label-warning',
        15 => 'label-info',
        16 => 'label-successful',
        17 => 'label-dark',
        20 => 'label-gray',
    ];

    private $badgeEstado = [
        13 => 'badge-danger',
        14 => 'badge-warning',
        15 => 'badge-info',
        16 => 'badge-successful',
        17 => 'badge-dark2',
        20 => 'badge-gray',
    ];

    public function __construct($facturaCompra)
    {
        $this->facturaCompra = $facturaCompra;
    }

    public function estado_label()
    {
        //areglar este metodo para facturas compras
        if (is_null($this->facturaCompra->estado)) {
            return '';
        }

        $color = '';
        if (array_key_exists($this->facturaCompra->estado_id, $this->labelEstado)) {
            $color = $this->labelEstado[$this->facturaCompra->estado_id];
        }

        return '<label
        data-id="'.$this->facturaCompra->id.'" data-uuid="'.$this->facturaCompra->uuid_factura.'" class="label change-state-btn '.$color.'">'.$this->facturaCompra->estado->valor.'</label>';
    }

    public function total()
    {
        if (is_numeric($this->facturaCompra->total)) {
            return '<label class="label-outline outline-success">$'.FormatoMoneda::numero($this->facturaCompra->total).'</label>';
        }

        return '';
    }

    public function saldo()
    {
        return '<label class="label-outline outline-danger">$'.FormatoMoneda::numero($this->facturaCompra->saldo).'</label>';
    }

    public function pagos()
    {
        //Muestra la cantidad de pagos que tiene cada factura #S-1227
      $color = '';
        if (array_key_exists($this->facturaCompra->estado_id, $this->badgeEstado)) {
            $color = $this->badgeEstado[$this->facturaCompra->estado_id];
        }
        $tooltip = $this->facturaCompra->pagos->implode('codigo', ', ');
        if (count($this->facturaCompra->pagos) > 0 && $this->facturaCompra->estado_id == 14 || $this->facturaCompra->estado_id == 15) {
            return '<span class="badge '.$color.'" data-toggle="tooltip" data-placement="bottom" title="'.$tooltip.'">'.count($this->facturaCompra->pagos).'</span>';
        } elseif (count($this->facturaCompra->pagos) > 0 && $this->facturaCompra->estado_id == 16) {
            return '<span class="badge '.$color.'" data-toggle="tooltip" data-placement="bottom" title="'.$tooltip.'">'.count($this->facturaCompra->pagos).'</span>';
        } elseif (count($this->facturaCompra->pagos) > 0 && $this->facturaCompra->estado_id == 17) {
            return '<span class="badge '.$color.'" data-toggle="tooltip" data-placement="bottom" title="'.$tooltip.'">'.count($this->facturaCompra->pagos).'</span>';
        } else {
            return '<span class="badge '.$color.'" data-toggle="tooltip" data-placement="bottom" title="'.$tooltip.'">'.count($this->facturaCompra->pagos).'</span>';
        }
    }

    public function vendedor()
    {
    }

    public function cliente()
    {
    }

    public function documentos()
    {
        $clause['facturacompra_id'] = $this->facturaCompra->id;
        $this->DocumentosRepository = new DocumentosRepository();
        $count = $this->DocumentosRepository->cantidad($clause)->count();

        return '<a href="#" data-id="'.$this->facturaCompra->id.'" class="documentosFiltro"><span class="badge badge-success">'.$count.'</span></a>';
    }
}

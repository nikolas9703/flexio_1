<?php

namespace Flexio\Modulo\ConfiguracionCompras\Presenter;

use Flexio\Presenter\Presenter;

class TerminoCondicionPresenter extends Presenter
{
    protected $termino_condicion;

    private $labelEstado = [
        'inactivo' => 'label-danger',
        'activo' => 'label-successful',
    ];

    private $estadoValor = [
        'inactivo' => 'Inactivo',
        'activo' => 'Activo',
    ];

    private $modules = [
        'pedidos' => 'Pedidos',
        'ordenes' => '&Oacute;rdenes de compra',
        'facturas_compras' => 'Facturas de compra',
        'cotizaciones' => 'Cotizaciones',
        'ordenes_ventas' => '&Oacute;rdenes de venta',
        'facturas' => 'Facturas de venta',
    ];

    public function __construct($termino_condicion)
    {
        $this->termino_condicion = $termino_condicion;
    }

    public function estado()
    {
        $color = '';
        if (array_key_exists($this->termino_condicion->estado, $this->labelEstado)) {
            $color = $this->labelEstado[$this->termino_condicion->estado];
        }
        return '<label data-id="'.$this->termino_condicion->id.'" class="label cambiar-estado-btn '.$color.'">'.$this->estadoValor[$this->termino_condicion->estado].'</label>';
    }

    public function categorias()
    {
        return $this->termino_condicion->categorias->implode('nombre', ', ');
    }

    public function modulo()
    {
        return $this->modules[$this->termino_condicion->modulo];
    }
}

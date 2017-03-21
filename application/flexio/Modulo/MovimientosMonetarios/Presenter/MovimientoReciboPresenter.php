<?php

namespace Flexio\Modulo\MovimientosMonetarios\Presenter;

use Flexio\Presenter\Presenter;
use Flexio\Modulo\MovimientosMonetarios\Models\MovimientoRecibo;

class MovimientoReciboPresenter extends Presenter
{
    protected $scope;

    public function __construct(MovimientoRecibo $scope)
    {
        $this->scope = $scope;
    }

    public function created_at()
    {
        return $this->scope->created_at->format('d/m/Y');
    }
}

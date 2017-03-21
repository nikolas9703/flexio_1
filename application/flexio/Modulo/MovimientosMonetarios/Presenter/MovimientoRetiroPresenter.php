<?php

namespace Flexio\Modulo\MovimientosMonetarios\Presenter;

use Flexio\Presenter\Presenter;
use Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros as MovimientoRetiro;

class MovimientoRetiroPresenter extends Presenter
{
    protected $scope;

    public function __construct(MovimientoRetiro $scope)
    {
        $this->scope = $scope;
    }

    public function created_at()
    {
        return $this->scope->created_at->format('d/m/Y');
    }
}

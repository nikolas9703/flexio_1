<?php

namespace Flexio\Modulo\FacturasCompras\Services;

use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository;
use Flexio\Modulo\SubContratos\Repository\SubContratoRepository;

class FacturaCompraEmpezable
{

    protected $OrdenesCompraRepository;
    protected $SubContratoRepository;

    public function __construct()
    {
        $this->OrdenesCompraRepository = new OrdenesCompraRepository;
        $this->SubContratoRepository = new SubContratoRepository;
    }

    public function getResponse($post)
    {
        return call_user_func_array([$this, $post['type']], [0 => $post]);
    }

    public function orden_compra($post)
    {
        $response = $this->OrdenesCompraRepository->find($post['id']);
        return count($response) ? $this->OrdenesCompraRepository->getOrdenCompra($response) : [];
    }

    public function subcontrato($post)
    {
        $response = $this->SubContratoRepository->findBy($post['id']);
        return count($response) ? $this->SubContratoRepository->getSubContrato($response) : [];
    }

}

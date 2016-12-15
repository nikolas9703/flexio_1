<?php
namespace Flexio\Modulo\ContratosAlquiler\Events;
use Flexio\Modulo\ContratosAlquiler\Repository\CargosRepository;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler;

class CambiarEstadoCargosContratoAlquilerEvent{
  protected $orden_venta_alquiler;
  protected $CargosRepository;
  function __construct($orden_venta)
  {
    $this->orden_venta_alquiler = $orden_venta;
    $this->CargosRepository = new CargosRepository();
  }

  function actualizarEstadoCargosContrato(){
    $clause = array(
      'contrato_id' => $this->orden_venta_alquiler->contrato_id,
      'empresa_id'  => $this->orden_venta_alquiler->empresa_id
    );
    $response = $this->CargosRepository->getCargosDeContratoPorfacturar($clause);
    CargosAlquiler::whereIn("id", $response["cargos"])->update(array("estado" => "facturado"));
  }
}

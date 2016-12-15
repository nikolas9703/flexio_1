<?php
namespace Flexio\Strategy\Transacciones\Anular;

interface InterfaceAnular{
  public function deshacerTransaccion($modelo);
}

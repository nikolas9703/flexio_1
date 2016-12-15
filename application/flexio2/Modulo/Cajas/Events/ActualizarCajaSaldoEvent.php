<?php

namespace Flexio\Modulo\Cajas\Events;

class ActualizarCajaSaldoEvent {
	protected $transferencia;
	protected $caja;
	
	function __construct($transferencia, $caja) {
		$this->transferencia = $transferencia;
		$this->caja = $caja;
	}
	function actualizarCajaSaldo() {
		$nuevo_saldo = $this->caja->saldo + $this->transferencia->monto;
		$this->caja->saldo = $nuevo_saldo;
		$this->caja->save();
	}
}

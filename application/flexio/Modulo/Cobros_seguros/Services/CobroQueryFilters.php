<?php
namespace Flexio\Modulo\Cobros_seguros\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class CobroQueryFilters extends QueryFilters{

	function codigo($codigo){
		return $this->builder->where('codigo','like',"%".$codigo."%");
	}

	function empresa($empresa){
		return $this->builder->where('cob_cobros.empresa_id',$empresa);
	}

	function cliente ($cliente){
		return $this->builder->where('cliente_id',$cliente);
	}
	
	function clientenombre ($cliente){
		$this->builder->join("cli_clientes", "cli_clientes.id", "=", "cob_cobros.cliente_id");
		return $this->builder->where('cli_clientes.nombre','LIKE','%'.$cliente.'%');
	}
	
	function metodoPago($metodo){
		$this->builder->join("cob_cobro_metodo_pago", "cob_cobro_metodo_pago.cobro_id", "=", "cob_cobros.id");
		return $this->builder->where('cob_cobro_metodo_pago.tipo_pago','LIKE','%'.$metodo.'%');
	}

	function fecha_min($fecha){
		$fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
		return $this->builder->where('fecha_pago','>=',$fecha);
	}

	function fecha_max($fecha){
		$fecha = Carbon::createFromFormat('d/m/Y', $fecha, 'America/Panama');
		return $this->builder->where('fecha_pago','<=',$fecha);
	}

	function estado($estado){
		return $this->builder->where('cob_cobros.estado',$estado);
	}

	function factura($factura_id){
		return $this->builder->where('empezable_id', $factura_id)->where('empezable_type','Flexio\Modulo\FacturasVentas\Models\FacturaVenta');
	}

}

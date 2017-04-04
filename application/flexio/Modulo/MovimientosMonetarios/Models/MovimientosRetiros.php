<?php
namespace Flexio\Modulo\MovimientosMonetarios\Models;

use Flexio\Modulo\Pagos\Observer\PagosObserver;
use \Illuminate\Database\Eloquent\Model as Model;
use Carbon\Carbon;

use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Politicas\PoliticableTrait;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Util\GenerarCodigo;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\MovimientosMonetarios\Models\ItemsRetiros;
use Flexio\Modulo\Documentos\Models\Documentos;

class MovimientosRetiros extends Model
{
  protected $table = 'mov_retiro_dinero';
  protected $fillable = ['uuid_retiro_dinero', 'codigo', 'narracion', 'created_at', 'updated_at', 'fecha_inicio', 'empresa_id', 'cliente_id', 'proveedor_id', 'estado', 'cuenta_id', 'tipo_pago_id', 'incluir_narracion'];
  protected $guarded = ['id'];

  public function items()
	{
		return $this->hasMany('Flexio\Modulo\MovimientosMonetarios\Models\ItemsRetiros', 'id_retiro');
	}

  function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public function pagos()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables', '', "pago_id")
        ->withPivot('monto_pagado','empresa_id')->withTimestamps();
    }

    public function pagos_aplicados()
    {
        return $this->pagos()->where("pag_pagos.estado", "aplicado");
    }

    public function getPagadoAttribute()
    {
        return $this->pagos_aplicados->sum('pivot.monto_pagado');
    }

    public function getPagosAplicadosSumaAttribute()
    {
        return $this->pagado;
    }

    public function pagos_todos()
    {
        return $this->pagos()->where("pag_pagos.estado", "!=", "anulado");
    }

    public function getPagosTodosSumaAttribute()
    {
        return $this->pagos_todos()->sum("pag_pagos_pagables.monto_pagado");
    }

    public function getSaldoAttribute()
    {
        return $this->items->sum('debito') - $this->pagado;
    }
}

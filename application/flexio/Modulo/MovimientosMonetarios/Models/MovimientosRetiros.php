<?php

namespace Flexio\Modulo\MovimientosMonetarios\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Documentos\Models\Documentos;

//utils
use Flexio\Library\Util\FlexioSession;

class MovimientosRetiros extends Model
{
    protected $table = 'mov_retiro_dinero';
    protected $fillable = ['uuid_retiro_dinero', 'codigo', 'narracion', 'created_at', 'updated_at', 'fecha_inicio', 'empresa_id', 'cliente_id', 'proveedor_id', 'estado', 'cuenta_id', 'tipo_pago_id', 'incluir_narracion', 'usuario_id', 'numero_cheque', 'numero_banco_cheque', 'numero_tarjeta', 'numero_recibo', 'banco_proveedor', 'numero_cuenta_proveedor'];
    protected $guarded = ['id', 'uuid_retiro_dinero'];
    protected $session;

    public function __construct(array $attributes = array())
    {
        $this->setUtils(new FlexioSession);
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_retiro_dinero' => Capsule::raw('ORDER_UUID(uuid())')
        )), true);
        parent::__construct($attributes);
    }

    public function setUtils(FlexioSession $session)
    {
        $this->session = $session;
    }

    public function getUuidRetiroDineroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function proveedor()
    {
        return $this->belongsTo('Flexio\Modulo\Proveedores\Models\Proveedores', 'proveedor_id');
    }

    public function cliente()
    {
        return $this->belongsTo('Flexio\Modulo\Cliente\Models\Cliente', 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo('Flexio\Modulo\Usuarios\Models\Usuarios', 'usuario_id');
    }

    public function items()
    {
        return $this->hasMany('Flexio\Modulo\MovimientosMonetarios\Models\ItemsRetiros', 'id_retiro');
    }

    public function documentos()
    {
        return $this->morphMany(Documentos::class, 'documentable');
    }

    public function pagos()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables', '', 'pago_id')
        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }

    public function pagos_aplicados()
    {
        return $this->pagos()->where('pag_pagos.estado', 'aplicado');
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
        return $this->pagos()->where('pag_pagos.estado', '!=', 'anulado');
    }

    public function getPagosTodosSumaAttribute()
    {
        return $this->pagos_todos()->sum('pag_pagos_pagables.monto_pagado');
    }

    public function getSaldoAttribute()
    {
        return $this->items->sum('debito') - $this->pagado;
    }

    public function landing_comments()
    {
        return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario', 'comentable');
    }

    public function collection()
    {
        return new \Flexio\Modulo\MovimientosMonetarios\Collection\MovimientoRetiroCollection($this);
    }

    public function present()
    {
        return new \Flexio\Modulo\MovimientosMonetarios\Presenter\MovimientoRetiroPresenter($this);
    }
}

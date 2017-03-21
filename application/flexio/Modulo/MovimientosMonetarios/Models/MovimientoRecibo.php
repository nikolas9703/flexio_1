<?php

namespace Flexio\Modulo\MovimientosMonetarios\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

//utils
use Flexio\Library\Util\FlexioSession;

class MovimientoRecibo extends Model
{
    protected $table = 'mov_recibo_dinero';
    protected $fillable = ['codigo', 'narracion', 'created_at', 'updated_at', 'empresa_id', 'cliente_id', 'proveedor_id', 'estado', 'cuenta_id', 'incluir_narracion', 'fecha_inicio', 'usuario_id'];
    protected $guarded = ['id', 'uuid_recibo_dinero'];
    protected $session;

    public function __construct(array $attributes = array())
    {
        $this->setUtils(new FlexioSession);
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_recibo_dinero' => Capsule::raw('ORDER_UUID(uuid())')
        )), true);
        parent::__construct($attributes);
    }

    public function setUtils(FlexioSession $session)
    {
        $this->session = $session;
    }

    public function getUuidReciboDineroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function items()
    {
        return $this->hasMany('Flexio\Modulo\MovimientosMonetarios\Models\ItemRecibo', 'id_recibo');
    }

    public function documentos()
    {
        return $this->morphMany('Flexio\Modulo\Documentos\Models\Documentos', 'documentable');
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

    //this method is not verified -> refavtory card 249.26
    public function pagos()
    {
        return $this->morphToMany('Flexio\Modulo\Pagos\Models\Pagos', 'pagable', 'pag_pagos_pagables', '', 'pago_id')
        ->withPivot('monto_pagado', 'empresa_id')->withTimestamps();
    }

    //this method is not verified -> refavtory card 249.26
    public function pagos_aplicados()
    {
        return $this->pagos()->where('pag_pagos.estado', 'aplicado');
    }

    //this method is not verified -> refavtory card 249.26
    public function getPagadoAttribute()
    {
        return $this->pagos_aplicados->sum('pivot.monto_pagado');
    }

    //this method is not verified -> refavtory card 249.26
    public function getPagosAplicadosSumaAttribute()
    {
        return $this->pagado;
    }

    //this method is not verified -> refavtory card 249.26
    public function pagos_todos()
    {
        return $this->pagos()->where('pag_pagos.estado', '!=', 'anulado');
    }

    //this method is not verified -> refavtory card 249.26
    public function getPagosTodosSumaAttribute()
    {
        return $this->pagos_todos()->sum('pag_pagos_pagables.monto_pagado');
    }

    //this method is not verified -> refavtory card 249.26
    public function getSaldoAttribute()
    {
        return $this->items->sum('credito') - $this->pagado;
    }

    public function landing_comments()
    {
        return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario', 'comentable');
    }

    public function collection()
    {
        return new \Flexio\Modulo\MovimientosMonetarios\Collection\MovimientoReciboCollection($this);
    }

    public function present()
    {
        return new \Flexio\Modulo\MovimientosMonetarios\Presenter\MovimientoReciboPresenter($this);
    }

}

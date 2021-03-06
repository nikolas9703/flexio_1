<?php
namespace Flexio\Modulo\Cobros\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FlexioSession;

class CobroFactura extends Model
{
    protected $fillable = ['cobro_id','monto_pagado','empresa_id','cobrable_id','cobrable_type','transaccion'];

    protected $guarded = ['id'];
    protected $table = 'cob_cobro_facturas';
    protected $casts = ['monto_pagado'=>'float'];
    protected $cobrar = ['factura'=>'Flexio\Modulo\FacturasVentas\Models\FacturaVenta'];

    public function __construct(array $attributes = array()){
	  $session = new FlexioSession;
      $this->setRawAttributes(array_merge($this->attributes, array('empresa_id' => $session->empresaId(),'transaccion'=>1)), true);
      parent::__construct($attributes);
    }

    function facturas(){
      return $this->belongsTo('Flexio\Modulo\FacturasVentas\Models\FacturaVenta','cobrable_id');
    }

    function cobrable(){
        return morphTo();
    }

    function setCobrableTypeAttribute($value){
        return $this->attributes['cobrable_type'] = $this->cobrar[$value];
    }

    function getCobrableTypeAttribute($value){
        $tipos = array_flip($this->cobrar);
        if(array_key_exists($value,$tipos)){
            return $tipos[$value];
        }
    }

    function cobros(){
      return $this->belongsTo(Cobro::class,'cobro_id');
    }

    public static function register($attributes)
    {
        return static::create($attributes);
    }
}

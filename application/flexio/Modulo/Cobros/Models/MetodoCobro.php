<?php
namespace Flexio\Modulo\Cobros\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class MetodoCobro extends Model
{

    protected $table = 'cob_cobro_metodo_pago';

    protected $fillable = ['cobro_id','tipo_pago','total_pagado','referencia'];

    protected $guarded = ['id'];

    protected $casts =['referencia' => 'array','total_pagado'=>'float'];

    public function cobro(){
      return $this->belongsTo(Cobro::class, 'cobro_id');
    }
    public function catalogo_metodo_pago(){
        return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo','tipo_pago','etiqueta')
               ->where('tipo','metodo_cobro')
               ->where('modulo','cobro');
    }

    public static function register($attributes)
    {
        return static::create($attributes);
    }



}

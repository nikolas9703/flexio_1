<?php
namespace Flexio\Modulo\Pagos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PagosMetodos extends Model
{

    protected $table = 'pag_pagos_metodo_pago';
    protected $fillable = ['pago_id','tipo_pago','total_pagado','referencia'];
    protected $guarded = ['id'];
    protected $casts = ['referencia' => 'array'];

    //relationships
    public function pago()
    {
        return $this->belongsTo('Flexio\Modulo\Pagos\Models\Pagos', 'pago_id');
    }

    public function catalogo_metodo_pago()
    {
        return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo','tipo_pago','etiqueta')
        ->where('flexio_catalogos.modulo','pagos');
    }

    public function setTotalPagadoAttribute($value)
    {
      $this->attributes['total_pagado'] = str_replace(",", "", $value);
    }



}

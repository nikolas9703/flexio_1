<?php

namespace Flexio\Modulo\CreditosAplicados\Models;

use Illuminate\Database\Eloquent\Model as Model;

class CreditoAplicado extends Model
{
    protected $table = 'cre_creditos_aplicados';
    protected $fillable = ['acreditable_type', 'acreditable_id', 'empresa_id', 'total', 'aplicable_type', 'aplicable_id'];
    protected $guarded = ['id'];
    protected $casts = [
        'total' => 'real',
    ];
    public $timestamps = true;

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = str_replace(",", "", $value);
    }

    public function acreditable()
    {
        return $this->morphTo();
    }

    public function empresa()
    {
       return $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa','empresa_id');
    }
}

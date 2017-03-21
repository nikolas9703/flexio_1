<?php

namespace Flexio\Modulo\MovimientosMonetarios\Models;

use Illuminate\Database\Eloquent\Model as Model;

class ItemsRetiros extends Model
{
    protected $table = 'mov_retiros_items';
    protected $fillable = ['nombre', 'cuenta_id', 'centro_id', 'updated_at', 'created_at', 'debito', 'id_retiro'];
    protected $guarded = ['id'];

    public function cuentas()
    {
        return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'cuenta_id');
    }

    public function centros()
    {
        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'centro_id');
    }

    public function setDebitoAttribute($value)
    {
        $this->attributes['debito'] = str_replace(',', '', $value);
    }
}

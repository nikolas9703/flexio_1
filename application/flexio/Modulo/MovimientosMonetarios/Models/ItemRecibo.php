<?php

namespace Flexio\Modulo\MovimientosMonetarios\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRecibo extends Model
{
    protected $table = 'mov_recibos_items';
    protected $fillable = ['nombre', 'cuenta_id', 'centro_id', 'updated_at', 'created_at', 'credito', 'id_recibo'];
    protected $guarded = ['id'];

    public function cuentas()
    {
        return $this->belongsTo('Flexio\Modulo\Contabilidad\Models\Cuentas', 'cuenta_id');
    }

    public function centros()
    {
        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'centro_id');
    }

    public function setCreditoAttribute($value)
    {
        $this->attributes['credito'] = str_replace(',', '', $value);
    }

}

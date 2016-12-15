<?php
namespace Flexio\Modulo\FacturasCompras\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class FacturaCompraCatalogo extends Model
{
    protected $table = 'fac_factura_catalogo';
    protected $guarded = ['id'];


    public function scopeEstadosFacturaVenta($query)
    {
        return $query->where("tipo", "etapa");
    }

    public function scopeTerminoFacturaVenta($query)
    {
        return $query->where("tipo", "termino_pago");
    }

}

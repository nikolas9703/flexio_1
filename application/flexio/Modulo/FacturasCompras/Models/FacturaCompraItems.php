<?php namespace Flexio\Modulo\FacturasCompras\Models;

use Illuminate\Database\Eloquent\Model as Model;

class FacturaCompraItems extends Model
{
    protected $table = 'faccom_facturas_items';
    protected $guarded = ['id'];
    protected $appends = ['total'];

    public function getTotalAttribute(){

        return $this->subtotal - $this->descuentos + $this->impuestos;

    }

    public function item()
    {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', 'item_id');
    }

    public function unidad()
    {
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Unidades', 'unidad_id');
    }

    public function factura()
    {
        return $this->belongsTo('Flexio\Modulo\FacturasCompras\Models\FacturaCompra', 'factura_id');
    }

}

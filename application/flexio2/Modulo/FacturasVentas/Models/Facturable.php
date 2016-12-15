<?php
namespace Flexio\Modulo\FacturasVentas\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
Use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
Use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;

class Facturable extends Model
{
  protected $table = 'fac_facturables';
  protected $guarded = ['id'];
  
  //facturas de ventas//contratos//compras
  function facturable(){
      return $this->morphTo();
  }

  function facturas(){
      return $this->belongsTo(FacturaVenta::class,'factura_id');
  }
}

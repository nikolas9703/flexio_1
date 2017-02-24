<?php
namespace Flexio\Modulo\FacturasSeguros\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
Use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
Use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro as FacturaSeguro;

class Facturable extends Model
{
  protected $table = 'fac_facturables';
  protected $guarded = ['id'];
  
  //facturas de ventas//contratos//compras
  function facturable(){
      return $this->morphTo();
  }

  function facturas(){
      return $this->belongsTo(FacturaSeguro::class,'factura_id');
  }
}

<?php
namespace Flexio\Modulo\CentroFacturable\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Cotizaciones\Models\Cotizacion;
use Flexio\Modulo\OrdenesVentas\Models\OrdenVenta;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;

class CentroFacturable extends Model{

  protected $table = 'cli_centros_facturacion';
  protected $fillable = ['nombre', 'empresa_id','direccion'];
  protected $guarded = ['id','cliente_id'];


  function cliente(){
    return $this->belongsTo(Cliente::class,'cliente_id');
  }

  function cotizacion(){
    return $this->morphedByMany(Cotizacion::class,'serviceable','centros_facturables','centro_facturacion_id')->withTimestamps();
  }

  function orden_ventas(){
    return $this->morphedByMany(OrdenVenta::class,'serviceable','centros_facturables','centro_facturacion_id')->withTimestamps();
  }

  function orden_alquiler(){
    return $this->morphedByMany(OrdenVentaAlquiler::class,'serviceable','centros_facturables','centro_facturacion_id')->withTimestamps();
  }

  function factura(){
    return $this->morphedByMany(FacturaVenta::class,'serviceable','centros_facturables','centro_facturacion_id')->withTimestamps();
  }

  function tiene_relaciones(){
    return ($this->factura->count() + $this->orden_ventas->count() + $this->cotizacion->count()) > 0;
  }
}

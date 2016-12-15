<?php
namespace Flexio\Modulo\OrdenesVentas\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class OrdenVentaCatalogo extends Model
{
  protected $table = 'ord_orden_venta_catalogo';
	protected $guarded = ['id'];
}

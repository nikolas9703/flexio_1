<?php
namespace Flexio\Modulo\Cotizaciones\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class CotizacionCatalogo extends Model
{
  protected $table = 'cotz_cotizaciones_catalogo';
	protected $guarded = ['id'];
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Cotizacion_catalogo_orm extends Model
{
  protected $table = 'cotz_cotizaciones_catalogo';
	protected $guarded = ['id'];
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Ordenes_catalogo_orm extends Model
{
  protected $table = 'ord_orden_venta_catalogo';
	protected $guarded = ['id'];
}

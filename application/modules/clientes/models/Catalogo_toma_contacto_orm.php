<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Catalogo_toma_contacto_orm extends Model
{
  protected $table = 'cli_clientes_catalogo_toma_contacto';
	protected $guarded = ['id','uuid_cliente'];
}

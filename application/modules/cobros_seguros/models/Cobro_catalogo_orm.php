<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Cobro_catalogo_orm extends Model
{
  protected $table = 'cob_cobro_catalogo';
	protected $guarded = ['id'];
}

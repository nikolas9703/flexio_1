<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Catalogo_tipo_intereses_orm extends Model
{
  protected $table = 'seg_ramos_tipo_interes';
	protected $guarded = ['id'];
}

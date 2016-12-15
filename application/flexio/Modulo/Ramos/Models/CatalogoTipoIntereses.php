<?php
namespace Flexio\Modulo\Ramos\Models;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class CatalogoTipoIntereses extends Model
{
  protected $table = 'seg_ramos_tipo_interes';
	protected $guarded = ['id'];
}
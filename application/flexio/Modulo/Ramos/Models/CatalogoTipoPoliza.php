<?php
namespace Flexio\Modulo\Ramos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class CatalogoTipoPoliza extends Model
{
	protected $table = 'seg_ramos_tipo_poliza';
	protected $guarded = ['id'];
}

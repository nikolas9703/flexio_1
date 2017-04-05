<?php 

namespace Flexio\Modulo\Core\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Descarga extends Model{

	protected $table    = 'flexio_descargas';
	protected $guarded  = 'id';
	public $timestamps  = true;
	protected $fillable = [
	'modulo',
	'descargaType',
	'usuario',
	'estado',
	'empresa_id'

	];
}

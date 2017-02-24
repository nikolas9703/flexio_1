<?php
namespace Flexio\Modulo\ConfiguracionRrhh\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;


class RrhhAreas extends Model
{
  protected $table = 'dep_departamentos';
	protected $fillable = ['empresa_id', 'nombre', 'creado_por'];
	protected $guarded = ['id'];
 
}

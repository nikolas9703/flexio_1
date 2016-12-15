<?php
namespace Flexio\Modulo\Cargos\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Cargos extends Model
{
    protected $table        = 'carg_cargos';
    protected $fillable     = ['empresa_id','departamento_id','codigo', 'nombre', 'descripcion', 'tipo_rata', 'rata', 'estado_id', 'creado_por'];
    protected $guarded      = ['id'];
    public $timestamps      = false;
}

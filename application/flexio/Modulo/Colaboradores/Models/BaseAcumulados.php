<?php
namespace Flexio\Modulo\Colaboradores\Models;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class BaseAcumulados extends Model
{
    protected $table = 'pln_base_acumulados';
    protected $fillable = ['acumulado_id', 'colaborador_id', 'indentificacion', 'tipo_acumulado', 'acumulado_original', 'acumulado_usado', 'acumulado_planilla'];
    protected $guarded = ['id'];
    public $timestamps      = true;
}
 

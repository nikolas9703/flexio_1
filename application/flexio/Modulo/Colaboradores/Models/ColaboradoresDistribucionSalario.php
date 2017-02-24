<?php
namespace Flexio\Modulo\Colaboradores\Models;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class ColaboradoresDistribucionSalario extends Model {

    protected $table = 'col_distribucion_salario';
    protected $fillable = ['colaborador_id', 'cuenta_costo_id', 'centro_contable_id', 'prcentaje_distribucion', 'monto_asignado', 'created_at', 'updated_at'];
    protected $guarded = ['id'];

}

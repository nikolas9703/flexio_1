<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Beneficiarios_orm extends Model
{
    protected $table = 'col_colaboradores_beneficiarios';
    protected $fillable = ['colaborador_id', 'tipo', 'nombre', 'parentesco_id', 'cedula', 'provincia_id', 'letra_id', 'tomo', 'asiento', 'no_pasaporte', 'porcentaje ', 'creado_por'];
    protected $guarded = ['id'];

}
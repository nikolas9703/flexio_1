<?php
namespace Flexio\Modulo\Colaboradores\Models;

use Illuminate\Database\Eloquent\Model as Model;




class ColaboradoresContratos extends Model
{
  protected $table = 'col_colaboradores_contrato';
 	protected $fillable = ['colaborador_id', 'fecha_ingreso', 'fecha_salida', 'empresa_id', 'estado', 'numero_contrato', 'centro_contable', 'fecha_creacion'];
 	protected $guarded = ['id'];

    

}

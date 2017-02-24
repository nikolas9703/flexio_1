<?php
namespace Flexio\Modulo\Colaboradores\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;

class ColaboradoresContratos extends Model
{
  protected $table = 'col_colaboradores_contrato';
 	protected $fillable = ['colaborador_id', 'fecha_ingreso', 'fecha_salida', 'empresa_id', 'estado', 'numero_contrato', 'centro_contable', 'fecha_creacion','asistencia','prima_antiguedad','decimo_tercermes','vacacion_acumulado'];
 	protected $guarded = ['id'];

  function salarios_devengados_contrato(){
    return $this->hasMany(PagadasColaborador::Class, 'contrato_id');
  }

  function salarios_devengados_contrato_vacaciones(){
    return $this->hasMany(PagadasColaborador::Class, 'contrato_id')->where("vacacion_acumulado",'si');
  }
  function salarios_devengados_contrato_decimo(){
    return $this->hasMany(PagadasColaborador::Class, 'contrato_id')->where("decimo_tercermes",'si');
  }
  function salarios_devengados_contrato_prima(){
    return $this->hasMany(PagadasColaborador::Class, 'contrato_id')->where("prima_antiguedad",'si');
  }
  function salarios_devengados_contrato_asistencia(){
    return $this->hasMany(PagadasColaborador::Class, 'contrato_id')->where("asistencia",'si');
  }
/*
  public function scopeDeVacacionFecha($query, $fecha_desde, $fecha_hasta)
  {
      return $this->salarios_devengados_contrato_vacaciones()->where->("fecha_cierre_planilla",'>=', $fecha_desde)->where->("fecha_cierre_planilla",'<=', $fecha_hasta);
   }*/

 }

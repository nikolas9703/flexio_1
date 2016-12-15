<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class AcumuladoConstructor extends Model{

	protected $table = 'pln_config_acumulados_constructores';
	protected $fillable = [
      	'operador_id',
      	'operador_valor',
      	'tipo_calculo_uno',
      	'valor_calculo_uno',
      	'tipo_calculo_dos',
      	'valor_calculo_dos',
        'acumulado_id'
	 ];

	protected $guarded = ['id'];
	public $timestamps = false;



}

<?php
namespace Flexio\Modulo\Comisiones\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Acumulados;


class ComisionColaboradorAcumulado extends Model
{
	protected $table = 'com_colaborador_acumulado';
	protected $fillable = [ 'com_colaborador_id','com_acumulado_id','monto'];
	protected $guarded = ['id'];
	public $timestamps = false;


  public function acumulado_dependiente()
  {
 			return $this->hasMany(ComisionAcumulado::Class, 'id','com_acumulado_id');
   }
}
 

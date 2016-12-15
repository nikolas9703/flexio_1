<?php
namespace Flexio\Modulo\Comisiones\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Deducciones;


class ComisionColaboradorDeduccion extends Model
{
	protected $table = 'com_colaborador_deduccion';
	protected $fillable = [ 'com_colaborador_id','com_deduccion_id','monto'];
	protected $guarded = ['id'];
	public $timestamps = false;


	public function deduccion_dependiente()
  {
 			return $this->hasMany(ComisionDeduccion::Class, 'id','com_deduccion_id');
   }
}

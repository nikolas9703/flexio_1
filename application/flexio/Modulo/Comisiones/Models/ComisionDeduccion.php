<?php
namespace Flexio\Modulo\Comisiones\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Deducciones;


class ComisionDeduccion extends Model
{
	protected $table = 'com_deducciones';
	protected $fillable = [ 'deduccion_id','comision_id'];
	protected $guarded = ['id'];
	public $timestamps = false;

   public function deduccion_info()
  {
      return $this->belongsTo(Deducciones::Class, 'deduccion_id', 'id');
   }
}

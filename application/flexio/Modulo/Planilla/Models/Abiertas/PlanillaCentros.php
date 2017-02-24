<?php
namespace Flexio\Modulo\Planilla\Models\Abiertas;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;


class PlanillaCentros extends Model
{
	protected $table = 'pln_planilla_centros';
	protected $fillable = [ 'centro_contable_id','planilla_id'];
	public $timestamps = false;


  public function centro_info()
  {
      return $this->belongsTo(CentrosContables::Class, 'centro_contable_id', 'id');
   }
}

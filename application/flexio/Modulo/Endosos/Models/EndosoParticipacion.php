<?php 

namespace Flexio\Modulo\Endosos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;



class EndosoParticipacion extends Model
{
  protected $table = 'end_endosos_participacion';
  protected $fillable = ['id','id_endoso','agente','agente_id','porcentaje_participacion',]; 
  protected $guarded = ['id'];
  public $timestamps = false;


  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'updated_at' => date('Y-m-d H:s:i'),
      'created_at' => date('Y-m-d H:s:i')
    )), true);
    parent::__construct($attributes);
  }

}

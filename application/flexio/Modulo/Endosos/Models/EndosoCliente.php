<?php 

namespace Flexio\Modulo\Endosos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class EndosoCliente extends Model
{
  protected $table = 'end_endosos_cliente';
  protected $fillable = ['id','id_endoso','nombre_cliente','identificacion','n_identificacion','grupo','telefono','correo_electronico','direccion','exonerado_impuesto']; 
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

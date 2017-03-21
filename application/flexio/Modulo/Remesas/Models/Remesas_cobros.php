<?php 

namespace Flexio\Modulo\Remesas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Remesas\Models\Remesa;

class Remesas_cobros extends Model
{
  protected $table = 'seg_remesas_cobros';
  protected $fillable = ['uuid_remesas_cobros','id_remesa','id_cobro'];
  protected $guarded = ['id'];
  public $timestamps = false;


  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_remesas_cobros' => Capsule::raw("ORDER_UUID(uuid())"),
      'created_at' => date('Y-m-d h:i:s'),
      'updated_at' => date('Y-m-d h:i:s'),
      )), true);
    parent::__construct($attributes);
  }


  public static function findByUuid($uuid){
    return self::where('uuid_remesas_cobros',hex2bin($uuid))->first();
  }

  public function datosEmpresa(){
    return $this->hasOne(Empresa::class, 'id', 'empresa_id');
  }

  public function remesas(){
    return $this->hasmany(Remesa::class, 'id','id_remesa');
  }

}

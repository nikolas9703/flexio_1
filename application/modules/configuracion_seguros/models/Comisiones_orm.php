<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Comisiones_orm extends Model
{
    protected $table = 'seg_planes_comisiones';
    protected $fillable = ['uuid_comisiones','inicio','fin','comision','sobre_comision','id_planes', 'updated_at', 'created_at'];
    protected $guarded = ['id'];
    public $timestamps = false;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
          'uuid_comisiones' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public static function findByUuid($uuid){
        return self::where('uuid_comisiones',hex2bin($uuid))->first();
    }
    //transformaciones para GET
    public function getUuidComisionesAttribute($value) {
        return strtoupper(bin2hex($value));
    }    
}
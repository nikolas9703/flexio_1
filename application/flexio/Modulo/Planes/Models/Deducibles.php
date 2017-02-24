<?php
namespace Flexio\Modulo\Planes\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Deducibles extends Model
{
    protected $table = 'seg_deducibles';
    protected $fillable = ['uuid_deducibles','nombre','id_planes', 'updated_at', 'created_at', 'deducible_monetario'];
    protected $guarded = ['id'];
    public $timestamps = false;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
          'uuid_deducibles' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public static function findByUuid($uuid){
        return self::where('uuid_deducibles',hex2bin($uuid))->first();
    }
    //transformaciones para GET
    public function getUuidDeduciblesAttribute($value) {
        return strtoupper(bin2hex($value));
    }   
    public static function findByIdP($id_planes){
        return self::where('id_planes',$id_planes)->get();
    }
}
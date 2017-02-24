<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Coberturas_orm extends Model
{
    protected $table = 'seg_coberturas';
    protected $fillable = ['uuid_coberturas','nombre','id_planes', 'updated_at', 'created_at', 'cobertura_monetario'];
    protected $guarded = ['id'];
    public $timestamps = false;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
          'uuid_coberturas' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public static function findByUuid($uuid){
        return self::where('uuid_coberturas',hex2bin($uuid))->first();
    }
    //transformaciones para GET
    public function getUuidCoberturasAttribute($value) {
        return strtoupper(bin2hex($value));
    }   
    public static function findByIdP($id_planes){
        return self::where('id_planes',$id_planes)->get();
    }
}
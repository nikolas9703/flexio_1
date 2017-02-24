<?php 
namespace Flexio\Modulo\Coberturas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Coberturas extends Model
{
    protected $table = 'seg_coberturas';

    protected $fillable = [
        'uuid_coberturas',
        'nombre',
        'id_planes', 
        'updated_at', 
        'created_at',
        'cobertura_monetario'
    ];

    protected $guarded = ['id'];
    //public $timestamps = false;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
          'uuid_coberturas' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public function findByUuid($uuid){
        return self::where('uuid_coberturas',hex2bin($uuid))->first();
    }
    //transformaciones para GET
    public function getUuidCoberturasAttribute($value) {
        return strtoupper(bin2hex($value));
    }   
}
<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Precios extends Model
{
    protected $table        = 'inv_precios';
    protected $fillable     = ['uuid_precio', 'nombre', 'descripcion', 'estado', 'empresa_id', 'created_by','tipo_precio'];
    protected $guarded      = ['id'];

    //GETS
    public function getUuidPrecioAttribute($value){

        return strtoupper(bin2hex($value));

    }


}

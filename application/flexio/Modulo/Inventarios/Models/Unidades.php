<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Unidades extends Model
{
    protected $table        = 'inv_unidades';
    protected $fillable     = ['uuid_unidad', 'nombre', 'descripcion', 'estado', 'empresa_id', 'created_by'];
    protected $guarded      = ['id'];
    public $timestamps      = false;
    
    //GETS
    public function getUuidUnidadAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
    
}

<?php 
namespace Flexio\Modulo\Proveedores\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class ProveedoresCategorias extends Model
{
    
    protected $table = 'pro_categorias';
    public $timestamps = false;
    
    
    //gets
    public function getUuidCategoriaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
}
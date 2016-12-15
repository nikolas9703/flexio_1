<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class ItemsCat extends Model
{
    protected $table        = 'inv_inventarios_cat';
    protected $fillable     = ['etiqueta'];
    protected $guarded      = ['id_cat'];
    public $timestamps      = false;
    
    //SCOPES
    public function scopeDeValor($query, $valor)
    {
        return $query->where("valor", $valor);
    }
}

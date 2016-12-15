<?php
namespace Flexio\Modulo\Ajustes\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class AjustesCat extends Model
{
    protected $table        = 'aju_ajustes_cat';
    protected $fillable     = ['etiqueta'];
    protected $guarded      = ['id_cat'];
    public $timestamps      = false;
    
    //SCOPES
    public function scopeDeValor($query, $valor)
    {
        return $query->where("valor", $valor);
    }
}

<?php
namespace Flexio\Modulo\Talleres\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class EquipoTrabajoCatalogo extends Model {
	
    protected  $table = 'tal_cat';
    protected $fillable = ['identificador', 'etiqueta', 'orden'];
    protected  $guarded = ['id', 'uuid_equipo'];
	
    public function scopeEstados($query) {
    	return $query->where("identificador", "Estado");
    }
}
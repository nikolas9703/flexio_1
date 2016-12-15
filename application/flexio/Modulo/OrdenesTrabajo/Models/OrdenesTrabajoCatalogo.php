<?php
namespace Flexio\Modulo\OrdenesTrabajo\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class OrdenesTrabajoCatalogo extends Model
{
    protected $table    = 'odt_cat';
    protected $fillable = ['identificador', 'etiqueta', 'orden'];
    protected $guarded	= ['id'];
    protected static $ci;
    
    public function scopeEstados($query) {
    	return $query->where("identificador", "Estado");
    }
    
    public function scopeTipoOrden($query) {
    	return $query->where("identificador", "Tipo de orden");
    }
    
    public function scopeListaPrecio($query) {
    	return $query->where("identificador", "Lista de precio");
    }
    
    public function scopeFacturable($query) {
    	return $query->where("identificador", "Facturable");
    }
    
    public function scopeOrdenDesde($query) {
    	return $query->where("identificador", "Orden desde");
    }
}

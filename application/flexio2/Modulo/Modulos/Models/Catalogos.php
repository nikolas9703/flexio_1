<?php
namespace Flexio\Modulo\Modulos\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Catalogos extends Model
{
    protected $table        = 'mod_catalogos';
    protected $fillable     = ['identificador', 'valor','etiqueta','orden','activo'];
    protected $guarded      = ['id_cat'];
    protected $primaryKey   = "id_cat";
    public $timestamps      = false;

    public function scopeFormasDePago($query) {
        return $query->where("identificador", "Forma de Pago");
    }

    public function scopeTerminosDePago($query) {
        return $query->where("identificador", "terminos_pago");
    }

    public function scopeTiposDeCuenta($query) {
        return $query->where("identificador", "Tipo de Cuenta");
    }

    public function scopeActivo($query) {
        return $query->where("activo", "1");
    }
    public function scopeMeses($query) {
      return $query->where("identificador", "meses");
    }
    public function scopeEstados($query) {
      return $query->where("identificador", "estado");
    }
}

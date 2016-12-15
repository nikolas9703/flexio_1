<?php
namespace Flexio\Modulo\Devoluciones\Models;
use Illuminate\Database\Eloquent\Model as Model;


class DevolucionCatalogo extends Model
{
    protected $table = 'dev_devolucion_catalogo';
    protected $guarded = ['id'];


    public function scopeEstados($query)
    {
        return $query->where("tipo", "etapa");
    }

    public function scopeRazon($query)
    {
        return $query->where("tipo", "razon");
    }


}

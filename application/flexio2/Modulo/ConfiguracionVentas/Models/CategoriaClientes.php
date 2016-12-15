<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 6/10/16
 * Time: 12:57 PM
 */

namespace Flexio\Modulo\ConfiguracionVentas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class CategoriaClientes extends Model
{

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cli_clientes_categoria';

    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['nombre','descripcion','estado'];
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id', 'uuid_categoria'];

    /**
     * Obtiene uuid_centro
     *
     * Se convierte la data binaria en una representacion
     * hexadecimal
     *
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getUuidCategoriaAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    public function scopeDeEmpresa($query, $empresa_id) {
        return  $query->where("id_empresa", $empresa_id);
    }
    function scopeEstadoActivo($query){
        return $query->whereHas('estado', function ($query) {
            $query->where('etiqueta', '=', 'activo')->where('valor', 'LIKE', '%Activo%');
        });
    }
    public function estadoReferencia() {
        return $this->belongsTo('Flexio\Modulo\ConfiguracionVentas\Models\CatalogosClientes', 'estado', 'etiqueta');
    }

    public function estado(){
        return $this->hasOne('Flexio\Modulo\ConfiguracionVentas\Models\CatalogosClientes', 'etiqueta', 'estado');
    }
}
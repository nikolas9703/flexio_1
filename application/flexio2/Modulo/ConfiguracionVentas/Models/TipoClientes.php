<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 6/10/16
 * Time: 12:54 PM
 */

namespace Flexio\Modulo\ConfiguracionVentas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class TipoClientes extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cli_clientes_tipo';
    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    //protected $dateFormat = 'U';


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
    protected $guarded = ['id', 'uuid_tipo'];
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
    public function getUuidTipoAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    public function scopeDeEmpresa($query, $empresa_id) {
        return  $query->where("id_empresa", $empresa_id);
    }

    public function estadoReferencia() {
        return $this->belongsTo('Flexio\Modulo\ConfiguracionVentas\Models\CatalogosClientes', 'estado', 'etiqueta');
    }

    public function estado(){
        return $this->hasOne('Flexio\Modulo\ConfiguracionVentas\Models\CatalogosClientes', 'etiqueta', 'estado');
    }
}
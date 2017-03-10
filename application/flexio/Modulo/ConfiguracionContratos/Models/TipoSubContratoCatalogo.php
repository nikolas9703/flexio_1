<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 21/2/17
 * Time: 2:47 PM
 */

namespace Flexio\Modulo\ConfiguracionContratos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class TipoSubContratoCatalogo extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'sub_subcontratos_catalogos';
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['nombre','acceso','estado','empresa_id','created_by'];
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id', 'uuid_tipo'];
   // public $timestamps = false;

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_tipo' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public function getUuidTipoAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    public function scopeDeEmpresa($query, $empresa_id) {
        return  $query->where("empresa_id", $empresa_id);
    }
}
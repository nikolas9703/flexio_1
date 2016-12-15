<?php
namespace Flexio\Modulo\Bodegas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;

class Bodegas2 extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'bod_bodegas';


    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    protected $dateFormat = 'U';


    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['uuid_bodega, codigo, nombre, contacto_principal, direccion, telefono, entrada_id, estado, empresa_id'];
    protected $appends = ['icono', 'enlace'];

    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];
    private static $bodegas = array();

    public function getUuidBodegaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }


    public function getIconoAttribute(){
        return 'fa fa-cubes';
    }

    public function getEnlaceAttribute()
    {
        return base_url("bodegas/ver/".$this->uuid_bodega);
    }

    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }
}
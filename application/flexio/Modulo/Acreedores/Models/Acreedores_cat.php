<?php 
namespace Flexio\Modulo\Acreedores\Models;

use Illuminate\Database\Eloquent\Model as Model;

class Acreedores_cat extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     * Un acreedor es un proveedor dentro de flexio por lo cual
     * utilizamos la misma tabla solo con una etiqueta para
     * diferenciarlos
     *
     * @var string
     */
    protected $table = 'pro_proveedores_cat';
    
    
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
    protected $fillable = ['id_campo','valor','etiqueta'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id_cat'];
    
    
    public function scopeTipos($query)
    {
        return $query->where("valor", "tipo_acreedor");
    }
}
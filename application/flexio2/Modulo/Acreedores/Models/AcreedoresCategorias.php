<?php 
namespace Flexio\Modulo\Acreedores\Models;

use Illuminate\Database\Eloquent\Model as Model;

class AcreedoresCategorias extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     * Un acreedor es un proveedor dentro de flexio por lo cual
     * utilizamos la misma tabla solo con una etiqueta para
     * diferenciarlos
     *
     * @var string
     */
    protected $table = 'pro_categorias';
    
    
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
    protected $fillable = ['uuid_categoria','nombre','descripcion', 'estado', 'fecha_creacion', 'creado_por', 'id_empresa'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];
    
    
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("id_empresa", $empresa_id);
    }
}
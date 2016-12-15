<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Illuminate\Database\Eloquent\Model as Model;

class InteresesAsegurados_cat extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string 
     */
    protected $table = 'int_intereses_asegurados_cat';
    
    
    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;
       
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
    
    
    public function scopeTipos($query) {
        return $query->where("valor", "tipo_interes_asegurado");
    }
}
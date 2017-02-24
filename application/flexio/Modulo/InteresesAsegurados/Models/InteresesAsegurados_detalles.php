<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Illuminate\Database\Eloquent\Model as Model;

class InteresesAsegurados_detalles extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string 
     */
    protected $table = 'int_intereses_asegurados_detalles';
    
    
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
    protected $fillable = ['id_intereses', 'id_solicitudes','detalle_relacion','detalle_prima', 'detalle_beneficio', 'detalle_monto', 'detalle_int_asociado', 'detalle_unico', 'detalle_suma_asegurada', 'detalle_deducible', 'detalle_certificado',  'fecha_inclusion', 'fecha_exclusion','detalle_participacion','tipo_relacion'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];
    
    
}
<?php
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Illuminate\Database\Eloquent\Model as Model;

class InteresesAseguradosDeducibleDetalle extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string 
     */
    protected $table = 'int_intereses_asegurados_deduccion_detalles';
    
    
    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = true;
       
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['detalle_unico','','nombre ','deducible_monetario','id_interes','id_solicitud'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];
    
}
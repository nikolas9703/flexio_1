<?php 
namespace Flexio\Modulo\Colaboradores\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Familia extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     * Un acreedor es un proveedor dentro de flexio por lo cual
     * utilizamos la misma tabla solo con una etiqueta para
     * diferenciarlos
     *
     * @var string
     */
    protected $table = 'col_familia';
    
    
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
    protected $fillable = ['colaborador_id','nombre', 'parentesco_id','cedula', 'provincia_id','letra_id', 'tomo', 'asiento', 'no_pasaporte', 'fecha_nacimiento'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];
    
    
    
}
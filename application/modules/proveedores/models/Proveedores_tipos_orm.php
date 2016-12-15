<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Proveedores_tipos_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'pro_tipos';
    
    
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
     * Instancia de CodeIgniter
     */
    protected $Ci;
    
    
    public function __construct() {
        $this->Ci = & get_instance();
    }
    
    public function getUuidTipoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
    
    
    
    
	
}
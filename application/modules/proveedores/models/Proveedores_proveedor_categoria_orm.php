<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Proveedores_proveedor_categoria_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'pro_proveedor_categoria';

    protected $fillable = ['id_proveedor', 'id_categoria'];
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
    
    
    public function countRegistro($id_proveedor= NULL, $id_categoria=NULL){
        return  Proveedores_proveedor_categoria_orm
            ::where("id_proveedor", "=", $id_proveedor)
            ->where("id_categoria", "=",$id_categoria)
            ->count();
    }
    
    
    
    
    
	
}
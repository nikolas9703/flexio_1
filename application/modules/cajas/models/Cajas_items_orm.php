<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Cajas_items_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ca_cajas';
    
    
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
    
    
//    public function __construct() {
//        $this->Ci = & get_instance();
//        
//        $this->Ci->load->model("Cajas/items_orm");
//    }
    
    
    /**
     * Obtiene la lista de items asociadas al pedido
     */
//    public function item()
//    {
//        return $this->belongsTo('items_orm', 'id_item', 'id');
//    }
    
 
    
    public function cajas()
    {
        $this->Ci->load->model("cajas/Cajas_orm");
        return $this->belongsTo('Cajas_orm', 'id_caja', 'id');
    }
    
//    public function unidadReferencia()
//    {
//        return $this->belongsTo('Unidades_orm', 'unidad', 'id');
//    }
    
}
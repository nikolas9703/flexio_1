<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Pedidos_items_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ped_pedidos_inv_items';
    
    
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
        
        $this->Ci->load->model("inventarios/Items_orm");
    }
    
    
    /**
     * Obtiene la lista de items asociadas al pedido
     */
    public function item()
    {
        return $this->belongsTo('Items_orm', 'id_item', 'id');
    }
    
    public function cuentaDeGasto()
    {
        return $this->belongsTo('Cuentas_orm', 'cuenta', 'id');
    }
    
    public function pedido()
    {
        $this->Ci->load->model("pedidos/Pedidos_orm");
        return $this->belongsTo('Pedidos_orm', 'id_pedido', 'id');
    }
    
    public function unidadReferencia()
    {
        return $this->belongsTo('Unidades_orm', 'unidad', 'id');
    }
    
    public function cantidadPorFactorConversion()
    {
        //obteniendo el factor de conversion
        $factor_conversion = 1;
        
        foreach($this->item->item_unidades as $item_unidad)
        {
            if($item_unidad->id_unidad == $this->unidad)
            {
                $factor_conversion = $item_unidad->factor_conversion;
            }
        }
        
        
        return $this->cantidad * $factor_conversion;
    }
	
}
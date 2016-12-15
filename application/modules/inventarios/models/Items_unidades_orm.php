<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Items_unidades_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inv_item_inv_unidad';
    
    
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
    protected $fillable = ['*'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * Instancia de CodeIgniter
     */
    protected $Ci;
    
    
    public function __construct() {
        $this->Ci = & get_instance();
    }
    
    
    public function unidad()
    {
        $this->Ci->load->model("inventarios/Unidades_orm");
        return $this->belongsTo('Unidades_orm', 'id_unidad', 'id');
    }
    
    public function item()
    {
        return $this->belongsTo('Items_orm', 'id_item', 'id');
    }
    
    public function findByItemAndUnidad($item_id, $unidad_id){
        return $this->where("id", "5");
    }
    
    
    public function scopeDeItem($query, $item_id)
    {
        return $query->where("id_item", $item_id);
    }
    
    public function scopeDeUnidad($query, $unidad_id)
    {
        return $query->where("id_unidad", $unidad_id);
    }
	
}
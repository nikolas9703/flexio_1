<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ajustes_items_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'aju_ajustes_items';
    
    
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
    //protected $dateFormat = 'U';
    
    
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
    protected $codeIgniter;
    
    
    public function __construct() {
        $this->codeIgniter = & get_instance();
        
        //Cargando Modelos
        $this->codeIgniter->load->model("inventarios/Items_orm");
        $this->codeIgniter->load->model("inventarios/Unidades_orm");
    }
    
    
    public function ajuste()
    {
        return $this->belongsTo('Ajustes_orm', 'ajuste_id', 'id');
    }
    
    public function comp__aux()
    {
        return array(
            "uuid_item"         => $this->item->uuid_item,
            "descripcion"       => $this->item->descripcion,
            "observacion"       => "",
            "cantidad"          => $this->cantidad,
            "uuid_unidad"       => $this->unidad->uuid_unidad,
            "uuid_cuentaGasto"  => $this->item->cuentaGasto->uuid_cuenta
        );
    }
    
    public function item()
    {
        return $this->belongsTo('Items_orm', 'item_id', 'id');
    }
    
    public function unidad()
    {
        return $this->belongsTo('Unidades_orm', 'unidad_id', 'id');
    }
    
    public function unidadReferencia()
    {
        return $this->belongsTo('Unidades_orm', 'unidad_id', 'id');
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
    
    public function cantidadRecibidaPorFactorConversion()
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
        
        
        return $this->cantidad_ajustada * $factor_conversion;
    }
    
	
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Consumos_items_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cons_consumos_items';
    
    
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
    protected $fillable = ['consumo_id', 'categoria_id', 'item_id', 'cantidad', 'unidad_id', 'precio_unidad', 'uuid_impuesto', 'descuento', 'cuenta_id', 'observacion'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    
    public function consumo()
    {
        return $this->belongsTo('Consumos_orm', 'consumo', 'id');
    }
    
    public function item()
    {
        return $this->belongsTo('Items_orm', 'item_id', 'id');
    }
    
    public function unidad()
    {
        return $this->belongsTo('Unidades_orm', 'unidad_id', 'id');
    }
    
    public function categoria()
    {
        return $this->belongsTo('Categorias_orm', 'categoria_id', 'id');
    }
    
    public function cuentaGasto()
    {
        return $this->belongsTo('Cuentas_orm', 'cuenta_id', 'id');
    }
    
    
    public function comp__aux()
    {
        return array(
            "uuid_item"         => $this->item->uuid_item,
            "descripcion"       => $this->item->descripcion,
            "observacion"       => $this->observacion,
            "cantidad"          => $this->cantidad,
            "uuid_unidad"       => $this->unidad->uuid_unidad,
            "uuid_cuentaGasto"  => $this->item->cuentaGasto->uuid_cuenta
        );
    }
    
    public function cantidadPorFactorConversion()
    {
        //obteniendo el factor de conversion
        $factor_conversion = 1;
        
        foreach($this->item->item_unidades as $item_unidad)
        {
            if($item_unidad->id_unidad == $this->unidad_id)
            {
                $factor_conversion = $item_unidad->factor_conversion;
            }
        }
        
        
        return $this->cantidad * $factor_conversion;
    }
	
}
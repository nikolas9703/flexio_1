<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Traslados_items_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'lines_items';
    
    
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
    
    //Gets
    public function getIdItemAttribute()
    {
        return $this->item_id;
    }
    public function getIdTrasladottribute()
    {
        return $this->tipoable_id;
    }
    public function getCantidadRecibidaAttribute()
    {
        return $this->cantidad2;
    }
    public function getUnidadAttribute()
    {
        return $this->unidad_id;
    }
    
    //Sets
    public function setIdItemAttribute($value)
    {
        $this->attributes["item_id"] = $value;
    }
    public function setIdTrasladottribute($value)
    {
        $this->attributes["tipoable_id"] = $value;
    }
    public function setCantidadRecibidaAttribute($value)
    {
        $this->attributes["cantidad2"] = $value;
    }
    public function setUnidadAttribute($value)
    {
        $this->attributes["unidad_id"] = $value;
    }




    public function traslado()
    {
        return $this->belongsTo('Traslados_orm', 'tipoable_id', 'id')
            ->where("tipoable_type", "Flexio\Modulo\Traslados\Models\Traslados");
    }
    
    public function item()
    {
        return $this->belongsTo('Items_orm', 'item_id', 'id');
    }
    
    public function unidadReferencia()
    {
        return $this->belongsTo('Unidades_orm', 'unidad_id', 'id');
    }
    
    public function precioPorFactorConversion()
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
        
        
        return $this->precio_unidad * $factor_conversion;
    }
    
    public function comp__aux()
    {
        return array(
            "uuid_item"         => $this->item->uuid_item,
            "descripcion"       => $this->item->descripcion,
            "observacion"       => $this->observacion,
            "cantidad"          => $this->cantidad,
            "uuid_unidad"       => $this->unidadReferencia->uuid_unidad,
            "uuid_cuentaGasto"  => $this->item->cuentaGasto->uuid_cuenta
        );
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
        
        
        return $this->cantidad_recibida * $factor_conversion;
    }
	
}
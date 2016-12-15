<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Bodegas_cat_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'bod_bodegas_cat';
    
    
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
    protected $fillable = ['id_campo, valor, etiqueta'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id_cat'];
    
    
    //maneja la relacion uno a muchos entre bodegas y categoria
    //relacionadas por el campo entrada_id
    public function bodegasByEntrada()
    {
        return $this->hasMany('Bodegas_orm', 'entrada_id', 'id_cat');
    }
    
    public function scopeTipos($query)
    {
        return $query->where("valor", "tipo_bodega");
    }
    
    public function scopeEstadosItems($query)
    {
        return $query->where("valor", "estado_items_bodega");
    }
    
	
}
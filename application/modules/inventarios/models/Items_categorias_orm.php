<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Items_categorias_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inv_items_categorias';
    
    
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
    
    
    
    
    public function categoria()
    {
        return $this->belongsTo('Categorias_orm', 'id_categoria', 'id');
    }
    
    public function item()
    {
        return $this->belongsTo('Items_orm', 'id_item', 'id');
    }
    
    
    
	
}
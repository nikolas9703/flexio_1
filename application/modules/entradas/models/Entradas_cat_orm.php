<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Entradas_cat_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ent_entradas_cat';
    
    
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
    protected $fillable = ['etiqueta'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id_cat'];
    
    
    public function entradasByEstado()
    {
        return $this->hasMany('Entradas_orm', 'estado_id', 'id_cat');
    }
    
    public function scopeEstados($query)
    {
        return $query->where('id_campo', '4');
    }
    
    
    public function comp__etiquetaWithSpan()
    {
        $estado     = "No Aplica";
        $background = "red";
        
        if($this->id_cat == 1)//Por recibir
        {
            $estado     = $this->etiqueta;
            $background = "#EBAD50";
        }
        elseif($this->id_cat == 2)//Parcial
        {
            $estado     = $this->etiqueta;
            $background = "#1C84C6";
        }
        elseif($this->id_cat == 3)//Recibido
        {
            $estado     = $this->etiqueta;
            $background = "#66B85B";
        }
        
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$estado.'</span>';
    }
}
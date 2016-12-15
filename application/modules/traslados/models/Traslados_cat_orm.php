<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Traslados_cat_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'tras_traslados_cat';
    
    
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
    
    
    public function comp__etiquetaWithSpan()
    {
        $estado     = "No Aplica";
        $background = "red";
        
        if($this->id_cat == 1)//Por enviar
        {
            $estado     = $this->etiqueta;
            $background = "#EBAD50";
        }
        elseif($this->id_cat == 2)//En transito
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
	
    public function scopeEstados($query)
    {
        return $query->where('id_campo', '4');
    }
}
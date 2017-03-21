<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ajustes_cat_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'aju_ajustes_cat';
    
    
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
     * Obtiene uuid_centro
     * 
     * Se convierte la data binaria en una representacion
     * hexadecimal
     * 
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    
    
    public function comp__tipoWithSpan()
    {
        $tipo   = "No Aplica";
        $clase  = "fa-level-up";
        
        if($this->id_cat == 1)//Ajuste negativo
        {
            $tipo   = $this->etiqueta;
            $clase  = "fa-level-down";
        }
        elseif($this->id_cat == 2)//Ajuste positivo
        {
            $tipo   = $this->etiqueta;
            $clase  = "fa-level-up";
        }
        
        return $tipo.'<span class="fa '.$clase.'" style="margin-left:10px;color:#049DBF;font-size:16px;"></span>';
    }
    
    public function comp__estadoWithSpan()
    {
        $estado     = "No Aplica";
        $background = "red";
        
        if($this->id_cat == 3)//Por aprobar
        {
            $estado     = $this->etiqueta;
            $background = "#F0AD4E";
        }
        elseif($this->id_cat == 4)//Aprobado
        {
            $estado     = $this->etiqueta;
            $background = "#5CB85C";
        }
        elseif($this->id_cat == 5)//Rechazado
        {
            $estado     = $this->etiqueta;
            $background = "#D9534F";
        }
        
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$estado.'</span>';
    }
    
    public function scopeTipos($query)
    {
        return $query->where("id_campo", 4);
    }
    
    public function scopeEstados($query)
    {
        return $query->where("id_campo", 18);
    }
}
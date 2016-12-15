<?php
namespace Flexio\Modulo\Salidas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class SalidasCat extends Model
{
    
    protected $table = 'sal_salidas_cat';
    public $timestamps = false;
    protected $fillable = ['etiqueta'];
    protected $guarded = ['id_cat'];
    
    //SCOPES
    public function scopeEstados($query)
    {
        return $query->where('id_campo', '4');
    }
    public function scopeDeValor($query, $valor)
    {
        return $query->where('valor', $valor);
    }
    
    //GETS
    public function getEtiquetaLabelAttribute()
    {
        $estado     = "No Aplica";
        $background = "red";
        
        if($this->id_cat == 1)//Por enviar
        {
            $estado     = $this->etiqueta;
            $background = "#EBAD50";
        }
        elseif($this->id_cat == 3)//Parcial
        {
            $estado     = $this->etiqueta;
            $background = "#1C84C6";
        }
        elseif($this->id_cat == 2)//Enviado
        {
            $estado     = $this->etiqueta;
            $background = "#66B85B";
        }
        
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$estado.'</span>';
    }
}
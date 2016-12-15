<?php
namespace Flexio\Modulo\Entradas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class EntradasCat extends Model
{
    
    protected $table = 'ent_entradas_cat';
    public $timestamps = false;
    protected $dateFormat = 'U';
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
<?php
namespace Flexio\Modulo\FacturasVentas\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class FacturaVentaCatalogo extends Model
{
    protected $table = 'fac_factura_catalogo';
    protected $guarded = ['id'];
    
    //Gets
    
    public function getValorSpanAttribute()
    {
        $estado     = "No Aplica";
        $background = "red";
        
        if($this->id == 13)//Por aprobrar
        {
            $estado     = $this->valor;
            $background = "#EBAD50";
        }
        elseif($this->id == 14)//Por pagar
        {
            $estado     = $this->valor;
            $background = "#1C84C6";
        }
        elseif($this->id == 15)//Pagada parcial
        {
            $estado     = $this->valor;
            $background = "#23C6C8";
        }
        elseif($this->id == 16)//Pagada completa
        {
            $estado     = $this->valor;
            $background = "#1AB394";
        }
        elseif($this->id == 17)//Anulado
        {
            $estado     = $this->valor;
            $background = "#D1DADE";
        }
        
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$estado.'</span>';
    }


    public function scopeEstadosFacturaVenta($query)
    {
        return $query->where("tipo", "etapa");
    }

    public function scopeTerminoFacturaVenta($query)
    {
        return $query->where("tipo", "termino_pago");
    }


}

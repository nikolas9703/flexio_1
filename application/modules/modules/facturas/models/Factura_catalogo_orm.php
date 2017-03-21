<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Factura_catalogo_orm extends Model
{
    protected $table = 'fac_factura_catalogo';
    protected $guarded = ['id'];

    public function scopeDeEtiqueta($query, $etiqueta)
    {
        return $query->where("etiqueta", $etiqueta);
    }

    public function scopeEstadosFacturasCompras($query)
    {
        return $query->where("tipo", "estado_factura_compra");
    }

    public function scopeTiposFacturasCompras($query)
    {
        return $query->where("tipo", "tipo_factura_compra");
    }

    public function valorSpan()
    {
        $estado     = $this->valor==null ?"No Aplica": $this->valor;
        $background = "red";

        switch ($this->id) {
            case 13: //Por aprobrar
                $background = "#EBAD50";
                break;
            case 14: //Por aprobrar
                $background = "#1C84C6";
                break;
            case 15: //Pagada parcial
                $background = "#23C6C8";
                break;
            case 16: //Pagada completa
                $background = "#1AB394";
                break;
            case 17: //Anulado
                $background = "black";
                break;
        }
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$estado.'</span>';
    }
}

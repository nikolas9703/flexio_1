<?php
namespace Flexio\Modulo\Salidas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Salidas extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['uuid_salida', 'prefijo', 'numero', 'estado_id', 'created_by', 'empresa_id', 'operacion_id', 'operacion_type', 'comentarios'];

    protected $table        = 'sal_salidas';
    public $timestamps      = true;
    protected $fillable     = ['uuid_salida', 'prefijo', 'numero', 'estado_id', 'created_by', 'empresa_id', 'operacion_id', 'operacion_type', 'comentarios'];
    protected $guarded      = ['id'];
    protected $prefijo      = "SAL";

    public static function findByUuid($uuid){
        return self::where('uuid_salidad',hex2bin($uuid))->first();
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    //GETS
    public function getUuidSalidaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getNumeroSalidaAttribute()
    {
        return $this->prefijo.$this->numero;
    }
    public function getNumeroSalidaEnlaceAttribute()
    {
        return '<a href="'. base_url('salidas/ver/'. $this->uuid_salida) .'" style="color:blue;">'.$this->prefijo.$this->numero.'</a>';
    }
    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }
    public function getNumeroSalidaBtnAttribute()
    {
        return '<a href="'. base_url('salidas/ver/'. $this->uuid_salida) .'" class="btn btn-block btn-outline btn-success">Ver Salida</a>';
    }
    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
    public function getUpdatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    //puede quedarse---
    public function getItemsAttribute()
    {
        $collect = Collect([]);
        if(is_null($this->operacion))
        {
            return $collect;
        }

        $aux = $this->operacion_type == 'Flexio\\Modulo\\OrdenesVentas\\Models\\OrdenVenta' ? $this->operacion->items2 : $this->operacion->items;

        //si $aux es null retorna array vacio
        if(is_null($aux))
        {
            return $collect;
        }

        return $aux->filter(function($item){
            return $item->tipo_id == "4" || $item->tipo_id == "5" || $item->tipo_id == "8";
        });//solo los tipos 4, 5 y 8 generan entradas
    }

    public function getTipoAttribute(){
        $tipo = "No Aplica";

        if($this->operacion_type == "Flexio\Modulo\Consumos\Models\Consumos")
        {
            $tipo = "Consumo";
        }
        elseif($this->operacion_type == "Flexio\Modulo\Traslados\Models\Traslados")
        {
            $tipo = "Traslado";
        }
        elseif($this->operacion_type == "Flexio\Modulo\Ajustes\Models\Ajustes")
        {
            $tipo = "Ajuste";
        }
        elseif($this->operacion_type == "Flexio\Modulo\OrdenesVentas\Models\OrdenVenta")
        {
            $tipo = "Orden de venta";
        }
        elseif($this->operacion_type == "Flexio\Modulo\FacturasVentas\Models\FacturaVenta")
        {
            $tipo = "Ajuste";
        }

        return $tipo;
    }


    //SCOPES
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('empresa_id', $empresa_id);
    }

    public function scopeDeFechaDesde($query, $fecha_desde)
    {
        return $query->whereDate("created_at", ">=", date('Y-m-d', strtotime($fecha_desde)));
    }

    public function scopeDeFechaHasta($query, $fecha_hasta)
    {
        return $query->whereDate("created_at", "<=", date('Y-m-d', strtotime($fecha_hasta)));
    }

    public function scopeDeEstado($query, $estado)
    {
        return $query->where("estado_id", $estado);
    }



    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('operacion_type', $tipo);
    }

    public function scopeDeTipoId($query, $tipo_id)
    {
        return $query->where('operacion_id', $tipo_id);
    }

    public function scopeDeEnviarDesde($query, $enviar_desde)
    {
        return  $query->where(function($q) use ($enviar_desde){
                    $q->where("operacion_type", "Flexio\Modulo\Consumos\Models\Consumos")
                    ->whereHas("consumo", function($q3) use ($enviar_desde){
                        $q3->where("uuid_bodega",hex2bin(strtolower($enviar_desde)));
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q4) use ($enviar_desde){
                        $q4->where("uuid_lugar_anterior",hex2bin(strtolower($enviar_desde)));
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Ajustes\Models\Ajustes")
                    ->whereHas("ajuste", function($q6) use ($enviar_desde){
                        $q6->where("uuid_bodega",hex2bin(strtolower($enviar_desde)))
                        ->where("tipo_ajuste_id", "1");//Ajuste Negativo
                    });
                });
    }

    public function scopeDeDestino($query, $destino)
    {  
        return  $query->where(function($q) use ($destino){
                    $q->where("operacion_type", "Flexio\Modulo\Consumos\Models\Consumos")
                    ->whereHas("consumo", function($q3) use ($destino){
                        if (!is_numeric($destino))
                        $q3->where("uuid_colaborador",hex2bin(strtolower($destino)));
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q4) use ($destino){
                        if (!is_numeric($destino))
                        $q4->where("uuid_lugar",hex2bin(strtolower($destino)));
                        
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\OrdenesVentas\Models\OrdenVenta")
                    ->whereHas("orden_venta", function($q4) use ($destino){
                        $q4->where("cliente_id",$destino);
                    });
                });
    }
    
    public function scopeDeCliente($cliente_id) 
    {
        return $this->hasMany($related);
    }

    public function scopeNoAjuste($query)
    {
        return $query->where("operacion_type", "!=", "Flexio\Modulo\Ajustes\Models\Ajustes");
    }

    public function scopeDeItem($query, $item_id)
    {
        return  $query->where(function($q) use ($item_id){
                    $q->where("operacion_type", "Flexio\Modulo\Consumos\Models\Consumos")
                    ->whereHas("consumo", function($q2) use ($item_id){
                        $q2->whereHas("items", function($q3) use ($item_id){
                            $q3->where("item_id", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q4) use ($item_id){
                        $q4->whereHas("items", function($q5) use ($item_id){
                            $q5->where("item_id", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Ajustes\Models\Ajustes")
                    ->whereHas("ajuste", function($q6) use ($item_id){
                        $q6->whereHas("items", function($q7) use ($item_id){
                            $q7->where("item_id", $item_id);
                        })->where("tipo_ajuste_id", "1");//Ajuste Negativo
                    });
                });
    }
    public function scopeDeNumero($query, $numero)
    {
        return $query->where("numero", "like", "%$numero%");
    }
    public function scopeDeEstadosValidos($query)
    {
        return $query->where('estado_id', '<', '4');
    }


    //RELACIONES
    public function operacion()
    {
        return $this->morphTo();
    }

    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\Salidas\Models\SalidasCat', 'estado_id', 'id_cat');
    }

    public function consumo()
    {
        return $this->belongsTo('Flexio\Modulo\Consumos\Models\Consumos', 'operacion_id', 'id');
    }

    public function traslado()
    {
        return $this->belongsTo('Flexio\Modulo\Traslados\Models\Traslados', 'operacion_id', 'id');
    }
    
    public function orden_venta()
    {
        return $this->belongsTo('Flexio\Modulo\OrdenesVentas\Models\OrdenVenta', 'operacion_id', 'id');
    }

    public function ajuste()
    {
        return $this->belongsTo('Flexio\Modulo\Ajustes\Models\Ajustes', 'operacion_id', 'id');
    }

    public function getNumeroDocumentoAttribute()
    {

    	return $this->numero;
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
    	$attrs = [
    	"href"  => $this->enlace,
    	"class" => "link"
    			];

    	$html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
    	return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }
    public function getEnlaceAttribute()
    {
    	return base_url("salidas/ver/".$this->uuid_salida);
    }

}

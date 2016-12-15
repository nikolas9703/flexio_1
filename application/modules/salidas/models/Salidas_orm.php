<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Salidas_orm extends Model
{

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'sal_salidas';


    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';


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
     * Instancia de CodeIgniter
     */
    protected $codeIgniter;


    public function __construct() {
        $this->codeIgniter = & get_instance();

        //Cargando Modelos
        $this->codeIgniter->load->model("ajustes/Ajustes_orm");
    }


    public function operacion()
    {
        return $this->morphTo();
    }

    public static function findByUuid($uuid){
        return self::where('uuid_salida',hex2bin($uuid))->first();
    }

    public static function findByOperacion($operacion_id, $operacion_type){
        return  self::where('operacion_id', $operacion_id)
                ->where("operacion_type", $operacion_type)
                ->first();
    }


    public function getUuidSalidaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }


    /**
     * Obtiene fecha de creacion formateada

     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    public function estado()
    {
        return $this->belongsTo('Salidas_cat_orm', 'estado_id', 'id_cat');
    }

    public function consumo()
    {
        return $this->belongsTo('Consumos_orm', 'operacion_id', 'id');
    }

    public function traslado()
    {
        return $this->belongsTo('Traslados_orm', 'operacion_id', 'id');
    }

    public function ajuste()
    {
        return $this->belongsTo('Ajustes_orm', 'operacion_id', 'id');
    }

    public function venta()
    {
        return $this->belongsTo('Orden_ventas_orm', 'operacion_id', 'id');
    }


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
    public function scopeDeTipoRelacion($query, $id,$tipo)
    {
        $query->DeTipo($tipo);
        return $query->DeOperacionId($id);
    }
    public function scopeDeOperacionType($query, $tipo)
    {
        return $query->where('operacion_type', $tipo);
    }
    public function scopeDeOperacionId($query, $operacion_id)
    {
        return $query->where('operacion_id', $operacion_id);
    }
    public function scopeDeEstadosValidos($query)
    {
        return $query->where('estado_id', '<', '4');
    }
    public function scopeDeNumero($query, $numero)
    {
        return $query->where("numero", "like", "%$numero%");
    }



    public function scopeDeDestino($query, $destino)
    {
        $cliente_id = "";
        $cliente    = Cliente_orm::findByUuid($destino);

        if(count($cliente))
        {
            $cliente_id = $cliente->id;
        }

        return  $query->where(function($q) use ($destino, $cliente_id){
                    $q->where("operacion_type", "Orden_ventas_orm")
                    ->whereHas("venta", function($q2) use ($cliente_id){
                        $q2->where("cliente_id",$cliente_id);
                    })
                    ->orWhere("operacion_type", "Consumos_orm")
                    ->whereHas("consumo", function($q3) use ($destino){
                        $q3->where("uuid_colaborador",hex2bin(strtolower($destino)));
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q4) use ($destino){
                        $q4->where("uuid_lugar",hex2bin(strtolower($destino)));
                    });
                });
    }

    public function scopeDeItem($query, $item_id)
    {
        return  $query->where(function($q) use ($item_id){
                    $q->where("operacion_type", "Orden_ventas_orm")
                    ->whereHas("venta", function($q2) use ($item_id){
                        $q2->whereHas("items_orden_ventas", function($q3) use ($item_id){
                            $q3->where("item_id", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q4) use ($item_id){
                        $q4->whereHas("traslados_items", function($q5) use ($item_id){
                            $q5->where("id_item", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Consumos_orm")
                    ->whereHas("consumo", function($q4) use ($item_id){
                        $q4->whereHas("consumos_items", function($q5) use ($item_id){
                            $q5->where("item_id", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Ajustes_orm")
                    ->whereHas("ajuste", function($q6) use ($item_id){
                        $q6->whereHas("ajuste_items", function($q7) use ($item_id){
                            $q7->where("item_id", $item_id);
                        })->where("tipo_ajuste_id", "1");//Ajuste Negativo
                    });
                });
    }

    public function scopeDeEnviarDesde($query, $enviar_desde)
    {
        $bodega_id  = "";
        $bodega     = Bodegas_orm::findByUuid($enviar_desde);

        if(count($bodega))
        {
            $bodega_id = $bodega->id;
        }

        return  $query->where(function($q) use ($enviar_desde, $bodega_id){
                    $q->where("operacion_type", "Orden_ventas_orm")
                    ->whereHas("venta", function($q2) use ($bodega_id){
                        $q2->where("bodega_id",$bodega_id);
                    })
                    ->orWhere("operacion_type", "Consumos_orm")
                    ->whereHas("consumo", function($q3) use ($enviar_desde){
                        $q3->where("uuid_bodega",hex2bin(strtolower($enviar_desde)));
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q4) use ($enviar_desde){
                        $q4->where("uuid_lugar_anterior",hex2bin(strtolower($enviar_desde)));
                    })
                    ->orWhere("operacion_type", "Ajustes_orm")
                    ->whereHas("ajuste", function($q6) use ($enviar_desde){
                        $q6->where("uuid_bodega",hex2bin(strtolower($enviar_desde)))
                        ->where("tipo_ajuste_id", "1");//Ajuste Negativo
                    });
                });
    }

    public function scopeNoAjuste($query)
    {
        return $query->where("operacion_type", "!=", "Ajustes_orm");
    }


    public function scopeDeNumeroDocumento($query, $numero)
    {
        return  $query->where(function($q) use ($numero){
                    $q->where("operacion_type", "Orden_ventas_orm")
                    ->whereHas("venta", function($q2) use ($numero){
                        $q2->where("codigo", "like", "%$numero%");
                    })
                    ->orWhere("operacion_type", "Consumos_orm")
                    ->whereHas("consumo", function($q4) use ($numero){
                        $q4->where("numero", "like", "%$numero%");
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($numero){
                        $q3->where("numero", "like", "%$numero%");
                    });
                });
    }

    public function scopeDeReferencia($query, $referencia)
    {
        return  $query->where(function($q) use ($referencia){
                    $q->where("operacion_type", "Orden_ventas_orm")
                    ->whereHas("venta", function($q2) use ($referencia){
                        $q2->where("referencia", "like", "%$referencia%");
                    })
                    ->orWhere("operacion_type", "Consumos_orm")
                    ->whereHas("consumo", function($q4) use ($referencia){
                        $q4->where("referencia", "like", "%$referencia%");
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($referencia){
                        $q3->where("referencia", "like", "%$referencia%");
                    });
                });
    }

    public function comp__tipo()
    {
        $tipos = [
            "Flexio\\Modulo\\Ajustes\\Models\\Ajustes"              => "Ajuste",
            "Flexio\\Modulo\\Traslados\\Models\\Traslados"          => "Traslado",
            "Flexio\\Modulo\\Consumos\\Models\\Consumos"            => "Consumo",
            "Flexio\\Modulo\\OrdenesVentas\\Models\\OrdenVenta"     => "Orden de venta",
            "Flexio\\Modulo\\FacturasVentas\\Models\\FacturaVenta"  => "Factura de venta"
        ];

        return (isset($tipos[$this->operacion_type])) ? $tipos[$this->operacion_type] : 'Falta integrar';
    }


    public function comp__origen()
    {
        return count($this->operacion) ? $this->operacion->origen->nombre_completo_enlace : '...';
    }

    public function comp__origenModel()
    {
        if($this->operacion_type == "Flexio\\Modulo\\Traslados\\Models\\Traslados")
        {
            return $this->operacion->deBodega;
        }

        return $this->operacion->bodega;
    }

    public function comp__destino()
    {
        return (count($this->operacion) and ($this->operacion_type != "Flexio\\Modulo\\Ajustes\\Models\\Ajustes" and $this->operacion->tipo_ajuste_id != "2")) ? $this->operacion->destino->nombre_completo_enlace : '...';
    }

    public function comp__destinoModel()
    {
        if($this->operacion_type == "Flexio\\Modulo\\Traslados\\Models\\Traslados")//va a una bodega
        {
            return $this->operacion->bodega;
        }
        elseif($this->operacion_type == "Flexio\\Modulo\\Consumos\\Models\\Consumos")//va a un colaborador
        {
            return $this->operacion->colaborador;
        }
        elseif($this->operacion_type == "Flexio\\Modulo\\OrdenesVentas\\Models\\OrdenVenta")//va a un cliente
        {
            return $this->operacion->cliente;
        }
    }

    public function getItemsAttribute()
    {
        echo $this->operacion_type."<br>\n";
        return $this->operacion->items2->filter(function($item){
            return $item->tipo_id == "4" || $item->tipo_id == "5" || $item->tipo_id == "8";
        });
    }

    public function comp__numeroSalida()
    {
        return $this->prefijo.$this->numero;
    }

    public function comp__numeroDocumento()
    {
        return count($this->operacion) ? $this->operacion->numero_documento_enlace : 'Registro Roto';
    }

}

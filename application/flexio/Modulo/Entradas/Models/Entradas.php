<?php
namespace Flexio\Modulo\Entradas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Entradas extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf =['codigo', 'estado_id', 'empresa_id', 'operacion_id', 'operacion_type', 'comentarios'];

    protected $table        = 'ent_entradas';
    public $timestamps      = true;
    protected $fillable     = ['uuid_entrada', 'codigo', 'estado_id', 'empresa_id', 'operacion_id', 'operacion_type', 'comentarios'];
    protected $guarded      = ['id'];
    protected $prefijo      = "ENT";
    protected $appends      = ['icono','enlace'];


    public function __construct(array $attributes = array()){
      $this->setRawAttributes(array_merge($this->attributes, array('uuid_entrada' => Capsule::raw("ORDER_UUID(uuid())"))), true);
      parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    public static function findByUuid($uuid){
        return self::where('uuid_entrada',hex2bin($uuid))->first();
    }

    //GETS
    public function getCodigoAttribute($value)
    {
        $numero = $this->prefijo.sprintf('%08d', $value);
        return $numero;
       // return sprintf('%08d', $value);
    }
    public function getUuidEntradaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidOrigenAttribute()
    {
        if($this->operacion_type == "Flexio\Modulo\Ajustes\Models\Ajustes" || (!count($this->operacion->proveedor) && !count($this->operacion->deBodega) ))
        {
            return "";//En el ajuste se desconoce el origen
        }

        return count($this->operacion->proveedor) ? $this->operacion->proveedor->uuid_proveedor : $this->operacion->deBodega->uuid_bodega;
    }
    public function getUuidCentroContableAttribute()
    {
        if($this->operacion_type == "Flexio\Modulo\Traslados\Models\Traslados")
        {
            return "";//Un traslado no tiene centro contable
        }
        return count($this->operacion->centro_contable) ? $this->operacion->centro_contable->uuid_centro : "";
    }

    public function getUuidBodegaAttribute()
    {
        return count($this->operacion->bodega) ? $this->operacion->bodega->uuid_bodega : "";
    }
    public function getNumeroDocumentoAttribute()
    {
        return $this->operacion->numero_documento;
    }
    public function getNumeroDocumentoEnlaceAttribute()
    {
        return '<a href="#" style="color:blue;">'.$this->operacion->numero_documento.'</a>';
    }
    public function getNumeroEntradaAttribute()
    {
        return $this->codigo;
    }
    public function getNumeroEntradaEnlaceAttribute()
    {
        return '<a href="'. base_url('entradas/ver/'. $this->uuid_entrada) .'" style="color:blue;">'.$this->codigo.'</a>';
    }
    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
    public function getItemsAttribute()
    {
        //falta traslados
        //falta ajustes
        return $this->operacion->items->filter(function($item){
            return $item->tipo_id == "4" || $item->tipo_id == "5" || $item->tipo_id == "8";
        });//solo los tipos 4, 5 y 8 generan entradas
    }
    public function getTipoAttribute(){
        $tipo = "No Aplica";

        if($this->operacion_type == "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
        {
            $tipo = "&Oacute;rden de compra";
        }
        elseif($this->operacion_type == "Flexio\Modulo\Traslados\Models\Traslados")
        {
            $tipo = "Traslado";
        }
        elseif($this->operacion_type == "Flexio\Modulo\Ajustes\Models\Ajustes")
        {
            $tipo = "Ajuste";
        }

        return $tipo;
    }
    public function getOrigenAttribute(){

        if($this->operacion_type == "Flexio\Modulo\Ajustes\Models\Ajustes")
        {
            return "Ajuste Positivo";
        }

        if($this->operacion_type == "Flexio\Modulo\Devoluciones\Models\Devolucion")
        {
            return "Devolucion";
        }

        if(!count($this->operacion->proveedor) && !count($this->operacion->deBodega))
        {
            return 'Sin registro';
        }

        $html   = count($this->operacion->proveedor) ? $this->operacion->proveedor->nombre : $this->operacion->deBodega->nombre;
        $href   = count($this->operacion->proveedor) ? base_url('proveedores/ver/'. $this->operacion->proveedor->uuid_proveedor) : base_url('bodegas/ver/'. $this->operacion->deBodega->uuid_bodega);

        return '<a href="'. $href .'" style="color:blue;">'.$html.'</a>';

    }

    //SCOPES
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('empresa_id', $empresa_id);
    }

    public function scopeDeFacturaDeCompra($entradas, $factura_compra_id)
    {
        return $entradas->whereHas("orden", function($orden) use ($factura_compra_id){
            $orden->whereHas("facturas", function($facturas) use ($factura_compra_id){
                $facturas->where("id", $factura_compra_id);
            });
        });
    }

    public function scopeDeFechaDesde($query, $fecha_desde)
    {
        return $query->where("created_at", ">=", date('Y-m-d', strtotime($fecha_desde)));
    }

    public function empresa()
    {
    	return  $this->belongsTo('Flexio\Modulo\Empresa\Models\Empresa', 'empresa_id');
     }


    public function scopeDeEstado($query, $estado)
    {
        return $query->where("estado_id", $estado);
    }

    public function scopeDeFechaHasta($query, $fecha_hasta)
    {
        return $query->where("created_at", "<=", date('Y-m-d', strtotime($fecha_hasta)));
    }

    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('operacion_type', $tipo);
    }

    public function scopeDeTipoId($query, $tipo_id)
    {
        return $query->where('operacion_id', $tipo_id);
    }

    public function scopeDeProveedor($query, $proveedor)
    {
        return  $query->where(function($q) use ($proveedor){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($proveedor){
                        $q2->where("uuid_proveedor",hex2bin(strtolower($proveedor)));
                    });
                });
    }

    public function scopeDeOrigen($query, $origen)
    {
        return  $query->where(function($q) use ($origen){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($origen){
                        $q2->where("uuid_proveedor",hex2bin(strtolower($origen)));
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q3) use ($origen){
                        $q3->where("uuid_lugar_anterior",hex2bin(strtolower($origen)));
                    });
                });
    }

    public function scopeWithMontosDesde($query, $montos_de)
    {
        return  $query->where(function($q) use ($montos_de){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($montos_de){
                        $q2->where("monto", ">=", $montos_de);
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q3) use ($montos_de){
                        $q3->where("monto", ">=", $montos_de);
                    });
                });
    }

    public function scopeWithMontosHasta($query, $montos_a)
    {
        return  $query->where(function($q) use ($montos_a){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($montos_a){
                        $q2->where("monto", "<=", $montos_a);
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q3) use ($montos_a){
                        $q3->where("monto", "<=", $montos_a);
                    });
                });
    }

    public function scopeWithReferencia($query, $referencia)
    {
        return  $query->where(function($q) use ($referencia){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($referencia){
                        $q2->where("referencia", "like", "%$referencia%");
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q3) use ($referencia){
                        $q3->where("referencia", "like", "%$referencia%");
                    });
                });
    }

    public function scopeWithNumeroDocumento($query, $numero)
    {
        return  $query->where(function($q) use ($numero){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($numero){
                        $q2->where("numero", "like", "%$numero%");
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q3) use ($numero){
                        $q3->where("numero", "like", "%$numero%");
                    });
                });
    }

    public function scopeWithRecepcionEn($query, $recibir_en)
    {
        return  $query->where(function($q) use ($recibir_en){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($recibir_en){
                        $q2->where("uuid_lugar",hex2bin(strtolower($recibir_en)));
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q3) use ($recibir_en){
                        $q3->where("uuid_lugar",hex2bin(strtolower($recibir_en)));
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Ajustes\Models\Ajustes")
                    ->whereHas("ajuste", function($q6) use ($recibir_en){
                        $q6->where("uuid_bodega",hex2bin(strtolower($recibir_en)))
                        ->where("tipo_ajuste_id", "2");//Ajuste Positivo
                    });
                });
    }

    public function scopeNoAjuste($query)
    {
        return $query->where("operacion_type", "!=", "Flexio\Modulo\Ajustes\Models\Ajustes");
    }

    public function scopeDeItem($query, $item_id)
    {
        return  $query->where(function($q) use ($item_id){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($item_id){
                        $q2->whereHas("items", function($q3) use ($item_id){
                            $q3->where("item_id", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q4) use ($item_id){
                        $q4->whereHas("items", function($q5) use ($item_id){
                            $q5->where("item_id", $item_id);
                        });
                    });
//                    ->orWhere("operacion_type", "Flexio\Modulo\Ajustes\Models\Ajustes")
//                    ->whereHas("ajuste", function($q6) use ($item_id){
//                        $q6->whereHas("ajuste_items", function($q7) use ($item_id){
//                            $q7->where("item_id", $item_id);
//                        })->where("tipo_ajuste_id", "2");//Ajuste Positivo
//                    });
                });
    }

    public function scopeDeSerie($query, $serie_id)
    {

        return  $query->where(function($q) use ($serie_id){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($serie_id){
                        $q2->whereHas("lines_items", function($line_item) use ($serie_id){
                            $line_item->whereHas('seriales', function($serie) use ($serie_id){
                                $serie->where('inv_items_seriales.id', $serie_id);
                            });
                        });
                    });
                });
    }


    //RELACIONES
    public function operacion()
    {
        return $this->morphTo();
    }

    public function estado()
    {
        return $this->belongsTo('Flexio\Modulo\Entradas\Models\EntradasCat', 'estado_id', 'id_cat');
    }

    //orden
    //traslado
    //ajuste
    public function orden()
    {
        return $this->belongsTo('Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra', 'operacion_id', 'id');
    }

    public function traslado()
    {
        return $this->belongsTo('Flexio\Modulo\Traslados\Models\Traslados', 'operacion_id', 'id');
    }

    public function ajuste()
    {
        return $this->belongsTo('Flexio\Modulo\Ajustes\Models\Ajustes', 'operacion_id', 'id');
    }

    public function getIconoAttribute(){
        return 'fa fa-cubes';
    }
    public function getEnlaceAttribute()
    {
        return base_url("entradas/ver/".$this->uuid_entrada);
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    //Mostrar Comentarios
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
}

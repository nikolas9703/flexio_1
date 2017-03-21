<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Entradas_orm extends Model
{
    
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ent_entradas';
    
    
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
        return self::where('uuid_entrada',hex2bin($uuid))->first();
    }
    
    public static function findByOperacion($operacion_id, $operacion_type){
        return  self::where('operacion_id', $operacion_id)
                ->where("operacion_type", $operacion_type)
                ->first();
    }
    

    public function getCodigoAttribute($value)
    {
        return sprintf('%08d', $value);
    }
    public function getUuidEntradaAttribute($value)
    {
        return strtoupper(bin2hex($value));
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
        return date('d/m/Y', strtotime($value));
    }
    
    public function getUpdatedAtAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }
    
    public function estado()
    {
        return $this->belongsTo('Entradas_cat_orm', 'estado_id', 'id_cat');
    }
    
    public function orden()
    {
        return $this->belongsTo('Ordenes_orm', 'operacion_id', 'id');
    }
    
    public function traslado()
    {
        return $this->belongsTo('Traslados_orm', 'operacion_id', 'id');
    }
    
    public function ajuste()
    {
        return $this->belongsTo('Ajustes_orm', 'operacion_id', 'id');
    }
    
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('empresa_id', $empresa_id);
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
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($proveedor){
                        $q2->where("uuid_proveedor",hex2bin(strtolower($proveedor)));
                    });
                });
    }
    
    public function scopeDeOrigen($query, $origen)
    {
        return  $query->where(function($q) use ($origen){
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($origen){
                        $q2->where("uuid_proveedor",hex2bin(strtolower($origen)));
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($origen){
                        $q3->where("uuid_lugar_anterior",hex2bin(strtolower($origen)));
                    });
                });
    }
    
    public function scopeWithMontosDesde($query, $montos_de)
    {
        return  $query->where(function($q) use ($montos_de){
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($montos_de){
                        $q2->where("monto", ">=", $montos_de);
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($montos_de){
                        $q3->where("monto", ">=", $montos_de);
                    });
                });
    }
    
    public function scopeWithMontosHasta($query, $montos_a)
    {
        return  $query->where(function($q) use ($montos_a){
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($montos_a){
                        $q2->where("monto", "<=", $montos_a);
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($montos_a){
                        $q3->where("monto", "<=", $montos_a);
                    });
                });
    }
    
    public function scopeWithReferencia($query, $referencia)
    {
        return  $query->where(function($q) use ($referencia){
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($referencia){
                        $q2->where("referencia", "like", "%$referencia%");
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($referencia){
                        $q3->where("referencia", "like", "%$referencia%");
                    });
                });
    }
    
    public function scopeWithNumeroDocumento($query, $numero)
    {
        return  $query->where(function($q) use ($numero){
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($numero){
                        $q2->where("numero", "like", "%$numero%");
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($numero){
                        $q3->where("numero", "like", "%$numero%");
                    });
                });
    }
    
    public function scopeWithRecepcionEn($query, $recibir_en)
    {
        return  $query->where(function($q) use ($recibir_en){
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($recibir_en){
                        $q2->where("uuid_lugar",hex2bin(strtolower($recibir_en)));
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q3) use ($recibir_en){
                        $q3->where("uuid_lugar",hex2bin(strtolower($recibir_en)));
                    })
                    ->orWhere("operacion_type", "Ajustes_orm")
                    ->whereHas("ajuste", function($q6) use ($recibir_en){
                        $q6->where("uuid_bodega",hex2bin(strtolower($recibir_en)))
                        ->where("tipo_ajuste_id", "2");//Ajuste Positivo
                    });
                });
    }
    
    public function scopeNoAjuste($query)
    {
        return $query->where("operacion_type", "!=", "Ajustes_orm");
    }
    
    public function scopeDeItem($query, $item_id)
    {
        return  $query->where(function($q) use ($item_id){
                    $q->where("operacion_type", "Ordenes_orm")
                    ->whereHas("orden", function($q2) use ($item_id){
                        $q2->whereHas("items", function($q3) use ($item_id){
                            $q3->where("id_item", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Traslados_orm")
                    ->whereHas("traslado", function($q4) use ($item_id){
                        $q4->whereHas("traslados_items", function($q5) use ($item_id){
                            $q5->where("id_item", $item_id);
                        });
                    })
                    ->orWhere("operacion_type", "Ajustes_orm")
                    ->whereHas("ajuste", function($q6) use ($item_id){
                        $q6->whereHas("ajuste_items", function($q7) use ($item_id){
                            $q7->where("item_id", $item_id);
                        })->where("tipo_ajuste_id", "2");//Ajuste Positivo
                    });
                });
    }
    
    public function comp__tipo()
    {
        $tipo = "No Aplica";
        
        if($this->operacion_type == "Ordenes_orm")//Por recibir
        {
            $tipo = "&Oacute;rden de compra";
        }
        elseif($this->operacion_type == "Traslados_orm")//Parcial
        {
            $tipo = "Traslado";
        }
        
        return $tipo;
    }
    
    public function comp__origen()
    {
        $html = "No aplica";
        $href = "#";
        
        if($this->operacion_type == "Ajustes_orm")
        {
            return "Ajuste Positivo";
        }
        
        $html   = isset($this->operacion->proveedor->id) ? $this->operacion->proveedor->nombre : $this->operacion->deBodega->nombre;
        $href   = isset($this->operacion->proveedor->id) ? base_url('proveedores/ver/'. $this->operacion->proveedor->uuid_proveedor) : base_url('bodegas/ver/'. $this->operacion->deBodega->uuid_bodega);
        
        return '<a href="'. $href .'" style="color:blue;">'.$html.'</a>';
    }
    
    public function comp__uuidOrigen()
    {
        $uuidOrigen = "";
        
        if($this->operacion_type == "Ajustes_orm")
        {
            return $uuidOrigen;//En el ajuste se desconoce el origen
        }
        
        $uuidOrigen = isset($this->operacion->proveedor->id) ? $this->operacion->proveedor->uuid_proveedor : $this->operacion->deBodega->uuid_bodega;
        
        return $uuidOrigen;
    }
    
    public function comp__numeroDocumento()
    {
        $numeroDocumento    = $this->operacion->numero;
        $prefijo            = "";
        
        if($this->operacion_type == "Ordenes_orm")
        {
            $prefijo = "OC";
        }
        elseif($this->operacion_type == "Traslados_orm")
        {
            $prefijo = "TRAS";
        }
        
        return $prefijo.$numeroDocumento;
    }
    
    /**
     * Los items que son de tipo servicios o no inventariados no se muestran
     * en las entradas
     * servicios = (int) 7;
     * no inventariados = (int) 6; 
     * 
     * @return model
     */
    public function comp__entradasItemsModel()
    {
        if($this->operacion_type == "Traslados_orm")//viene de un traslado
        {
            return Traslados_items_orm::where("id_traslado", $this->operacion_id)
                    ->whereHas("item", function($q){
                        $q->where(function($q2){
                            $q2->where("tipo_id", "4")//inventariado
                                ->orWhere("tipo_id", "5")//inventariado con serie
                                ->orWhere("tipo_id", "8");//activos fijos
                        });
                    })->get();
        }
        elseif($this->operacion_type == "Ordenes_orm")//viene de una compra
        {
            return Ordenes_items_orm::where("id_orden", $this->operacion_id)
                    ->whereHas("item", function($q){
                        $q->where(function($q2){
                            $q2->where("tipo_id", "4")//inventariado
                                ->orWhere("tipo_id", "5")//inventariado con serie
                                ->orWhere("tipo_id", "8");//activos fijos
                        });
                    })->get();
        }
        elseif($this->operacion_type == "Ajustes_orm")//viene de un ajuste
        {
            return Ajustes_items_orm::where("ajuste_id", $this->operacion_id)
                    ->whereHas("item", function($q){
                        $q->where(function($q2){
                            $q2->where("tipo_id", "4")//inventariado
                                ->orWhere("tipo_id", "5")//inventariado con serie
                                ->orWhere("tipo_id", "8");//activos fijos
                        });
                    })->get();
        }
    }
	
}
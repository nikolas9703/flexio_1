<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Traslados_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'tras_traslados';


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
    public function getUuidTrasladoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }

    public function getFechaCreacionAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getFechaEntregaAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('id_empresa', $empresa_id);
    }

    public function scopeDeTraslado($query, $numero_traslado)
    {
        return $query->where('numero', "like", "%$numero_traslado%");
    }

    public function scopeDeEstado($query, $estado)
    {
        return $query->where('id_estado', $estado);
    }

    public function scopeDeProcedencia($query, $de_bodega)
    {
        return $query->where('uuid_lugar_anterior', hex2bin(strtolower($de_bodega)));
    }

    public function scopeDeDestino($query, $a_bodega)
    {
        return $query->where('uuid_lugar', hex2bin(strtolower($a_bodega)));
    }

    public function scopeDeFechaDeSolicitud($query, $fecha)
    {
        return $query->where('fecha_creacion', $fecha);
    }

    public function scopeDeFechaDeEntrega($query, $fecha)
    {
        return $query->where('fecha_entrega', $fecha);
    }

    public function scopeConItem($query, $item_id)
    {
        return $query->whereHas('traslados_items', function($q) use($item_id){
            $q->where("item_id", $item_id);
        });
    }

    public function pedido()
    {
        return $this->belongsTo('Pedidos_orm', 'uuid_pedido', 'uuid_pedido');
    }

    public function items()
    {
        return $this->belongsToMany('Items_orm', 'lines_items', 'tipoable_id', 'item_id')
                ->where("tipoable_type", "Flexio\Modulo\Traslados\Models\Traslados")
                ->withPivot("cantidad", "unidad", "precio_unidad", "uuid_impuesto", "descuento", "cuenta");
    }

    public function traslados_items()
    {
        return $this->hasMany('Traslados_items_orm', 'tipoable_id', 'id')
            ->where("tipoable_type", "Flexio\Modulo\Traslados\Models\Traslados");
    }

    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'uuid_lugar', 'uuid_bodega');
    }

    public function estado()
    {
        return $this->belongsTo('Traslados_cat_orm', 'id_estado', 'id_cat');
    }

    public function deBodega()
    {
        return $this->belongsTo('Bodegas_orm', 'uuid_lugar_anterior', 'uuid_bodega');
    }


    public static function findByUuid($uuid){
        return self::where('uuid_traslado',hex2bin($uuid))->first();
    }

    public function comp_numeroDocumento()
    {
        return $this->prefijo.$this->numero;
    }

    public function lines_items()
    {
        return $this->hasMany('Flexio\Modulo\Inventarios\Models\LinesItems', 'tipoable_id')
        ->where('tipoable_type', 'Flexio\Modulo\Traslados\Models\Traslados');
    }

    public function cantidadTrasladadaItem($item_id)
    {
        $cantidadTrasladada = 0;
        $factorConversion   = 1;

        foreach($this->traslados_items as $ti)
        {
            $unidad = Unidades_orm::find($ti->unidad);
            if($ti->id_item == $item_id)
            {
                $cantidadTrasladada += $ti->cantidad * $unidad->factorConversion($item_id);//por factor conversion
            }
        }

        return $cantidadTrasladada;
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Traslados\Services\TrasladoFilters;
        return $queryFilter->apply($query, $campo);
    }

}

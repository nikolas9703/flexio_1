<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Consumos_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cons_consumos';
    
    
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
    //protected $dateFormat = 'U';
    
    
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['empresa_id', 'uuid_consumo', 'referencia', 'prefijo', 'numero', 'uuid_bodega', 'uuid_colaborador', 'uuid_centro', 'estado_id', 'created_by', 'comentarios'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public static function findByUuid($uuid){
        return self::where('uuid_consumo',hex2bin($uuid))->first();
    }
    
    public function consumos_items(){
        return $this->hasMany('Consumos_items_orm','consumo_id');
    }
    
    
    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }
    
    public function getUuidConsumoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
    
    public function getUpdatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
    
    
    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'uuid_bodega', 'uuid_bodega');
    }
    
    public function colaborador()
    {
        return $this->belongsTo('Colaboradores_orm', 'uuid_colaborador', 'uuid_colaborador');
    }
    
    public function centro()
    {
        return $this->belongsTo('Centros_orm', 'uuid_centro', 'uuid_centro');
    }
    
    public function estado()
    {
        return $this->belongsTo('Consumos_cat_orm', 'estado_id', 'id_cat');
    }
    
    public function items()
    {
        return  $this->belongsToMany('Items_orm', 'cons_consumos_items', 'consumo_id', 'item_id')
                ->withPivot("categoria_id", "unidad_id", "cantidad", "cuenta_id", "observacion");
    }
    
    public function comp_numeroDocumento()
    {
        return $this->prefijo.$this->numero;
    }
    
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }
    
    public function scopeDeFechaDesde($query, $fecha_desde)
    {
        return $query->whereDate("created_at", ">=", $fecha_desde);
    }
    
    public function scopeDeFechaHasta($query, $fecha_hasta)
    {
        return $query->whereDate("created_at", "<=", $fecha_hasta);
    }
    
    public function scopeDeColaborador($query, $colaborador)
    {
        return $query->where("uuid_colaborador", hex2bin($colaborador));
    }
    
    public function scopeDeEstado($query, $estado)
    {
        return $query->where("estado_id", $estado);
    }
    
    public function scopeDeReferencia($query, $referencia)
    {
        return $query->where("referencia", "like", "%$referencia%");
    }
    
    public function scopeDeNumero($query, $numero)
    {
        return $query->where("numero", "like", "%$numero%");
    }
    
    public function scopeDeCentro($query, $centro)
    {
        return $query->where("uuid_centro", hex2bin($centro));
    }
    
	
}
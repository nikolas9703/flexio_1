<?php
namespace Flexio\Modulo\Inventarios\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Categoria extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inv_categorias';


    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['nombre','descripcion','estado','empresa_id','created_by','depreciacion_meses','porcentaje_depreciacion','cuenta_id','depreciar'];


    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id','uuid_categoria'];

    protected $casts = [
        'depreciar' => 'boolean',
    ];


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_categoria' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }

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
    public function getUuidCategoriaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidActivoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidIngresoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidGastoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUuidVarianteAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function estadoReferencia()
    {
        return $this->belongsTo('Items_estados_orm', 'estado', 'id_cat');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return  $query->where("empresa_id", $empresa_id);
    }

    public function scopeConItems($query)
    {
        return  $query->has("items");
    }

    public static function findByUuid($uuid){
        return self::where('uuid_categoria',hex2bin($uuid))->first();
    }

    public  static function categoriasConItems($empresa_id)
    {
      $categoria_items = Items_categorias_orm::all();
      $ids = $categoria_items->unique('id_categoria');
      $id_categoria = $ids->pluck('id_categoria');
    return  $categorias = self::where(function($query) use($empresa_id, $id_categoria){
        $query->where('empresa_id','=',$empresa_id);
        $query->where('estado','=','1');
        $query->whereIn('id',$id_categoria->all());
      })->get();


    }


    public function items()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Items','inv_items_categorias','id_categoria','id_item')
        ->select(['inv_items.id','uuid_item','codigo','nombre','uuid_ingreso','uuid_venta','tipo_id','inv_items.cuentas']);
    }

    public function categorias_items()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Items','inv_items_categorias','id_categoria','id_item');
    }

    //se usa en
    //-contratos de alquiler
    //-cotizaciones de alquiler
    public function items_contratos_alquiler()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Items','inv_items_categorias','id_categoria','id_item')->select(['inv_items.id','uuid_item','codigo','nombre','tipo_id']);
    }
    public function items_solo_alquiler()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Items','inv_items_categorias','id_categoria','id_item')->select(['inv_items.id','uuid_item','codigo','nombre','tipo_id','item_alquiler'])->where('item_alquiler','=',1);
    }
}

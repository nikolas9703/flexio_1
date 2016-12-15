<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Categorias_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inv_categorias';


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
    protected $fillable = ['nombre'];


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



    public function items(){
      return $this->belongsToMany('Items_orm','inv_items_categorias','id_categoria','id_item')->select(['inv_items.id','uuid_item','codigo','nombre','uuid_ingreso','uuid_venta']);
    }

    function present(){
        return new \Flexio\Modulo\Inventarios\Presenter\ConfiguracionInventarioPresenter($this);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ajustes_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'aju_ajustes';


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
        $this->codeIgniter->load->model("ajustes/Ajustes_items_orm");
        $this->codeIgniter->load->model("centros/Centros_orm");
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
    public function getUuidAjusteAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('empresa_id', $empresa_id);
    }

    public function scopeConItem($query, $item_id)
    {
        return $query->whereHas('ajuste_items', function($q) use($item_id){
            $q->where("lines_items.item_id", $item_id);
        });
    }

    public function scopeDeFecha($query, $fecha)
    {
        return  $query->whereDate("created_at", "=", $fecha);
    }

    public function scopeDeCentro($query, $centro)
    {
        return $query->where("centro_id", $centro);
    }

    public function scopeDeBodega($query, $uuid_bodega)
    {
        return $query->where("uuid_bodega", hex2bin($uuid_bodega));
    }

    public function scopeDeTipoAjuste($query, $tipo_ajuste_id)
    {
        return $query->where("tipo_ajuste_id", $tipo_ajuste_id);
    }

    public function scopeDeNumeroAjuste($query, $numero_ajuste)
    {
        return $query->where("numero", "like","%$numero_ajuste%");
    }

    public function scopeDeEstado($query, $estado_id)
    {
        return $query->where("estado_id", $estado_id);
    }

    public function scopeDeItemsCategorias($query, $categorias)
    {
        return $query->whereHas("ajuste_items", function($q) use ($categorias){
            $q->whereHas("item", function($q2) use ($categorias){
                $q2->whereHas("item_categorias", function($q3) use ($categorias){
                    $q3->whereIn("id_categoria", $categorias);
                });
            });
        });
    }

    public function scopeDeItemsNumero($query, $numero)
    {
        return $query->whereHas("ajuste_items", function($q) use ($numero){
            $q->whereHas("item", function($q2) use ($numero){
                $q2->where("codigo", "like", "%$numero%");
            });
        });
    }

    public function items()
    {
        return  $this->belongsToMany('Items_orm', 'aju_ajustes_items', 'ajuste_id', 'item_id')
                ->withPivot("cantidad", "cantidad_disponible", "precio_unitario");
    }

    public function bodega()
    {
        return $this->belongsTo('Bodegas_orm', 'uuid_bodega', 'uuid_bodega');
    }

    public function centros()
    {
        return $this->belongsTo('Centros_orm', 'uuid_centro', 'uuid_centro');

    }

    public function ajuste_items()
    {
        return $this->hasMany('Flexio\Modulo\Cotizaciones\Models\LineItem', 'tipoable_id', 'id')
        ->where('tipoable_type', 'Flexio\\Modulo\\Ajustes\\Models\\Ajustes');
    }

    public function tipo_ajuste()
    {
        return $this->belongsTo('Ajustes_cat_orm', 'tipo_ajuste_id', 'id_cat');
    }

    public function estado()
    {
        return $this->belongsTo('Ajustes_cat_orm', 'estado_id', 'id_cat');
    }

    public function usuario()
    {
        return $this->belongsTo('Usuario_orm', 'created_by', 'id');
    }

    public static function findByUuid($uuid){
        return self::where('uuid_ajuste',hex2bin($uuid))->first();
    }


    public function cantidadAjustadaItem($item_id)
    {
        $cantidadAjustada   = 0;

        foreach($this->ajuste_items as $ai)
        {
            if($ai->item_id == $item_id)
            {
                $cantidadAjustada += $ai->cantidad;
            }
        }

        return $cantidadAjustada;
    }

    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }

    public function comp_numeroDocumento()
    {
        $numeroDocumento    = $this->numero;
        $prefijo            = "AJU";

        return $prefijo.$numeroDocumento;
    }

}

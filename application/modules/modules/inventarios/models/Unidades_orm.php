<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Unidades_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inv_unidades';


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
    public function getUuidUnidadAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function item_unidades()
    {
        return $this->hasMany('Items_unidades_orm', 'id_unidad', 'id');
    }

    public static function findByUuid($uuid){
        return self::where('uuid_unidad',hex2bin($uuid))->first();
    }

    public function estadoReferencia()
    {
        return $this->belongsTo('Items_estados_orm', 'estado', 'id_cat');
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return  $query->where("empresa_id", $empresa_id);
    }

    public function factorConversion($item_id)
    {
        $factor_conversion = 1;

        foreach($this->item_unidades as $iu)
        {
            if($iu->id_item == $item_id)
            {
                $factor_conversion = $iu->factor_conversion;
            }
        }

        return $factor_conversion;
    }

    function present(){
        return new \Flexio\Modulo\Inventarios\Presenter\ConfiguracionInventarioPresenter($this);
    }
}

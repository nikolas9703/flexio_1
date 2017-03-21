<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Centros_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cen_centros';


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
    protected $dateFormat = 'U';


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
    public function getUuidCentroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    /**
     * Retorna listado de centros de tipo Gasto: Se usa en el modulo de Comisiones
     */
    public static function lista_por_empresa($empresa_id=NULL){

    	if($empresa_id==NULL){
    		return false;
    	}

    	return self::where('empresa_id', $empresa_id)->get()->toArray();
    }

    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeActiva($query)
    {
        return $query->where("estado", "Activo");
    }

    public function scopeDeMasJuventud($query, $empresa_id)
    {
        $ids = Centros_orm::deEmpresa($empresa_id)->lists('padre_id');

        return $query->whereNotIn("id", $ids);
    }

    public static function centros($empresa_id=NULL, $centro_id=NULL){

    	if($empresa_id==NULL){
    		return false;
    	}

    	return self::where('empresa_id', $empresa_id)
                ->where('id', $centro_id)
                ->get()->toArray();

    }


}

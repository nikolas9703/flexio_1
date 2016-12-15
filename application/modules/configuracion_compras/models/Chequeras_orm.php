<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Chequeras_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'che_chequeras';


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
    public function getUuidChequeraAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getUuidActivoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getUuidIngresoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getUuidGastoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function getUuidVarianteAttribute($value) {
        return strtoupper(bin2hex($value));
    }


    public function scopeDeEmpresa($query, $empresa_id) {
        return  $query->where("empresa_id", $empresa_id);
    }

    public function scopeConItems($query) {
        return  $query->has("items");
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_chequera',hex2bin($uuid))->first();
    }

    public  static function chequerasConItems($empresa_id) {
      $chequera_items = Items_chequeras_orm::all();
      $ids = $chequera_items->unique('id_chequera');
      $id_chequera = $ids->pluck('id_chequera');
    return  $chequeras = self::where(function($query) use($empresa_id, $id_chequera){
        $query->where('empresa_id','=',$empresa_id);
        $query->where('estado','=','1');
        $query->whereIn('id',$id_chequera->all());
      })->get();


    }
    public static function exportar($clause = array()) {

        return self::whereIn('uuid_chequera', $clause)->get();
    }

    function present() {
        return new \Flexio\Modulo\ConfiguracionCompras\Presenter\ConfiguracionCompraPresenter($this);
    }

}

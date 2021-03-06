<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Tipos_proveedores_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'pro_tipos';


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
    protected $fillable = ['nombre','descripcion','estado'];


    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id', 'uuid_tipo'];

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
    public function getUuidCategoriaAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    public function scopeDeEmpresa($query, $empresa_id) {
        return  $query->where("id_empresa", $empresa_id);
    }
    public static function findByUuid($uuid) {
        return self::where('uuid_tipo',hex2bin($uuid))->first();
    }
    public static function exportar($clause = array()) {

        return self::whereIn('uuid_tipo', $clause)->get();
    }
    public function estadoReferencia() {
        return $this->belongsTo('Items_estados_orm', 'estado', 'id_cat');
    }

    public function estado(){
  		return $this->hasOne('Items_estados_orm', 'id_cat', 'estado');
  	}

    function scopeEstadoActivo($query){
  		return $query->whereHas('estado', function ($query) {
  			$query->where('valor', '=', 'activo')->where('etiqueta', 'LIKE', '%Activo%');
  		});
  	}
    function present() {
        return new \Flexio\Modulo\ConfiguracionCompras\Presenter\ConfiguracionCompraPresenter($this);
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Precios_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inv_precios';


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
    protected $fillable = ['nombre', 'descripcion', 'estado', 'principal'];


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
    public function getUuidPrecioAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function scopeActivo($query) {
        return $query->where('estado', 1);
    }

    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where('empresa_id', $empresa_id);
    }
    public function scopeDeTipoVenta($query) {
        return $query->where('tipo_precio', 'venta');
    }
    public function scopeDeTipoAlquiler($query) {
        return $query->where('tipo_precio', 'alquiler');
    }

    public function items() {
        return $this->belongsToMany('Items_orm', 'inv_items_precios', 'id_precio', 'id_item');
    }

    public function precio_items() {
        return $this->hasMany('Items_precios_orm', 'id_precio', 'id');
    }

    public function estadoReferencia() {
        return $this->belongsTo('Items_estados_orm', 'estado', 'id_cat');
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_precio',hex2bin($uuid))->first();
    }

    public static function asignar_precio_principal($clause = array(), $tipo_precio) {

        $precio = self::where('principal','=', '1')->where('tipo_precio','=', $tipo_precio)->get();
        if(count($precio)>0){
            foreach ($precio as $count){
                if($count->id != $clause['id']){
                    $count->principal = 0;
                    $principal = self::find($clause['id']);
                    $principal->principal = 1;
                    $principal->save();
                }else{
                    $count->principal = 1;
                }
                $count->save();
            }
        }else{
            $principal = self::find($clause['id']);
            $principal->principal = 1;
            $principal->save();
        }
        return true;
    }

    function present(){
        return new \Flexio\Modulo\Inventarios\Presenter\ConfiguracionInventarioPresenter($this);
    }

}

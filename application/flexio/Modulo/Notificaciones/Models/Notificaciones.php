<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 1/11/16
 * Time: 4:46 PM
 */

namespace Flexio\Modulo\Notificaciones\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Notificaciones extends Model
{
    protected $table = 'not_notificaciones';
    protected $fillable = ['modulo','transaccion','roles','usuarios','categoria_items', 'operador', 'monto', 'sin_transaccion', 'tipo_notificacion', 'estado', 'mensaje', 'empresa_id'];
    protected $guarded = ['id','uuid_notificacion'];
    //public $timestamps      = false;
    protected $casts = [
        'roles' => 'array',
        'usuarios' => 'array',
        'tipo_notificacion' => 'array',
    ];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_notificacion' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
    public function getUuidNotificacionAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function estados(){

        return $this->belongsTo('Flexio\Modulo\Notificaciones\Models\NotificacionesCatalog', 'estado', 'valor');

    }
    public function tipo_notificaciones(){

        return $this->belongsTo('Flexio\Modulo\Notificaciones\Models\NotificacionesCatalog', 'tipo_notificacion', 'valor');

    }

    public function operadores(){

        return $this->belongsTo('Flexio\Modulo\Notificaciones\Models\NotificacionesCatalog', 'operador', 'valor');

    }
    public function modulos(){

        return $this->belongsTo('Flexio\Modulo\Modulos\Models\Modulos', 'modulo', 'id');

    }
    public function transacciones(){

        return $this->belongsTo('Flexio\Modulo\Pedidos\Models\PedidosCat', 'transaccion', 'id_cat');
    } 
    public function categorias(){
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Categoria', 'categoria_items', 'id');
    }
    public function getUsuarios(){
        return $this->belongsToMany('Flexio\Modulo\Usuarios\Models\Usuarios','usuarios','id', 'usuarios');
    }
    public function present() {
        return new \Flexio\Modulo\Notificaciones\Presenter\NotificacionesPresenter($this);
    }
}
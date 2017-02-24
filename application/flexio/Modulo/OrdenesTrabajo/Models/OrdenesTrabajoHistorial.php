<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 22/12/16
 * Time: 3:34 PM
 */

namespace Flexio\Modulo\OrdenesTrabajo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class OrdenesTrabajoHistorial extends Model
{
    protected $table = 'odt_historial';
    protected $fillable = ['codigo','descripcion','empresa_id','usuario_id','odt_id','antes','despues','tipo','codigo_cuenta'];
    protected $guarded = ['id','uuid_historial'];
    protected $appends = ['nombre_usuario','hace_tiempo','fecha_creacion','hora'];

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_historial' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    public function getUuidHistorialAttribute($value){
        return strtoupper(bin2hex($value));
    }

    public function getNombreUsuarioAttribute(){
        if(is_null($this->usuario)){
            return "";
        }
        return $this->usuario->nombre ." ".$this->usuario->apellido;
    }

    public function getHaceTiempoAttribute(){
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->diffForHumans();
    }
    public function getFechaCreacionAttribute() {
        setlocale(LC_TIME, 'es');
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->formatLocalized('%d de %B, %Y');
    }
    public function getHoraAttribute() {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('h:i a');
    }
    public function usuario(){
        return $this->belongsTo(Usuarios::class,'usuario_id');
    }


}
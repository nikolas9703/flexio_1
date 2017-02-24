<?php
namespace Flexio\Modulo\Cajas\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class Cajas extends Model
{
    protected $table    = 'ca_cajas';
    protected $fillable = ['uuid_caja', 'empresa_id', 'centro_id', 'responsable_id', 'cuenta_id', 'estado_id', 'nombre', 'numero', 'saldo', 'limite', 'creado_por'];
    protected $guarded	= ['id'];
    protected $appends      = ['icono','codigo','enlace'];
    protected static $ci;


    public function getUuidCajaAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    public function centro(){
    	return $this->belongsTo('Centros_orm', 'centro_id', 'id');
    }

    public function responsable(){
    	return $this->belongsTo('Usuario_orm', 'responsable_id', 'id');
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function cajas_asignadas() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("cajas/ver/".$this->uuid_caja);
    }
    public function getIconoAttribute(){
        return 'fa fa-shopping-cart';
    }
    public function getCodigoAttribute(){
        return $this->numero;
    }
    function documentos(){
    	return $this->morphMany(Documentos::class, 'documentable');
    }
    public function getModuloAttribute(){
        return 'Caja';
    }
    function responsable2() {
        return $this->belongsTo(Usuarios::class, 'responsable_id');
    }
}

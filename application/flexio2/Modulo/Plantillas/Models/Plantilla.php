<?php
namespace Flexio\Modulo\Plantillas\Models;
use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;

class Plantilla extends Model
{
    protected $table    = 'plnt_plantillas';
    protected $fillable	= ['tipo_id', 'codigo', 'nombre', 'estado', 'archivo_nombre', 'archivo_ruta', 'create_at'];
    protected $guarded  = ['id'];
    protected $appends      = ['icono','enlace'];
    public function tipo(){
    	return $this->hasOne(TiposPlantilla::class, 'id', 'tipo_id');
    }

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("plantillas/ver/".$this->uuid_proveedor);
    }
    public function getIconoAttribute(){
        return 'fa fa-users';
    }
}
 
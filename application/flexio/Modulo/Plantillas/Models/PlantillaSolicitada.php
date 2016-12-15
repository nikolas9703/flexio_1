<?php

namespace Flexio\Modulo\Plantillas\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use  Flexio\Modulo\Plantillas\Models\Plantilla as Plantilla;
use  Flexio\Modulo\Plantillas\Models\PlantillaCatalogo as PlantillaCatalogo;
use  Flexio\Modulo\Plantillas\Models\TiposPlantilla as TiposPlantilla;
use Flexio\Modulo\Comentario\Models\Comentario;

class PlantillaSolicitada extends Model { 

    protected $table    = 'plnt_plantillas_solicitadas';
    protected $fillable	= ['plantilla_id', 'colaborador_id', 'prefijo_id', 'estado_id', 'estado_id', 'acreedor', 'plantilla', 'empresa_id', 'creado_por', 'uuid_plantilla', 'codigo', 'firmado_por'];
    protected $appends     = ['icono','enlace'];
    protected $guarded  = ['id']; 

    public function nombre_plantilla() {
        return $this->hasOne(Plantilla::class, 'id', 'plantilla_id');
    }

    public function estado() {
        return $this->hasOne(PlantillaCatalogo::class, 'id', 'estado_id');
    }
    public function getUuidPlantillaAttribute($value){
        return bin2hex($value);
    }
    public function nombre_plantillas_solitadas(){
     //   return $this->hasOne(TiposPlantilla::class, 'id', );
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
        return base_url("plantillas/ver/".$this->uuid_plantilla);
    }
    public function getIconoAttribute(){
        return 'fa fa-users';
    }

}

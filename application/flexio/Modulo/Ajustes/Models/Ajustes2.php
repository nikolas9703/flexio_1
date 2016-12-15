<?php
namespace Flexio\Modulo\Ajustes\Models;

use \Illuminate\Database\Eloquent\Model as Model;

//utilities
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;

class Ajustes2 extends Model
{
protected $prefijo      = 'AJS';
protected $table        = 'aju_ajustes';
protected $fillable     = ['uuid_ajuste', 'uuid_bodega', 'numero', 'descripcion', 'tipo_ajuste_id', 'estado_id', 'created_by', 'comentarios', 'total', 'empresa_id', 'uuid_centro', 'razon_id'];
protected $guarded      = ['id'];
public $timestamps      = true;
protected $appends      = ['icono','codigo','enlace'];

    //GETS
    public function getUuidAjusteAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    //GETS
    public function getUuidBodegaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    //GETS
    public function getUuidCentroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getIconoAttribute(){
        return 'fa fa-cubes';
    }
    public function getCodigoAttribute(){
        $numero = $this->prefijo.sprintf('%08d', $this->numero);
        return $numero;
    }
    public function getEnlaceAttribute()
    {
        return base_url("ajustes/ver/".$this->uuid_ajuste);
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

}
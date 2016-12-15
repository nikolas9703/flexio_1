<?php
namespace Flexio\Modulo\Inventarios\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;

class Items2 extends Model
{
protected $table        = 'inv_items';
protected $fillable     = ['uuid_item','codigo','nombre', 'descripcion', 'fecha_creacion', 'estado', 'empresa_id', 'creado_por', 'tipo_id', 'uuid_activo', 'uuid_ingreso', 'uuid_gasto', 'uuid_variante', 'uuid_compra', 'uuid_venta'];
protected $guarded      = ['id'];
protected $appends      = ['icono','enlace'];

public $timestamps      = false;

    public function getUuidItemAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidActivoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidIngresoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidGastoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidVarianteAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidCompraAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidVentaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    //Mostrar Comentarios
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    //functiones para el landing_page
    public function getEnlaceAttribute()
    {
        return base_url("inventarios/ver/".$this->uuid_item);
    }
    public function getIconoAttribute(){
        return 'fa fa-cubes';
    }

}
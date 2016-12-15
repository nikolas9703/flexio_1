<?php
namespace Flexio\Modulo\Traslados\Models;

use \Illuminate\Database\Eloquent\Model as Model;

//utilities
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
class Traslados2 extends Model
{
    protected $prefijo = 'TRAS';
    protected $table = 'tras_traslados';
    protected $fillable = ['*'];
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $appends      = ['icono','codigo','enlace'];


    //GETS
    public function getUuidTrasladoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidLugarAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidLugarAnteriorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidProveedorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidPedidoAttribute($value)
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
        return base_url("traslados/ver/".$this->uuid_traslado);
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

}
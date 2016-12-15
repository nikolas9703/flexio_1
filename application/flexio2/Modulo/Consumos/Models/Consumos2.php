<?php
namespace Flexio\Modulo\Consumos\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
//utilities
use Carbon\Carbon as Carbon;

class Consumos2 extends Model
{
    protected $table = 'cons_consumos';
    protected $fillable = ['uuid_consumo', 'referencia', 'prefijo', 'numero', 'uuid_bodega', 'uuid_colaborador', 'uuid_centro', 'estado_id', 'created_by', 'empresa_id', 'comentarios'];
    protected $guarded = ['id'];
    public $timestamps = true;
    private $prefijo = "CONS";
    protected $appends      = ['icono','codigo','enlace'];

    public function getUuidConsumoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidBodegaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidColaboradorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
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
        return base_url("consumos/ver/".$this->uuid_consumo);
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

}
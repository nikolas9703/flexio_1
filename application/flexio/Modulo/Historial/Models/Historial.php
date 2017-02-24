<?php
namespace Flexio\Modulo\Historial\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Usuarios\Models\Usuarios;


class Historial extends Model
{
	protected $table = 'historiables';
	protected $fillable = ['descripcion','historiable_id','historiable_type', 'usuario_id','tipo','antes','despues','titulo'];
	protected $guarded = ['id','empresa_id','uuid_historial'];
    protected $appends = ['nombre_usuario','hace_tiempo','fecha_creacion','hora'];
    protected $casts =['antes' => 'array','despues'=>'array'];

	public function __construct(array $attributes = array()){
	    $session = new FlexioSession;
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_historial' => Capsule::raw("ORDER_UUID(uuid())"),'empresa_id' => $session->empresaId())), true);
        parent::__construct($attributes);
    }


     public function getUuidHistorialAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getNombreUsuarioAttribute() {

		if(is_null($this->usuarios)){
			return "";
		}
		return $this->usuarios->nombre ." ".$this->usuarios->apellido;
  }

  public function getHaceTiempoAttribute(){
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->diffForHumans();
    }

    public function getFechaCreacionAttribute() {
		return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->formatLocalized('%d de %B');
	}

	public function getHoraAttribute() {
		return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('h:i a');
	}

    public function historiable() {
        return $this->morphTo();
    }

    function usuarios() {
            return $this->belongsTo(Usuarios::class,'usuario_id');
    }

    function empresas() {
        return $this->belongsTo(Empresa::class,'empresa_id');
    }

}    
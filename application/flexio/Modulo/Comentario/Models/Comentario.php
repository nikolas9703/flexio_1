<?php
namespace Flexio\Modulo\Comentario\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Usuarios\Models\Usuarios;


class Comentario extends Model
{
	protected $table = 'comentarios';
	protected $fillable = ['comentario','comentable_id','comentable_type', 'usuario_id','created_at'];
	protected $guarded = ['id','empresa_id'];
  protected $appends = ['nombre_usuario','cuanto_tiempo','fecha_creacion','hora'];


	public function __construct(array $attributes = array()){
		  $session = new FlexioSession;
      $this->setRawAttributes(array_merge($this->attributes, array('empresa_id' => $session->empresaId())), true);
      parent::__construct($attributes);
    }

  public function getCreatedAtAttribute($date) {
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m-d-Y H:i:s');
  }

  public function getNombreUsuarioAttribute() {

		if(is_null($this->usuarios)){
			return "";
		}
		return $this->usuarios->nombre ." ".$this->usuarios->apellido;
  }

	public function getCuantoTiempoAttribute() {
		return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->diffForHumans();
	}

	public function getFechaCreacionAttribute() {
		return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->formatLocalized('%d de %B');
	}

	public function getHoraAttribute() {
		return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('h:i a');
	}

  public function comentable() {
    return $this->morphTo();
  }

  function usuarios() {
		return $this->belongsTo(Usuarios::class,'usuario_id');
  }

  function empresas() {
    return $this->belongsTo(Empresa::class,'empresa_id');
  }


}

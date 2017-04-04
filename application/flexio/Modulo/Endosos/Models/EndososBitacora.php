<?php 

namespace Flexio\Modulo\Endosos\Models;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FlexioSession;

use Illuminate\Database\Eloquent\Model as Model;

class EndososBitacora extends Model
{
	
	protected $table        = 'end_endosos_bitacora';
    protected $fillable     = ['comentario', 'comentable_id', 'comentable_type', 'usuario_id', 'created_at', 'updated_at', 'empresa_id'];
    protected $guarded      = ['id'];
	
	public function __construct(array $attributes = array()){
		$session = new FlexioSession;
		$this->setRawAttributes(array_merge($this->attributes, array('empresa_id' => $session->empresaId())), true);
		parent::__construct($attributes);
    }
	
	public function getCuantoTiempo($created_at){
		return Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->diffForHumans();
	}

	public function getFechaCreacion($created_at){
		return Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->formatLocalized('%d de %B');
	}

	public function getHora($created_at){	
		return Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->format('h:i a');
	}
	
	public function usuario(){
		return $this->hasOne(Usuarios::class, 'id', 'usuario_id');
	}
	
}
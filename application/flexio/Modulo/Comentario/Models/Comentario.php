<?php

namespace Flexio\Modulo\Comentario\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Usuarios\Models\Usuarios;

class Comentario extends Model
{
    protected $table = 'comentarios';
    protected $fillable = ['comentario', 'comentable_id', 'comentable_type', 'usuario_id', 'created_at','centro_contable_id'];
    protected $guarded = ['id', 'empresa_id'];
    protected $appends = ['nombre_usuario', 'cuanto_tiempo', 'fecha_creacion', 'hora'];

    public function __construct(array $attributes = array())
    {
        $session = new FlexioSession();
        $this->setRawAttributes(array_merge($this->attributes, array('empresa_id' => $session->empresaId())), true);
        parent::__construct($attributes);
    }

	public static function boot()
    {
        parent::boot();
        static::creating(function($comentario){
			if(count($comentario->comentable))
			{
				$comentable = $comentario->comentable;
				if (!empty($comentable->centro_id)) {
					$comentario->centro_contable_id = $comentable->centro_id;
		        } elseif (!empty($comentable->centro_contable_id)) {
		            $comentario->centro_contable_id = $comentable->centro_contable_id;
		        } else {
					$comentario->centro_contable_id = count($comentable->centro_contable) ? $comentable->centro_contable->id : 0;
		        }
			}
            return $comentario;
        });
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m-d-Y H:i:s');
    }

    public function getNombreUsuarioAttribute()
    {
        if (is_null($this->usuarios)) {
            return '';
        }

        return $this->usuarios->nombre.' '.$this->usuarios->apellido;
    }

    public function getCuantoTiempoAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->diffForHumans();
    }

    public function getFechaCreacionAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->formatLocalized('%d de %B');
    }

    public function getHoraAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('h:i a');
    }

    public function comentable()
    {
        return $this->morphTo();
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function empresas()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function scopeDeFiltro($query, $model)
    {
        $queryFilter = new \Flexio\Modulo\Comentario\Services\ComentarioFilters();

        return $queryFilter->apply($query, $model);
    }
}

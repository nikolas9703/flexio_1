<?php

namespace Flexio\Modulo\Politicas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Roles\Models\Roles;
use Flexio\Modulo\Modulos\Models\Modulos;
use Flexio\Modulo\Inventarios\Models\Categoria;

use Flexio\Modulo\Empresa\Models\Empresa;

class Politicas extends Model
{
    protected $table = 'ptr_transacciones';
    protected $fillable = ['nombre', 'politica_estado', 'modulo', 'role_id', 'transaccion_id', 'monto_limite', 'estado_id', 'usuario_id'];
    protected $guarded = ['id', 'usuario_id'];

    public function __construct(array $attributes = array())
    {
        $session = new FlexioSession();
        $this->setRawAttributes(array_merge($this->attributes, array('usuario_id' => $session->usuarioId(), 'empresa_id' => $session->empresaId())), true);
        parent::__construct($attributes);
    }

    public function rol()
    {
        return $this->hasOne(Roles::class, 'id', 'role_id');
    }

    public function modulo()
    {
        return $this->hasOne(Modulos::class, 'id', 'modulo_id');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }

    public function categoria()
    {
        return $this->hasOne(Categoria::class, 'id', 'transaccion_id');
    }

    //politica estado del modulo
    public function estado_politica()
    {
        return $this->belongsTo(PoliticasCatalogo::class, 'politica_estado');
    }

    //relacion con la categoria del items
    public function categorias()
    {
    	return $this->belongsToMany(Categoria::class, 'ptr_transacciones_categoria', 'transaccion_id', 'categoria_id');
    }

    public function transaccion()
    {
        return $this->hasOne(PoliticasCatalogo::class, 'id', 'transaccion_id');
    }

    public function scopeDeEmpresa($query, $clause)
    {
        return $query->where('empresa_id', '=', $clause['empresa_id']);
    }
	
}

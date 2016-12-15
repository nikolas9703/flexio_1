<?php
namespace Flexio\Modulo\Politicas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Roles\Models\Roles;
use Flexio\Modulo\Modulos\Models\Modulos;
use Flexio\Modulo\Inventarios\Models\Categoria;
use Flexio\Modulo\Politicas\Models\PoliticasCatalogo;
use Flexio\Modulo\Politicas\Models\PoliticasCategoria;
use Flexio\Modulo\Empresa\Models\Empresa;



class Politicas extends Model
{
	protected $table = 'ptr_transacciones';
	protected $fillable = ['nombre','politica_estado','modulo','role_id','transaccion_id','monto_limite','estado_id','usuario_id'];
	protected $guarded = ['id','usuario_id'];

 	public function __construct(array $attributes = array()){
		  $session = new FlexioSession;
                  $this->setRawAttributes(array_merge($this->attributes, array('usuario_id' => $session->usuarioId(),'empresa_id'=>$session->empresaId())), true);
                  parent::__construct($attributes);
         }

        public function rol() {
            return $this->hasOne(Roles::Class, 'id','role_id');
        }

         public function modulo() {
            return $this->hasOne(Modulos::Class, 'id','modulo_id');
        }
           public function empresa() {
            return $this->hasOne(Empresa::Class, 'id','empresa_id');
        }
         public function categoria() {
          return $this->hasOne(Categoria::Class, 'id','transaccion_id');
        }

        //politica estado del modulo
        public function estado_politica() {
          return $this->belongsTo(PoliticasCatalogo::Class, 'politica_estado');
        }

        //relacion con la categoria del items
         public function categorias() {
              return $this->belongsToMany(Categoria::Class, 'ptr_transacciones_categoria', 'transaccion_id', 'categoria_id');
         }

         public function transaccion() {
          return $this->hasOne(PoliticasCatalogo::Class, 'id','transaccion_id');
        }
         public function scopeDeEmpresa($query, $clause){

                return $query->where('empresa_id','=',$clause['empresa_id']);
        }

 }

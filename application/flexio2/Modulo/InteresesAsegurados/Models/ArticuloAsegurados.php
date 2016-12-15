<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Documentos\Models\Documentos;

class ArticuloAsegurados extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'int_articulo';
        
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [        
        'empresa_id',
        'numero',
        'nombre',
        'clase_equipo',
        'marca',
        'modelo',
        'anio',
        'numero_serie',
        'id_condicion',
        'valor',
        'observaciones',
        'estado',
        'tipo_id'
    ];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];
          
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("id_empresa", $empresa_id);
    }
    
    public function scopeDeEmpresaArticulo($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    
    public function scopeDeNombre($query, $nombre) {
        return $query->where("nombre", "like", "%$nombre%");
    }
    
    public function interesesAsegurados() {
		return $this->hasMany(InteresesAsegurados::class, 'interesestable_id');
    }
    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }


}
<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo;
use Flexio\Modulo\Proveedores\Models\Proveedores;

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
        'uuid_articulo',    
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
        'tipo_id',
        'created_at',
        'updated_at'
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
	
	public function datosCondicion() {
        return $this->hasOne(SegCatalogo::class, 'id', 'id_condicion');
    }
	
	public function datosAcreedor() {
        return $this->hasOne(Proveedores::class, 'id', 'acreedor');
    }

    public static function listar_articulo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        $articulo = self::join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_articulo.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.interesestable_type", '1')->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
            if((isset($clause['empresa_id'])) && (!empty($clause['empresa_id']))) $query->where('int_intereses_asegurados.empresa_id','=' , $clause['empresa_id']);
            if((isset($clause['detalle_unico'])) && (!empty($clause['detalle_unico']))) $query->where('int_intereses_asegurados_detalles.detalle_unico','=' , $clause['detalle_unico']);
            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
        
        if($sidx!=NULL && $sord!=NULL){ $articulo->orderBy($sidx, $sord); }

        return $articulo->get();
    }

}
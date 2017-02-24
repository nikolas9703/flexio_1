<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Documentos\Models\Documentos;

class AereoAsegurados extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'int_casco_aereo';
    
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [        
        'empresa_id',
        'serie',
        'marca',
        'modelo',
        'matricula',
        'valor',
        'pasajeros',
        'tripulacion',
        'observaciones',
        'numero',
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
    
    public function scopeDeEmpresaAereo($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    
    public function scopeConTipo($query) {
        return $query->has("tipo");
    }
    
    public function scopeDeNombre($query, $nombre) {
        return $query->where("nombre", "like", "%$nombre%");
    }
    
    public function scopeDeTipo($query, $tipo) {
        return $query->where("tipo_id", $tipo);
    }
    
    public function scopeDeTelefono($query, $telefono) {
        return $query->where("telefono", "like", "%$telefono%");
    }
    
    public function tipo() {
        return $this->belongsTo(InteresesAsegurados_cat::class, "tipo_id", "id_cat");
    }
    public function interesesAsegurados() {
		return $this->hasMany(InteresesAsegurados::class, 'interesestable_id');
    }
    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public static function listar_aereo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        $aereo = self::join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_casco_aereo.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.interesestable_type", '3')->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
            if((isset($clause['empresa_id'])) && (!empty($clause['empresa_id']))) $query->where('int_intereses_asegurados.empresa_id','=' , $clause['empresa_id']);
            if((isset($clause['detalle_unico'])) && (!empty($clause['detalle_unico']))) $query->where('int_intereses_asegurados_detalles.detalle_unico','=' , $clause['detalle_unico']);
            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
        
        if($sidx!=NULL && $sord!=NULL){ $aereo->orderBy($sidx, $sord); }

        return $aereo->get();
    }


}
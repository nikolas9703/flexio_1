<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Proveedores\Models\Proveedores;
use Flexio\Modulo\Modulos\Models\Catalogos;

class MaritimoAsegurados extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'int_casco_maritimo';
        
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [  
        'uuid_casco_maritimo', //TODO
        'empresa_id',
        'numero',
        'serie',
        'nombre_embarcacion',
        'tipo',
        'marca',
        'valor',
        'pasajeros',
        'acreedor',
        'porcentaje_acreedor',
        'observaciones',
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
    
    public function scopeDeEmpresaMaritimo($query, $empresa_id) {
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
     public function datosAcreedor() {
        return $this->hasOne(Proveedores::class, 'id', 'acreedor');
    }
	public function tipotrans() {
        return $this->belongsTo(Catalogos::class, "tipo", "id_cat");
    }

    public static function listar_maritimo_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        $maritimo = self::select("int_intereses_asegurados.*","int_intereses_asegurados_detalles.*","int_casco_maritimo.*","pro_proveedores.nombre","int_intereses_asegurados_detalles.id as id_det")->join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_casco_maritimo.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->leftjoin("pro_proveedores","pro_proveedores.id","=","int_casco_maritimo.acreedor")->where("int_intereses_asegurados.interesestable_type", '4')->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
            if((isset($clause['empresa_id'])) && (!empty($clause['empresa_id']))) $query->where('int_intereses_asegurados.empresa_id','=' , $clause['empresa_id']);
            if((isset($clause['detalle_unico'])) && (!empty($clause['detalle_unico']))) $query->where('int_intereses_asegurados_detalles.detalle_unico','=' , $clause['detalle_unico']);
            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
        
        if($sidx!=NULL && $sord!=NULL){ $maritimo->orderBy($sidx, $sord); }

        return $maritimo->get();
    }


}
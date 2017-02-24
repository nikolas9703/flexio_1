<?php

namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Flexio\Modulo\SegInteresesAsegurados\Models\SegInteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Proveedores\Models\Proveedores;

class ProyectoAsegurados extends Model {

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'int_proyecto_actividad';

    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [
        'uuid_proyecto', //TODO
        'empresa_id',
        'numero',
        'nombre_proyecto',
        'no_orden',
        'contratista',
        'representante_legal',
        'duracion',
        'fecha',
        'monto',
        'monto_afianzado',
        'acreedor',
        'porcentaje_acreedor',
        'ubicacion',
        'observaciones',
        'updated_at',
        'created_at',
        'estado',
        'tipo_id',
        'tipo_propuesta',
        'validez_fianza_pr',
        'tipo_fianza',
        'fecha_concurso',
        'acreedor_opcional',
        'validez_fianza_opcional',
        'asignado_acreedor',
        'tipo_propuesta_opcional',
    ];

    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function findByUuid($uuid) {
        return self::where('uuid_intereses_asegurados', hex2bin($uuid))->first();
    }

    //transformaciones para GET
    public function getUuidInteresAseguradoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    //transformaciones para SET
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("id_empresa", $empresa_id);
    }

    public function scopeDeEmpresaProyecto($query, $empresa_id) {
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

    public function tipodeFianza() {
        return $this->hasOne(SegInteresesAsegurados::class, 'valor', 'tipo_fianza');
    }
     public function tipodePropuesta() {
        return $this->hasOne(SegInteresesAsegurados::class, 'id_cat', 'tipo_propuesta');
    }
     public function tipodeFianzapr() {
        return $this->hasOne(SegInteresesAsegurados::class, 'id_cat', 'validez_fianza_pr');
    }
     public function datosAcreedor() {
        return $this->hasOne(Proveedores::class, 'id', 'acreedor');
    }

    public function listar_proyecto_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
         $proyecto = self::join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_proyecto_actividad.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.interesestable_type", '6')->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
            if((isset($clause['empresa_id'])) && (!empty($clause['empresa_id']))) $query->where('int_intereses_asegurados.empresa_id','=' , $clause['empresa_id']);
            if((isset($clause['detalle_unico'])) && (!empty($clause['detalle_unico']))) $query->where('int_intereses_asegurados_detalles.detalle_unico','=' , $clause['detalle_unico']);
            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
        
        if($sidx!=NULL && $sord!=NULL){ $proyecto->orderBy($sidx, $sord); }

        return $proyecto->get();
    }

}

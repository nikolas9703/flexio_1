<?php

namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\Proveedores\Models\Proveedores;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Documentos\Models\Documentos;

class UbicacionAsegurados extends Model {

    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'int_ubicacion';

    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [
        'uuid_ubicacion', //TODO
        'empresa_id',
        'numero',
        'nombre',
        'direccion',
        'edif_mejoras',
        'contenido',
        'maquinaria',
        'inventario',
        'acreedor',
        'porcentaje_acreedor',
        'observaciones',
        'estado',
        'tipo_id',
        'acreedor_opcional'
    ];

    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id'];

    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("id_empresa", $empresa_id);
    }

    public function scopeDeEmpresaUbicacion($query, $empresa_id) {
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

    public function datosAcreedor() {
        return $this->hasOne(Proveedores::class, 'id', 'acreedor');
    }

    public static function listar_ubicacion_provicional($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
        if((isset($clause['acreedor'])) && ($clause['acreedor']!='otro' || $clause['acreedor']!='' )) {
        $ubicacion = self::select("int_intereses_asegurados.*","int_ubicacion.*","int_intereses_asegurados_detalles.*","pro_proveedores.nombre as acreedor")->join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_ubicacion.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->leftjoin("pro_proveedores","pro_proveedores.id","=","int_ubicacion.acreedor")->where("pro_proveedores.tipo_id","=", $clause['acreedor'])->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
        
          //$ubicacion = self::join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_ubicacion.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.interesestable_type", '7')->where(function($query) use($clause,$sidx,$sord,$limit,$start){  
            if((isset($clause['empresa_id'])) && (!empty($clause['empresa_id']))) $query->where('int_intereses_asegurados.empresa_id','=' , $clause['empresa_id']);
            if((isset($clause['detalle_unico'])) && (!empty($clause['detalle_unico']))) $query->where('int_intereses_asegurados_detalles.detalle_unico','=' , $clause['detalle_unico']);
            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
        
        if($sidx!=NULL && $sord!=NULL){ $ubicacion->orderBy($sidx, $sord); }
        return $ubicacion->get();

        } else {
            $ubicacion = self::select("int_intereses_asegurados.*","int_ubicacion.*","int_intereses_asegurados_detalles.*")->join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_ubicacion.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where(function($query) use($clause,$sidx,$sord,$limit,$start){
            
        
          //$ubicacion = self::join("int_intereses_asegurados", "int_intereses_asegurados.interesestable_id", "=", "int_ubicacion.id")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.interesestable_type", '7')->where(function($query) use($clause,$sidx,$sord,$limit,$start){  
            if((isset($clause['empresa_id'])) && (!empty($clause['empresa_id']))) $query->where('int_intereses_asegurados.empresa_id','=' , $clause['empresa_id']);
            if((isset($clause['detalle_unico'])) && (!empty($clause['detalle_unico']))) $query->where('int_intereses_asegurados_detalles.detalle_unico','=' , $clause['detalle_unico']);
            if($limit!=NULL) $query->skip($start)->take($limit);            
            });
        
        if($sidx!=NULL && $sord!=NULL){ $ubicacion->orderBy($sidx, $sord); }
        return $ubicacion->get();
        }
         
    }

}

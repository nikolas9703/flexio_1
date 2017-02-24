<?php
namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Solicitudes\Models\SolicitudesCoberturas;
use Flexio\Modulo\Solicitudes\Models\SolicitudesDeduccion;
use Flexio\Modulo\Solicitudes\Models\SolicitudesVigencia;
use Flexio\Modulo\Solicitudes\Models\SolicitudesPrima;
use Flexio\Modulo\Solicitudes\Models\SolicitudesParticipacion;
use Flexio\Modulo\Planes\Models\Planes;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;


class Solicitudes extends Model
{
    protected $table        = 'seg_solicitudes';    
    protected $fillable     = ['uuid_solicitudes', 'numero', 'cliente_id', 'aseguradora_id', 'ramo', 'id_tipo_poliza', 'usuario_id', 'estado', 'observaciones', 'updated_at', 'created_at', 'empresa_id', 'fecha_creacion','plan_id','comision','ramo_id','grupo','direccion','porcentaje_sobre_comision','impuesto', 'centro_contable'];
    protected $guarded      = ['id'];
    
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    
    public function cliente() {
    	return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }
    public function aseguradora() {
        return $this->hasOne(Aseguradoras::class, 'id', 'aseguradora_id');
    }
	
	public function interes() {
        return $this->hasOne(Aseguradoras::class, 'id', 'aseguradora_id');
    }
	
    public function tipo() {
        return $this->hasOne(CatalogoTipoPoliza::class, 'id', 'id_tipo_poliza');
    }
    public function usuario() {
        return $this->hasOne(Usuarios::class, 'id', 'usuario_id');
    }
	
	function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public function coberturas(){
        return $this->hasMany(SolicitudesCoberturas::class, 'id_solicitud', 'id');
    }
    public function deduccion(){
        return $this->hasMany(SolicitudesDeduccion::class, 'id_solicitud', 'id');
    }
    public function vigencia(){
        return $this->hasOne(SolicitudesVigencia::class, 'id_solicitudes', 'id');
    }
    public function prima(){
        return $this->hasOne(SolicitudesPrima::class, 'id_solicitudes', 'id');
    }
    public function participacion(){
        return $this->hasMany(SolicitudesParticipacion::class, 'id_solicitud', 'id');
    }
    public function plan(){
        return $this->hasOne(Planes::class, 'id', 'plan_id');
    }
	public function ramorelacion(){
        return $this->hasOne(Ramos::class, 'id', 'ramo_id');
    }
    public function datosEmpresa(){
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }

    public function centros(){
        return $this->belongsTo(CentrosContables::class, 'centro_contable', 'id');
    }

    public function guardarcopiadocumentos($campos){
        
        foreach ($campos as $value) {
            $clause = array();
            $clause = array("empresa_id" => $value['empresa_id'], "archivo_ruta" => $value['archivo_ruta'], "archivo_nombre" => $value['archivo_nombre'], "extra_datos" => $value['extra_datos'], "subido_por" => $value['subido_por'], "documentable_id" => $value['documentable_id'], "documentable_type" => $value['documentable_type'], "nombre_documento" => $value['nombre_documento'], "centro_contable_id" => "0", "tipo_id" => "0", "etapa" => "por_enviar", "padre_id" => "0", "archivado" => "0" );
            $clause["uuid_documento"] = Capsule::raw("ORDER_UUID(uuid())");
            $query = Documentos::create($clause);
        }

        return $query;
        //return $this->hasMany(Documentos::class,'padre_id','id');
    }
}   
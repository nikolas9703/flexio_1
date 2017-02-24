<?php
namespace Flexio\Modulo\Reclamos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Reclamos\Models\ReclamosCoberturas;
use Flexio\Modulo\Reclamos\Models\ReclamosDeduccion;
use Flexio\Modulo\Reclamos\Models\ReclamosArticulo;
use Flexio\Modulo\Reclamos\Models\ReclamosCarga;
use Flexio\Modulo\Reclamos\Models\ReclamosAereo;
use Flexio\Modulo\Reclamos\Models\ReclamosMaritimo;
use Flexio\Modulo\Reclamos\Models\ReclamosPersonas;
use Flexio\Modulo\Reclamos\Models\ReclamosProyecto;
use Flexio\Modulo\Reclamos\Models\ReclamosUbicacion;
use Flexio\Modulo\Reclamos\Models\ReclamosVehiculo;
use Flexio\Modulo\Planes\Models\Planes;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Ajustadores\Models\Ajustadores;
use Flexio\Modulo\Ajustadores\Models\AjustadoresContacto;


class Reclamos extends Model
{
    protected $table        = 'rec_reclamos';    
    protected $fillable     = ['uuid_reclamos', 'numero','id_poliza', 'fecha', 'numero_caso', 'fecha_siniestro', 'fecha_notificacion', 'id_cliente', 'telefono', 'celular', 'updated_at', 'created_at', 'correo', 'no_certificado', 'id_interes_asegurado', 'reclamante', 'estado', 'empresa_id', 'id_ramo', 'id_usuario', 'causa', 'ajustador', 'contacto', 'telefonodetalle', 'descripcionsiniestro', 'total_reclamar', 'pago_asegurado', 'pago_deducible', 'gastos_no_cubiertos', 'numero_cheque', 'fecha_cheque', 'fecha_juicio', 'taller', 'tipo_interes'];
    protected $guarded      = ['id'];
    
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }

    public function poliza() {
        return $this->hasOne(Polizas::class, 'id', 'id_poliza');
    }
    public function coberturas(){
        return $this->hasMany(ReclamosCoberturas::class, 'id_reclamo', 'id');
    }
    public function deduccion(){
        return $this->hasMany(ReclamosDeduccion::class, 'id_reclamo', 'id');
    }    
    public function cliente() {
    	return $this->hasOne(Cliente::class, 'id', 'id_cliente');
    }
    public function usuarios() {
        return $this->hasOne(Usuarios::class, 'id', 'id_usuario');
    }
    public function ajustadores() {
        return $this->hasOne(Ajustadores::class, 'id', 'ajustador');
    }
    public function contactos() {
        return $this->hasOne(AjustadoresContacto::class, 'id', 'contacto');
    }    

    public function articulofk() {
        return $this->hasOne(ReclamosArticulo::class, 'id_reclamo', 'id');
    }
    public function cargafk() {
        return $this->hasOne(ReclamosCarga::class, 'id_reclamo', 'id');
    }
    public function aereofk() {
        return $this->hasOne(ReclamosAereo::class, 'id_reclamo', 'id');
    }
    public function maritimofk() {
        return $this->hasOne(ReclamosMaritimo::class, 'id_reclamo', 'id');
    }
    public function personasfk() {
        return $this->hasOne(ReclamosPersonas::class, 'id_reclamo', 'id');
    }
    public function proyectofk() {
        return $this->hasOne(ReclamosProyecto::class, 'id_reclamo', 'id');
    }
    public function ubicacionfk() {
        return $this->hasOne(ReclamosUbicacion::class, 'id_reclamo', 'id');
    }
    public function vehiculofk() {
        return $this->hasOne(ReclamosVehiculo::class, 'id_reclamo', 'id');
    }
	
	function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }

    public static function findByUuid($uuid) {
        return self::where('uuid_reclamos',hex2bin($uuid))->first();
    }

    
    
    public function datosEmpresa(){
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }

    
}
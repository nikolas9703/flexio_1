<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Documentos\Models\Documentos;

class VehiculoAsegurados extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'int_vehiculo';
    
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [
        'uuid_vehiculo', //TODO
        'empresa_id',
        'chasis',
        'unidad',
        'uso',
        'condicion',
        'acreedor',
        'marca',
        'modelo',
        'placa',
        'ano',
        'motor',
        'color',
        'capacidad',
        'estado',
        'operador',
        'extras',
        'valor_extras',
        'porcentaje_acreedor',
        'observaciones',
        'numero',
        'tipo_id'
    ];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];
    
    public static function findByUuid($uuid) {
        return self::where('uuid_intereses_asegurados',hex2bin($uuid))->first();
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
    
    public function scopeDeEmpresaVehiculo($query, $empresa_id) {
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


}
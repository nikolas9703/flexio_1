<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas;
use Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Modulos\Models\Catalogos;
use Illuminate\Database\Eloquent\Model as Model;

class InteresesAsegurados extends Model 
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'int_intereses_asegurados';
    
    
    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;
    
    
    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    //protected $dateFormat = 'U';
    
    
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = [
        'empresa_id', //TODO
        'interesestable_type',
        'interesestable_id',
        'uuid_intereses',
        'numero',
        'identificacion',
        'estado'
    ];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];
    
    function documentos() {
    	return $this->morphMany(Documentos::class, 'documentable');
    }
    public function articulo() {
            return $this->belongsTo(ArticuloAsegurados::class, 'interesestable_id');
    }
    public function ubicacion() {
            return $this->belongsTo(UbicacionAsegurados::class, 'interesestable_id');
    }
    public function persona() {
            return $this->belongsTo(InteresesPersonas::class, 'interesestable_id');
    }
    public function casco_aereo() {
            return $this->belongsTo(AereoAsegurados::class, 'interesestable_id');
    }
    public function carga() {
            return $this->belongsTo(CargaAsegurados::class, 'interesestable_id');
    }
    public function casco_maritimo() {
            return $this->belongsTo(MaritimoAsegurados::class, 'interesestable_id');
    }
    public function proyecto_actividad() {
            return $this->belongsTo(ProyectoAsegurados::class, 'interesestable_id');
    }
    public function vehiculo() {
            return $this->belongsTo(VehiculoAsegurados::class, 'interesestable_id');
    }
    public static function findByUuid($uuid) {
        return self::where('uuid_intereses',hex2bin($uuid))->first();
    }
    public static function findByInteresestable($interesestable_id, $tipo_id) {
        return self::where(array('interesestable_id' => $interesestable_id, 'interesestable_type' => $tipo_id))->first();
    }

    //transformaciones para GET
    public function getUuidInteresAttribute($value) {
        return strtoupper(bin2hex($value));
    }
    
    //transformaciones para SET
    
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    public function scopeDeUuid($query, $uuid) {        
        return $query->whereIn("id", $uuid);
    } 
    
    public function scopeConTipo($query) {
        return $query->has("tipo");
    }
    
    public function scopeDeNumero($query, $numero) {      
        return $query->where("numero", "like", "%$numero%");
    } 
    
    public function scopeDeTipo($query, $tipo) {       
        return $query->where("interesestable_type", $tipo);
    }
    public function scopeDeEstado($query, $estado) {       
        return $query->where("estado", $estado);
    }
    
    public function scopeDeIdentificacion($query, $identificacion) {
        return $query->where("identificacion", "like", "%$identificacion%");
    }
    
    public function tipo() {
        return $this->belongsTo(InteresesAsegurados_cat::class, "interesestable_type", "id_cat");
    }
    public function estado_catalogo() {
        return $this->belongsTo(Catalogos::class, "estado", "id_cat");
    }

}
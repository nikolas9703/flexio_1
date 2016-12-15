<?php 
namespace Flexio\Modulo\InteresesAsegurados\Models;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Illuminate\Database\Eloquent\Model as Model;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Documentos\Models\Documentos;

class ProyectoAsegurados extends Model
{
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
        'tipo_id',
        'estado',
        'tipo_propuesta',
        'tipo_fianza',
        'validez_fianza'
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
    
    public function scopeDeEmpresaProyecto($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    
    public function scopeConTipo($query) {
        return $query->has("tipo");
    }
    
    /*public function getFechaAttribute($date) {
      return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    }*/
    
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
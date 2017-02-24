<?php
namespace Flexio\Modulo\SubContratos\Models;

use Illuminate\Database\Eloquent\Model            as Model;
use Illuminate\Database\Capsule\Manager           as Capsule;
use Flexio\Modulo\SubContratos\Models\AdendaMonto as AdendaMonto;
use Flexio\Modulo\SubContratos\Models\SubContrato as SubContrato;
use Carbon\Carbon                                 as Carbon;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Adenda extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = [
        'codigo',
        'proveedor_id',
        'empresa_id',
        'fecha',
        'subcontrato_id',
        'referencia',
        'usuario_id',
        'monto_adenda',
        'comentario',
        'monto_acumulado'
    ];

    /**
     * Tabla del modelo
     * @var string
     */
    protected $table = 'sub_adendas_subcontratos';

    /**
     * Campos a llenar en la tabla
     * @var array
     */
    protected $fillable = [
        'codigo',
        'proveedor_id',
        'empresa_id',
        'fecha',
        'subcontrato_id',
        'referencia',
        'usuario_id',
        'monto_adenda',
        'comentario',
        'monto_acumulado'
    ];

    protected $casts = [
        'monto_adenda' => 'float',
        'monto_acumulado' => 'float'
    ];

    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded = ['id', 'uuid_adenda'];
    protected $appends =['icono','enlace'];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_adenda' => Capsule::raw("ORDER_UUID(uuid())")
            )), true);
        parent::__construct($attributes);
    }
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    public function getUuidAdendaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getFechaAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
    }
    
    public function setFechaAttribute($date){
  		return  $this->attributes['fecha'] = Carbon::createFromFormat('d/m/Y', $date, 'America/Panama');
    }

    public function adenda_montos()
    {
        return $this->hasMany(AdendaMonto::class);
    }

    public function usuario()
    {
        return $this->belongsTo("Usuario_orm", 'usuario_id');
    }

    public function subcontrato()
    {
        return $this->belongsTo(SubContrato::class, 'subcontrato_id');
    }

    public function monto_subcontrato()
    {
        return  $this->subcontrato()->sum('monto_subcontrato');
    }

    public function comentario(){
        return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario','comentable');
    }

    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeUuid($query, $uuid_adenda)
    {
        return $query->where("uuid_adenda", hex2bin($uuid_adenda));
    }

    public function scopeDeCodigo($query, $codigo)
    {
        return $query->where("codigo", $codigo);
    }

    //functiones para el landing_page
   public function getIconoAttribute(){
     return 'fa fa-shopping-cart';
   }
   public function landing_comments(){
      return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario','comentable');
    }

    public function getEnlaceAttribute()
    {
      return base_url("subcontratos/editar_adenda/".$this->uuid_adenda);
    }
}

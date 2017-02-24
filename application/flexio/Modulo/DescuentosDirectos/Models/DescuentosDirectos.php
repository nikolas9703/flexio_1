<?php
namespace Flexio\Modulo\DescuentosDirectos\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Modulos\Models\Catalogos as Catalogos;
use Flexio\Modulo\Acreedores\Models\Acreedores;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDescuentos;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class DescuentosDirectos extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['colaborador_id', 'empresa_id', 'plan_contable_id', 'tipo_descuento_id', 'acreedor_id', 'ciclo_id', 'monto_adeudado', 'monto_ciclo', 'porcentaje_capacidad', 'descuento_diciembre', 'carta_descuento', 'fecha_inicio', 'detalle', 'archivo_ruta', 'archivo_nombre', 'estado_id', 'creado_por', 'inicial', 'anio', 'secuencial', 'uuid_descuento', 'no_referencia', 'codigo'];

    protected $table        = 'desc_descuentos';
    protected $fillable = ['colaborador_id', 'empresa_id', 'plan_contable_id', 'tipo_descuento_id', 'acreedor_id', 'ciclo_id','monto_inicial', 'monto_adeudado', 'monto_ciclo', 'porcentaje_capacidad', 'descuento_diciembre', 'carta_descuento', 'fecha_inicio', 'detalle', 'archivo_ruta', 'archivo_nombre', 'estado_id', 'creado_por', 'inicial', 'anio', 'secuencial', 'uuid_descuento', 'no_referencia','codigo'];
    protected $appends      = ['icono','enlace','suma_descuento_pendiente'];
    protected $guarded      = ['id'];
    public $timestamps      = false;

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    public function ciclo(){
        return $this->belongsTo(Catalogos::class, 'ciclo_id', 'id_cat');
    }
    public function acreedor(){

     	return $this->hasOne(Acreedores::Class, 'id', 'acreedor_id');

    }
    public function getUuidDescuentoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function descuentos_pendientes() {
         return $this->hasMany(PagadasDescuentos::Class, 'descuento_id', 'id')->where("estado_pago_proveedor",'pendiente');
    }

    public function getSumaDescuentoPendienteAttribute() {
            return  $this->descuentos_pendientes()->sum('monto_ciclo');
    }


    public function descuentos_asignados() {
        return $this->hasMany(Asignados::class,'id');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("descuentos/ver/".$this->uuid_descuento);
    }
    public function getIconoAttribute(){
        return 'fa fa-institution';
    }
    /*public function getCodigoAttribute(){
        dd($this->toArray());
        return $this->codigo;
    }*/
}
